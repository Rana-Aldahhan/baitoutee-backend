<!-- This file is used to store sidebar items, starting with Backpack\Base 0.9.0 -->
<li class="nav-item"><a class="nav-link" href="{{ backpack_url('dashboard') }}"><i class="la la-home nav-icon"></i> {{ trans('backpack::base.dashboard') }}</a></li>
@if(backpack_user()->role->name=='super admin')
<li class='nav-item'><a class='nav-link' href='{{ backpack_url('admin') }}'><i class='nav-icon la la-question'></i> Admins</a></li>
@endif
@if(backpack_user()->role->name=='super admin' || backpack_user()->role->name=='hr admin')
<li class='nav-item'><a class='nav-link' href='{{ backpack_url('user-join-request') }}'><i class='nav-icon la la-question'></i> User join requests</a></li>
<li class='nav-item'><a class='nav-link' href='{{ backpack_url('chef-join-request') }}'><i class='nav-icon la la-question'></i> Chef join requests</a></li>
<li class='nav-item'><a class='nav-link' href='{{ backpack_url('deliveryman-join-request') }}'><i class='nav-icon la la-question'></i> Deliveryman join requests</a></li>
@endif