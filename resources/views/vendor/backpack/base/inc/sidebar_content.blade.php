<!-- This file is used to store sidebar items, starting with Backpack\Base 0.9.0 -->
<li class="nav-item"><a class="nav-link" href="{{ backpack_url('dashboard') }}"><i class="la la-home nav-icon"></i> {{ trans('backpack::base.dashboard') }}</a></li>
@if(backpack_user()->role->name=='super admin')
<li class='nav-item'><a class='nav-link' href='{{ backpack_url('admin') }}'><i class='nav-icon la la-question'></i> المشرفين</a></li>
@endif

<div id="accordion">
    @if(backpack_user()->role->name=='super admin' || backpack_user()->role->name=='hr admin')
    <div class='nav-item'>        
          <button class="btn" data-toggle="collapse" data-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
            إدارة طلبات الانضمام
          </button>
      <div id="collapseOne" class="collapse hide" aria-labelledby="headingOne" data-parent="#accordion">
            <li class='nav-item'><a class='nav-link' href='{{ backpack_url('user-join-request') }}'><i class='nav-icon la la-question'></i>طلبات انضمام المستخدمين</a></li>
            <li class='nav-item'><a class='nav-link' href='{{ backpack_url('chef-join-request') }}'><i class='nav-icon la la-question'></i> طلبات انضمام الطهاة</a></li>
            <li class='nav-item'><a class='nav-link' href='{{ backpack_url('deliveryman-join-request') }}'><i class='nav-icon la la-question'></i> طلبات انضمام عمال التوصيل</a></li>
      </div>
    </div>
    @endif
    @if(backpack_user()->role->name=='super admin' || backpack_user()->role->name=='hr admin')
    <div class='nav-item'>        
      <button class="btn" data-toggle="collapse" data-target="#collapseTwo" aria-expanded="true" aria-controls="collapseTwo">
        إدارة الأمور المالية
      </button>
      <div id="collapseTwo" class="collapse hide" aria-labelledby="headingTwo" data-parent="#accordion">
        <li class='nav-item'><a class='nav-link' href='{{ backpack_url('meal') }}'><i class='nav-icon la la-question'></i> الوجبات </a></li>
        <li class='nav-item'><a class='nav-link' href='{{ backpack_url('price-change-request') }}'><i class='nav-icon la la-question'></i> طلبات تغيير السعر</a></li>
      </div>
    </div>
    @endif
  </div>

