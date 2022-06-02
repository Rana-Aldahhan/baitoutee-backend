<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class CommonAuthController extends Controller
{
    public function sendPhoneNumberVerificationCode(Request $request){
        /**
         * send a request to an OTP provider API endpoint to send a verification code 
         * to the provided phone number
         */
        $validator = Validator::make($request->all(), [
            'phone_number' => 'required'
        ]);
        if($validator->fails())//case of input validation failure
        {
            return $this->errorResponse($validator->errors()->first(),422);
        }
        $phoneNumber=$request['phone_number'];
        //store in the database that this phone number has been sent a verification code
        DB::table('phone_number_verifications')->updateOrInsert(
            ['phone_number'=>$phoneNumber],
            ['phone_number'=>$phoneNumber,'code_sent_at'=>now(),'verified_at'=>null]
        );
        return $this->successResponse(['message'=>'code sent to phone number successfully!']);
    }
}
