<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\UserJoinRequest;
use App\Models\ChefJoinRequest;
use App\Models\DeliverymanJoinRequest;
use App\Models\User;
use App\Models\Chef;
use App\Models\Deliveryman;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class JoinRequestsController extends Controller
{

    public function approveUser(Request $request,$id)
    {
        
        \Auth::shouldUse('backpack');
        Gate::authorize('approve-reject-join-requests');
        $joinRequest=UserJoinRequest::find($id);
        //case the join request is already rejected or approved
        if($joinRequest->approved===false || $joinRequest->approved===true)
            return  redirect('/admin/user-join-request');
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
    public function approveChef(Request $request,$id)
    {
        \Auth::shouldUse('backpack');
        Gate::authorize('approve-reject-join-requests');
        $joinRequest=ChefJoinRequest::find($id);
        //case the join request is already rejected or approved
        if($joinRequest->approved===false || $joinRequest->approved===true)
            return  redirect('/admin/chef-join-request');
        //create new user entity
        $user=Chef::create([
            'phone_number' => $joinRequest->phone_number,
            'name' => $joinRequest->name,
            'email' => $joinRequest->email,
            'birth_date'=>$joinRequest->birth_date,
            'gender'=> $joinRequest->gender,
            'location_id'=>$joinRequest->location_id,
            'delivery_starts_at'=>$joinRequest->delivery_starts_at,
            'delivery_ends_at'=>$joinRequest->delivery_ends_at,
            'max_meals_per_day'=>$joinRequest->max_meals_per_day,
            'profile_picture'=>$joinRequest->profile_picture,
            'certificate'=>$joinRequest->certificate,
            'chef_join_request_id'=>$joinRequest->id,
            'approved_at'=>now(),
        ]);
        //TODO send notification to that chef

        // make the request entity approved
        $joinRequest->approved=true;
        $joinRequest->save();
        return redirect('/admin/chef-join-request');
    }
    public function approveDeliveryman(Request $request,$id)
    {
        \Auth::shouldUse('backpack');
        Gate::authorize('approve-reject-join-requests');
        $joinRequest=DeliverymanJoinRequest::find($id);
        //case the join request is already rejected or approved
        if($joinRequest->approved===false || $joinRequest->approved===true)
            return  redirect('/admin/chef-join-request');
        //create new user entity
        $user=Deliveryman::create([
            'phone_number' => $joinRequest->phone_number,
            'name' => $joinRequest->name,
            'email' => $joinRequest->email,
            'birth_date'=>$joinRequest->birth_date,
            'gender'=> $joinRequest->gender,
            'transportation_type'=>$joinRequest->transportation_type,
            'work_days'=>$joinRequest->work_days,
            'work_hours_from'=>$joinRequest->work_hours_from,
            'work_hours_to'=>$joinRequest->work_hours_to,
            'deliveryman_join_request_id'=>$joinRequest->id,
            'approved_at'=>now(),
        ]);
        //TODO send notification to that chef

        // make the request entity approved
        $joinRequest->approved=true;
        $joinRequest->save();
        return redirect('/admin/deliveryman-join-request');
    }
    public function rejectUser(Request $request,$id)
    {
        \Auth::shouldUse('backpack');
        Gate::authorize('approve-reject-join-requests');
        $joinRequest=UserJoinRequest::find($id);
        //case the join request is already rejected or approved
        if($joinRequest->approved===false || $joinRequest->approved===true)
            return  redirect('/admin/user-join-request');
        //TODO send notification to that user

        // make the request entity rejected
        $joinRequest->approved=false;
        $joinRequest->save();
        return redirect('/admin/user-join-request');
    }
    public function rejectChef(Request $request,$id)
    {
        \Auth::shouldUse('backpack');
        Gate::authorize('approve-reject-join-requests');
        $joinRequest=ChefJoinRequest::find($id);
        //case the join request is already rejected or approved
        if($joinRequest->approved===false || $joinRequest->approved===true)
            return  redirect('/admin/chef-join-request');
       
        //TODO send notification to that chef

        // make the request entity rejected
        $joinRequest->approved=false;
        $joinRequest->save();
        return redirect('/admin/chef-join-request');
    }
    public function rejectDeliveryman(Request $request,$id)
    {
        \Auth::shouldUse('backpack');
        Gate::authorize('approve-reject-join-requests');
        $joinRequest=DeliverymanJoinRequest::find($id);
        //case the join request is already rejected or approved
        if($joinRequest->approved===false || $joinRequest->approved===true)
            return  redirect('/admin/deliveryman-join-request');
        //TODO send notification to that chef

        // make the request entity approved
        $joinRequest->approved=false;
        $joinRequest->save();
        return redirect('/admin/deliveryman-join-request');
    }
    
}
