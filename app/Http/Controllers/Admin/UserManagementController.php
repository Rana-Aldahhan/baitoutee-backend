<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Chef;
use App\Models\Deliveryman;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Gate; 

class UserManagementController extends Controller
{
    public function blockUser($id)
    {
        \Auth::shouldUse('backpack');
        Gate::authorize('block-users');
        $user=User::withTrashed()->find($id);
        $user->delete();

        return redirect('admin/user');
    }

    public function unblockUser($id)
    {
        \Auth::shouldUse('backpack');
        Gate::authorize('block-users');
        $user=User::withTrashed()->find($id);
        $user->restore();

        return redirect('admin/user');
    }
    public function blockChef($id)
    {
        \Auth::shouldUse('backpack');
        Gate::authorize('block-users');
        $user=Chef::withTrashed()->find($id);
        $user->delete();

        return redirect('admin/chef');
    }

    public function unblockChef($id)
    {
        \Auth::shouldUse('backpack');
        Gate::authorize('block-users');
        $user=Chef::withTrashed()->find($id);
        $user->restore();

        return redirect('admin/chef');
    }
    public function blockDeliveryman($id)
    {
        \Auth::shouldUse('backpack');
        Gate::authorize('block-users');
        $user=Deliveryman::withTrashed()->find($id);
        $user->delete();

        return redirect('admin/deliveryman');
    }

    public function unblockDeliveryman($id)
    {
        \Auth::shouldUse('backpack');
        Gate::authorize('block-users');
        $user=Deliveryman::withTrashed()->find($id);
        $user->restore();

        return redirect('admin/deliveryman');
    }

}
