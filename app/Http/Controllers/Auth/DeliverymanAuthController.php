<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\DeliverymanJoinRequest;
use App\Models\Deliveryman;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;


enum DeliverymanAccessStatus : int {
    case approved=0;
    case notApproved=1;
    case notRegistered=2;
    case notVerified=3;
    case blocked=4;
}
enum TransportationType : int {
    case bicycle=0;
    case electricBicycle=1;
    case motorcycle=2;
    case car=3;
}
class DeliverymanAuthController extends Controller
{
    public function checkDeliverymanCodeAndRegisterStatus(Request $request){
        $validator = Validator::make($request->all(), [
            'phone' => 'required',
            'code' =>'required'
        ]);
        if($validator->fails())//case of input validation failure
        {
            return $this->errorResponse($validator->errors()->first(),422);
        }
        //query parameters
        $verificationCode=$request['code'];
        $phoneNumber=$request['phone'];
        //response data
        $code_is_valid=false;
        $registered=false;
        $approved=false;
        $is_blocked=false;
        //verification code validation
         /** 
         * TODO :send a request to the OTP provider API endpoint to check if the verification code 
         * provided by the user is valid
         * @returns boolean
         */ 
        if($verificationCode!='0000')//if the respone returned invalid code
        {
            //return $this->errorResponse('الرمز الذي أدخلته غير صالح',401);
            return $this->errorResponseWithCustomizedStatus(DeliverymanAccessStatus::notVerified->value,'الرمز الذي أدخلته غير صالح',401);
        }
        else // the response retuned it is a valid code:check if the user has registedred before 
        {
            //store in the database that this phone number has been verified
             DB::table('phone_number_verifications')
              ->updateOrInsert(['phone_number'=> $phoneNumber],['verified_at' => now()]);
            $code_is_valid=true;
            $joinRequest=DeliverymanJoinRequest::where('phone_number',$phoneNumber)->orderBy('created_at', 'desc')->first();;
            $joinRequest!=null?$registered=true:$registered=false;
            if(!$registered) // case the user has not registered yet
            {
                return $this->successResponseWithCustomizedStatus(DeliverymanAccessStatus::notRegistered->value,[]);
            }
            else // case the user has registered :check if the registration request has been approved
            {
                $approved=$joinRequest->approved;
                if(!$approved) //case not approved
                {
                    return $this->successResponseWithCustomizedStatus(DeliverymanAccessStatus::notApproved->value,[]);
                }else //case approved :check if blocked or inactive account
                {
                    $deliveryman=$joinRequest->deliveryman->withTrashed()->first();
                    $deliveryman->deleted_at !=null?$isblocked=true:$is_blocked=false;
                    if($is_blocked)//case is blocked
                    {
                        return $this->errorResponseWithCustomizedStatus(DeliverymanAccessStatus::blocked->value,'حسابك تم حجبه, لا يمكنك الدخول',403);
                    }
                    else//case not blocked
                    {
                        $loggedDeliveryman=$this->login($deliveryman);
                        return $this->successResponseWithCustomizedStatus(DeliverymanAccessStatus::approved->value,$loggedDeliveryman);
                    }
            }
         } 
        }
    }
    public function makeRegisterRequest(Request $request) {
        $validator = Validator::make($request->all(), [
            'phone_number' => 'required|unique:deliveryman_join_requests,phone_number',
            'name' => 'required',
            'email' => 'required|email|unique:deliveryman_join_requests,email',
            'birth_date'=>'required|date',
            'gender'=> ['required', Rule::in(['f', 'm'])],
            'transportation_type'=>['required', Rule::in([0,1,2,3])],
            'work_days'=>'required',
            'work_hours_from'=>'required|date_format:H:i:s',
            'work_hours_to'=>'required|date_format:H:i:s',
        ]);
        if($validator->fails())//case of input validation failure
        {
            return $this->errorResponse($validator->errors()->first(),422);
        }
        $joinRequest=DeliverymanJoinRequest::create([
            'phone_number' => $request['phone_number'],
            'name' => $request['name'],
            'email' => $request['email'],
            'birth_date'=>$request['birth_date'],
            'gender'=> $request['gender'],
            'transportation_type'=>TransportationType::from($request['transportation_type'])->name,
            'work_days'=>$request['work_days'],
            'work_hours_from'=>$request['work_hours_from'],
            'work_hours_to'=>$request['work_hours_to'],
        ]);
        return $this->successResponse([],201);

    }
    public function login(Deliveryman $deliveryman){
        $access_token= $deliveryman->createToken('app',['deliveryman'])->plainTextToken;
        $deliveryman->append('access_token');
        $deliveryman->access_token=$access_token;
        return $deliveryman;
    }
    public function logout(){
        auth('deliveryman')->user()->tokens()->delete();
        return $this->successResponse([],200);
    }
}
