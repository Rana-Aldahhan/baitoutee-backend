<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Location;
use App\Models\ChefJoinRequest;
use App\Models\Chef;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use App\Traits\DistanceCalculator;

enum ChefAccessStatus : int {
    case approved=0;
    case notApproved=1;
    case notRegistered=2;
    case notVerified=3;
    case blocked=4;
}
enum Gender:int {
    case m=0;
    case f=1;
}

class ChefAuthController extends Controller
{
    use DistanceCalculator;

    public function checkChefCodeAndRegisterStatus(Request $request){
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
        //verification code validation
         /** 
         * TODO :send a request to the OTP provider API endpoint to check if the verification code 
         * provided by the user is valid
         * @returns boolean
         */ 
        if($verificationCode!='0000')//if the respone returned invalid code
        {
            //return $this->errorResponse('الرمز الذي أدخلته غير صالح',401);
            return $this->errorResponseWithCustomizedStatus(ChefAccessStatus::notVerified->value,'الرمز الذي أدخلته غير صالح',401);
        }
        else // the response retuned it is a valid code:check if the user has registedred before 
        {
            //store in the database that this phone number has been verified
             DB::table('phone_number_verifications')
              ->updateOrInsert(['phone_number'=> $phoneNumber],['verified_at' => now()]);
            $code_is_valid=true;
            $joinRequest=ChefJoinRequest::where('phone_number',$phoneNumber)->orderBy('created_at', 'desc')->first();;
            $joinRequest!=null?$registered=true:$registered=false;
            if(!$registered) // case the user has not registered yet
            {
                // return $this->successResponse(['code_is_valid'=>$code_is_valid,'registered'=>$registered]);
                return $this->successResponseWithCustomizedStatus(ChefAccessStatus::notRegistered->value,[]);
            }
            else // case the user has registered :check if the registration request has been approved
            {
                $approved=$joinRequest->approved;
                if(!$approved) //case not approved
                {
                    // return $this->successResponse(['code_is_valid'=>$code_is_valid,'registered'=>$registered,'approved'=>$approved]);
                    return $this->successResponseWithCustomizedStatus(ChefAccessStatus::notApproved->value,[]);
                }else //case approved :check if blocked or inactive account
                {
                    $chef=$joinRequest->chef()->withTrashed()->first();
                    $chef->deleted_at !=null?$isblocked=true:$is_blocked=false;
                    if($is_blocked)//case is blocked
                    {
                        // return $this->errorResponse('حسابك تم حجبه, لا يمكنك الدخول',403);
                        return $this->errorResponseWithCustomizedStatus(ChefAccessStatus::blocked->value,'حسابك تم حجبه, لا يمكنك الدخول',403);
                    }
                    else//case not blocked
                    {
                        $loggedChef=$this->login($chef);
                        return $this->successResponseWithCustomizedStatus(ChefAccessStatus::approved->value,$loggedChef);
                    }
            }
         } 
        }
    }
    public function makeRegisterRequest(Request $request) {
        $validator = Validator::make($request->all(), [
            'phone_number' => 'required|unique:chef_join_requests,phone_number',
            'name' => 'required',
            'email' => 'required|email|unique:chef_join_requests,email',
            'birth_date'=>'required|date',
            'gender'=> ['required', Rule::in([0,1])],
            'location'=>'required',
            'latitude'=>'required|numeric',
            'longitude'=>'required|numeric',
            //TODO add check 
            'delivery_starts_at' =>'required|date_format:H:i:s',
            'delivery_ends_at' =>'required|date_format:H:i:s',
            'max_meals_per_day'=>'required|numeric',
            'profile_picture'=>'nullable|image',
        ]);
        if($validator->fails())//case of input validation failure
        {
            return $this->errorResponse($validator->errors()->first(),422);
        }
        //store the new location
        $newLocation=Location::create([
            'name'=>$request['location'],
            'latitude'=>$request['latitude'],
            'longitude'=>$request['longitude'],
        ]);
        //calculate then store the distance between the new locations and each of the three campus locations
        $firstLocation=Location::find(1);
        $secondLocation=Location::find(2);
        $thirdLocation=Location::find(3);
        $newLocation->distance_to_first_location=$this->calculateDistanceBetween($newLocation,$firstLocation);
        $newLocation->distance_to_second_location=$this->calculateDistanceBetween($newLocation,$secondLocation);
        $newLocation->distance_to_third_location=$this->calculateDistanceBetween($newLocation,$thirdLocation);
        $newLocation->save();
         //store profile if given
        $profilePath=null;
        if($request->hasFile('profile_picture')){
            // Get filename with the extension
            $filenameWithExt = $request->file('profile_picture')->getClientOriginalName();
            // Get just filename
            $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
            // Get just ext
            $extension = $request->file('profile_picture')->getClientOriginalExtension();
            // Filename to store
            $fileNameToStore= $filename.'_'.time().'.'.$extension;
            // Upload Image
            $profilePath = $request->file('profile_picture')->storeAs('public/profiles', $fileNameToStore);
            //$profilePath=asset('storage/profiles/'.$fileNameToStore);
        }
        //store certificate if given
         $certificatePath=null;
         if($request->hasFile('certificate')){
             // Get filename with the extension
             $filenameWithExt = $request->file('certificate')->getClientOriginalName();
             // Get just filename
             $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
             // Get just ext
             $extension = $request->file('certificate')->getClientOriginalExtension();
             // Filename to store
             $fileNameToStore= $filename.'_'.time().'.'.$extension;
             // Upload Image
             $certificatePath = $request->file('certificate')->storeAs('public/certificates', $fileNameToStore);
             //$certificatePath=asset('storage/certificatess/'.$fileNameToStore);
         }
         is_null($profilePath)?$profilePath='storage/profiles/default_profile_pic.jpg':$profilePath=$profilePath;
        //make new chef join request
        $joinRequest=ChefJoinRequest::create([
            'phone_number' => $request['phone_number'],
            'name' => $request['name'],
            'email' => $request['email'],
            'birth_date'=>$request['birth_date'],
            'gender'=> Gender::from($request['gender'])->name,
            'delivery_starts_at' =>$request['delivery_starts_at'],
            'delivery_ends_at' =>$request['delivery_ends_at'],
            'max_meals_per_day'=>$request['max_meals_per_day'],
            'profile_picture'=>$profilePath,
            'certificate'=>$certificatePath,
            'location_id'=>$newLocation->id
        ]);
        return $this->successResponse([],201);

    }
    public function login(Chef $chef){
        $access_token= $chef->createToken('app',['chef'])->plainTextToken;
        $chef->append('access_token');
        $chef->access_token=$access_token;
        return $chef;
    }
    public function logout(){
        auth('chef')->user()->tokens()->delete();
        return $this->successResponse([],200);
    }
 
}
