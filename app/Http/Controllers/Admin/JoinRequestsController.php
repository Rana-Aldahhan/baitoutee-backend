<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\UserJoinRequest;
use App\Models\User;
use Illuminate\Http\Request;

class JoinRequestsController extends Controller
{
    public function approveUser(Request $request,$id)
    {
        $joinRequest=UserJoinRequest::find($id);
        //create new user entity
        $user=User::create([
            'phone_number' => $joinRequest->phone_number,
            'name' => $joinRequest->name,
            'email' => $joinRequest->email,
            'birth_date'=>$joinRequest->birth_date,
            'gender'=> $joinRequest->gender,
            'national_id'=>$joinRequest->national_id,
            'campus_card_id'=>$joinRequest->campus_card_id,
            'campus_unit_number' => $joinRequest->campus_unit_number,
            'campus_card_expiry_date' =>$joinRequest->campus_card_expiry_date,
            'study_specialty'=>$joinRequest->study_specialty,
            'study_year'=>$joinRequest->study_year,
            'location_id'=>$joinRequest->location_id,
            'approved_at'=>now(),
        ]);
        //TODO send notification to that user

        // make the request entity approved
        $joinRequest->approved=true;
        $joinRequest->user_id=$user->id;
        $joinRequest->save();
        return redirect('/admin/user-join-request');
    }
}
