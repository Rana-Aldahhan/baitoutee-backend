<!-- This file is used to store sidebar items, starting with Backpack\Base 0.9.0 -->
<li class="nav-item"><a class="nav-link" href="{{ backpack_url('dashboard') }}"><i class="la la-home nav-icon"></i> {{ trans('backpack::base.dashboard') }}</a></li>
@if(backpack_user()->role->name=='super admin')
<li class='nav-item'><a class='nav-link' href='{{ backpack_url('admin') }}'>
  <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-person-workspace nav-icon" viewBox="0 0 16 16">
  <path d="M4 16s-1 0-1-1 1-4 5-4 5 3 5 4-1 1-1 1H4Zm4-5.95a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5Z"/>
  <path d="M2 1a2 2 0 0 0-2 2v9.5A1.5 1.5 0 0 0 1.5 14h.653a5.373 5.373 0 0 1 1.066-2H1V3a1 1 0 0 1 1-1h12a1 1 0 0 1 1 1v9h-2.219c.554.654.89 1.373 1.066 2h.653a1.5 1.5 0 0 0 1.5-1.5V3a2 2 0 0 0-2-2H2Z"/>
</svg> {{trans('adminPanel.entities.the_admins')}}</a></li>
@endif

<div id="accordion">
    <!-- every one can view users -->
    <div class='nav-item'> 
      <i class="nav-icon las la-users" style="color: #869ab8"></i>   
          <button class="btn" data-toggle="collapse" data-target="#collapseFour" aria-expanded="true" aria-controls="collapseOne" >
            {{trans('adminPanel.titles.show_users')}} 
          </button>
      <div id="collapseFour" class="collapse hide" aria-labelledby="headingFour" data-parent="#accordion">
        <li class='nav-item'><a class='nav-link' href='{{ backpack_url('user') }}'><i class=" nav-icon las la-user-friends"></i> {{trans('adminPanel.entities.users')}}</a></li>
        <li class='nav-item'><a class='nav-link' href='{{ backpack_url('chef') }}'><i class='nav-icon las la-utensils'></i> {{trans('adminPanel.entities.chefs')}}</a></li>
        <li class='nav-item'><a class='nav-link' href='{{ backpack_url('deliveryman') }}'><i class='nav-icon las la-motorcycle'></i>{{trans('adminPanel.entities.deliverymen')}}</a></li>
      </div>
    </div>

    @if(backpack_user()->role->name=='super admin' || backpack_user()->role->name=='hr admin')
    <div class='nav-item'> 
      <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="#869ab8" class="nav-icon bi bi-person-plus " viewBox="0 0 16 16">
        <path d="M6 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6zm2-3a2 2 0 1 1-4 0 2 2 0 0 1 4 0zm4 8c0 1-1 1-1 1H1s-1 0-1-1 1-4 6-4 6 3 6 4zm-1-.004c-.001-.246-.154-.986-.832-1.664C9.516 10.68 8.289 10 6 10c-2.29 0-3.516.68-4.168 1.332-.678.678-.83 1.418-.832 1.664h10z"/>
        <path fill-rule="evenodd" d="M13.5 5a.5.5 0 0 1 .5.5V7h1.5a.5.5 0 0 1 0 1H14v1.5a.5.5 0 0 1-1 0V8h-1.5a.5.5 0 0 1 0-1H13V5.5a.5.5 0 0 1 .5-.5z"/>
      </svg>      
          <button class="btn" data-toggle="collapse" data-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne" >
            {{trans('adminPanel.titles.manage_join_requests')}} 
          </button>
      <div id="collapseOne" class="collapse hide" aria-labelledby="headingOne" data-parent="#accordion">
            <li class='nav-item'><a class='nav-link' href='{{ backpack_url('user-join-request') }}'><i class=" nav-icon las la-user-friends"></i>{{trans('adminPanel.entities.user_join_requests')}}</a></li>
            <li class='nav-item'><a class='nav-link' href='{{ backpack_url('chef-join-request') }}'><i class=" nav-icon las la-utensils"></i> {{trans('adminPanel.entities.chef_join_requests')}}</a></li>
            <li class='nav-item'><a class='nav-link' href='{{ backpack_url('deliveryman-join-request') }}'><i class="nav-icon las la-motorcycle"></i> {{trans('adminPanel.entities.deliveryman_join_requests')}}</a></li>
      </div>
    </div>
    @endif
    @if(backpack_user()->role->name=='super admin' || backpack_user()->role->name=='accountant admin')
    <div class='nav-item'>   
      <i class="nav-icon las la-money-bill" style="color: #869ab8"></i>     
      <button class="btn" data-toggle="collapse" data-target="#collapseTwo" aria-expanded="true" aria-controls="collapseTwo">
        {{trans('adminPanel.titles.manage_financial_affairs')}} 
      </button>
      <div id="collapseTwo" class="collapse hide" aria-labelledby="headingTwo" data-parent="#accordion">
        <li class='nav-item'><a class='nav-link' href='{{ backpack_url('meal') }}'><i class="nav-icon las la-hamburger"></i>{{trans('adminPanel.entities.meals')}} </a></li>
        <li class='nav-item'><a class='nav-link' href='{{ backpack_url('price-change-request') }}'><i class=" nav-icon las la-hand-holding-usd"></i> {{trans('adminPanel.entities.price_change_requests')}}</a></li>
        <li class='nav-item'><a class='nav-link' href='/admin/profit-values'><i class=" nav-icon las la-funnel-dollar"></i>  {{trans('adminPanel.titles.manage_profit_values')}}</a></li>
        <li class='nav-item'><a class='nav-link' href='/admin/chefs-financial-accounts'><i class="nav-icon las la-file-invoice-dollar"></i>  {{trans('adminPanel.titles.chefs_financial_accounts')}}</a></li>
        <li class='nav-item'><a class='nav-link' href='/admin/deliverymen-financial-accounts'><i class="nav-icon las la-file-invoice-dollar"></i>  {{trans('adminPanel.titles.deliverymen_financial_accounts')}}</a></li>
      </div>
    </div>
    @endif
    {{-- @if(backpack_user()->role->name=='super admin' || backpack_user()->role->name=='orders admin') --}}
    <div class='nav-item'> 
      <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="#869ab8" class="bi bi-gear" viewBox="0 0 16 16">
        <path d="M8 4.754a3.246 3.246 0 1 0 0 6.492 3.246 3.246 0 0 0 0-6.492zM5.754 8a2.246 2.246 0 1 1 4.492 0 2.246 2.246 0 0 1-4.492 0z"/>
        <path d="M9.796 1.343c-.527-1.79-3.065-1.79-3.592 0l-.094.319a.873.873 0 0 1-1.255.52l-.292-.16c-1.64-.892-3.433.902-2.54 2.541l.159.292a.873.873 0 0 1-.52 1.255l-.319.094c-1.79.527-1.79 3.065 0 3.592l.319.094a.873.873 0 0 1 .52 1.255l-.16.292c-.892 1.64.901 3.434 2.541 2.54l.292-.159a.873.873 0 0 1 1.255.52l.094.319c.527 1.79 3.065 1.79 3.592 0l.094-.319a.873.873 0 0 1 1.255-.52l.292.16c1.64.893 3.434-.902 2.54-2.541l-.159-.292a.873.873 0 0 1 .52-1.255l.319-.094c1.79-.527 1.79-3.065 0-3.592l-.319-.094a.873.873 0 0 1-.52-1.255l.16-.292c.893-1.64-.902-3.433-2.541-2.54l-.292.159a.873.873 0 0 1-1.255-.52l-.094-.319zm-2.633.283c.246-.835 1.428-.835 1.674 0l.094.319a1.873 1.873 0 0 0 2.693 1.115l.291-.16c.764-.415 1.6.42 1.184 1.185l-.159.292a1.873 1.873 0 0 0 1.116 2.692l.318.094c.835.246.835 1.428 0 1.674l-.319.094a1.873 1.873 0 0 0-1.115 2.693l.16.291c.415.764-.42 1.6-1.185 1.184l-.291-.159a1.873 1.873 0 0 0-2.693 1.116l-.094.318c-.246.835-1.428.835-1.674 0l-.094-.319a1.873 1.873 0 0 0-2.692-1.115l-.292.16c-.764.415-1.6-.42-1.184-1.185l.159-.291A1.873 1.873 0 0 0 1.945 8.93l-.319-.094c-.835-.246-.835-1.428 0-1.674l.319-.094A1.873 1.873 0 0 0 3.06 4.377l-.16-.292c-.415-.764.42-1.6 1.185-1.184l.292.159a1.873 1.873 0 0 0 2.692-1.115l.094-.319z"/>
      </svg>       
      <button class="btn" data-toggle="collapse" data-target="#collapseThree" aria-expanded="true" aria-controls="collapseTwo">
        {{trans('adminPanel.titles.manage_orders_and_subscriptions')}} 
      </button>
      <div id="collapseThree" class="collapse hide" aria-labelledby="headingTwo" data-parent="#accordion">
        <li class='nav-item'><a class='nav-link' href='/admin/new-orders'>
          <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="#869ab8" class="bi bi-hourglass-split" viewBox="0 0 16 16">
          <path d="M2.5 15a.5.5 0 1 1 0-1h1v-1a4.5 4.5 0 0 1 2.557-4.06c.29-.139.443-.377.443-.59v-.7c0-.213-.154-.451-.443-.59A4.5 4.5 0 0 1 3.5 3V2h-1a.5.5 0 0 1 0-1h11a.5.5 0 0 1 0 1h-1v1a4.5 4.5 0 0 1-2.557 4.06c-.29.139-.443.377-.443.59v.7c0 .213.154.451.443.59A4.5 4.5 0 0 1 12.5 13v1h1a.5.5 0 0 1 0 1h-11zm2-13v1c0 .537.12 1.045.337 1.5h6.326c.216-.455.337-.963.337-1.5V2h-7zm3 6.35c0 .701-.478 1.236-1.011 1.492A3.5 3.5 0 0 0 4.5 13s.866-1.299 3-1.48V8.35zm1 0v3.17c2.134.181 3 1.48 3 1.48a3.5 3.5 0 0 0-1.989-3.158C8.978 9.586 8.5 9.052 8.5 8.351z"/>
        </svg>{{trans('adminPanel.titles.manage_pending_orders')}}  </a></li>
        <li class='nav-item'><a class='nav-link' href='{{ backpack_url('order') }}'><i class="nav-icon las la-clipboard-list"></i> {{trans('adminPanel.entities.orders')}}</a></li>
        <li class='nav-item'><a class='nav-link' href='{{ backpack_url('delivery') }}'><i class="nav-icon las la-motorcycle"></i> {{trans('adminPanel.entities.deliveries')}}</a></li>
        <li class='nav-item'><a class='nav-link' href='{{ backpack_url('subscription') }}'><i class=" nav-icon las la-stream"></i> {{trans('adminPanel.entities.subscriptions')}}</a></li>
      </div>
    </div>
    {{-- @endif --}}
  </div>


@if(backpack_user()->role->name=='super admin' || backpack_user()->role->name=='reports admin')
<li class='nav-item'><a class='nav-link' href='{{ backpack_url('report') }}'><i class="nav-icon las la-flag"></i> {{trans('adminPanel.entities.reports')}}</a></li>
@endif
