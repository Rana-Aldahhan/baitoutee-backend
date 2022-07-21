<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\UserJoinRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use App\Enums\UserAccessStatus;
use App\Enums\Gender;

class UserAuthController extends Controller
{

    public function checkUserCodeAndRegisterStatus(Request $request){
        $validator = Validator::make($request->all(), [
            'phone_number' => 'required',
            'code' =>'required'
        ]);
        if($validator->fails())//case of input validation failure
        {
            return $this->errorResponse($validator->errors()->first(),422);
        }
        //query parameters
        $verificationCode=$request['code'];
        $phoneNumber=$request['phone_number'];
        //response data
        $code_is_valid=false;
        $registered=false;
        $approved=false;
        $is_blocked=false;
        $is_active=true;
        //verification code validation
         /** 
         * TODO :send a request to the OTP provider API endpoint to check if the verification code 
         * provided by the user is valid
         * @returns boolean
         */ 
        if($verificationCode!='0000')//if the respone returned invalid code
        {
            return $this->errorResponseWithCustomizedStatus(UserAccessStatus::notVerified->value,'الرمز الذي أدخلته غير صالح',401);
        }
        else // the response retuned it is a valid code:check if the user has registedred before 
        {
            //store in the database that this phone number has been verified
             DB::table('phone_number_verifications')
              ->updateOrInsert(['phone_number'=> $phoneNumber],['verified_at' => now()]);
            $code_is_valid=true;
            $joinRequest=UserJoinRequest::where('phone_number',$phoneNumber)->orderBy('created_at', 'desc')->first();;
            $joinRequest!=null?$registered=true:$registered=false;
            if(!$registered) // case the user has not registered yet
            {
                    return $this->successResponseWithCustomizedStatus(UserAccessStatus::notRegistered->value,[]);
            }
            else // case the user has registered :check if the registration request has been approved
            {
                $approved=$joinRequest->approved;
                if($approved===0)//case the user request is rejected
                // return $this->successResponseWithCustomizedStatus(UserAccessStatus::rejected->value,[]);
                    return $this->errorResponseWithCustomizedStatus(UserAccessStatus::rejected->value,'تم رفض طلب الانضمام الخاص بك لا يمكنك الدخول',403);
                else if($approved===null) //case not approved
                {
                    return $this->successResponseWithCustomizedStatus(UserAccessStatus::notApproved->value,[]);
                }else //case approved :check if blocked or inactive account
                {
                    $user=$joinRequest->user()->withTrashed()->first();
                    $user->deleted_at !=null?$is_blocked=true:$is_blocked=false;
                    if($is_blocked)//case is blocked
                    {
                        return $this->errorResponseWithCustomizedStatus(UserAccessStatus::blocked->value,'حسابك تم حجبه, لا يمكنك الدخول',403);
                    }
                    else//case not blocked:check if account is active
                    {
                        now()>$user->campus_card_expiry_date?$is_active=false:$is_active=true;
                        if(!$is_active)//case the account is not active
                        {
                            return $this->errorResponseWithCustomizedStatus(UserAccessStatus::inactive->value,'انتهت مدة صلاحية حسابك السابق الرجاء إعادة تفعيله',403);
                        }
                        else//case the account is active:log in
                        {
                            //save fcm token
                            $user->fcm_token=$request->fcm_token;
                            $user->save();
                            //create access token and send it with user model
                            $loggedUser=$this->login($user);
                            return $this->successResponseWithCustomizedStatus(UserAccessStatus::active->value,$loggedUser);
                        }
                    } 
            }
         } 
        }
    }
    public function makeRegisterRequest(Request $request) {
        $validator = Validator::make($request->all(), [
            'phone_number' => 'required',
            'name' => 'required',
            'email' => 'required|email',
            'birth_date'=>'required|date',
            'gender'=> ['required', Rule::in([0,1])],
            'national_id'=>'required|numeric',
            'campus_card_id'=>'required',
            'campus_unit_number' => 'required|numeric',
            'campus_card_expiry_date' => 'required|date|after:tomorrow',
            'study_specialty'=>'required',
            'study_year'=>'required',
            'location'=> ['required', Rule::in([0,1,2])],
        ]);
        if($validator->fails())//case of input validation failure
        {
            return $this->errorResponse($validator->errors()->first(),422);
        }
        $joinRequest=UserJoinRequest::create([
            'phone_number' => $request['phone_number'],
            'name' => $request['name'],
            'email' => $request['email'],
            'birth_date'=>date_format(date_create($request['birth_date']),'Y-m-d'),
            'gender'=> Gender::from($request['gender'])->name,
            'national_id'=>$request['national_id'],
            'campus_card_id'=>$request['campus_card_id'],
            'campus_unit_number' => $request['campus_unit_number'],
            'campus_card_expiry_date' =>date_format(date_create($request['campus_card_expiry_date']),'Y-m-d') ,
            'study_specialty'=>$request['study_specialty'],
            'study_year'=>$request['study_year'],
            /**
             * locations are agreed upon:
             * 0 means Mazzah campus , it is the first record in DB
             * 1 means Hamak campus , it is the second record in DB
             * 2 means Barzeh campus , it is the third record in DB
             * we get the agreed upon location code and add 1 to it to store the foreign key
             */
            'location_id'=>$request['location']+1,
            'fcm_token'=>$request['fcm_token']
        ]);
        return $this->successResponse([],201);

    }
    public function login(User $user){
        $access_token= $user->createToken('app',['user'])->plainTextToken;
        $user->access_token=$access_token;
        return $user;
    }
    public function logout(){
        $user=auth('user')->user()->tokens()->delete();
        $user->fcm_token=null;
        $user->save();
        return $this->successResponse([],200);
    }
}
