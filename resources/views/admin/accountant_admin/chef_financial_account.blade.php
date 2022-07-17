@extends(backpack_view('blank'))

@php
$defaultBreadcrumbs = [
    trans('backpack::crud.admin') => url(config('backpack.base.route_prefix'), 'dashboard'),
    trans('adminPanel.titles.chefs_financial_accounts') => url(config('backpack.base.route_prefix'),'chefs-financial-accounts'),
    trans('adminPanel.titles.chef_financial_account')=>false
];
$breadcrumbs = $breadcrumbs ?? $defaultBreadcrumbs;
@endphp

@section('header')
    <div class="container-fluid">
        <h2>
            <span class="text-capitalize"> {{trans('adminPanel.titles.chef_financial_account')}} : {{$chef->name}}</span>
        </h2>
    </div>
@endsection

@section('content')
    <!-- Default box -->
    <div class="row">
        <div class="col-sm-6">
            <div class="card text-white bg-success mb-3">
                <div class="card-body">
                {{ trans('adminPanel.attributes.earned_balance')}} : {{$chef->balance}} S.P
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <h3>
            {{ trans('adminPanel.attributes.orders_history')}}
        </h3>
        <div class="dropdown show ml-3">
            <a class="btn btn-primary dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="las la-filter"></i>
                {{trans('adminPanel.actions.filter')}}
            </a>
          
            <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
              <a class="dropdown-item @if(request()->paid==null) active @endif" href="/admin/chefs-financial-accounts/{{$chef->id}} @if(request()->page!=null) &page={{request()->page}} @endif">{{trans('adminPanel.actions.all_results')}}</a>
              <a class="dropdown-item @if(request()->paid) active @endif" href="/admin/chefs-financial-accounts/{{$chef->id}}?paid=1 @if(request()->page!=null) &page={{request()->page}} @endif">{{trans('adminPanel.actions.paid')}}</a>
              <a class="dropdown-item @if(!request()->paid && request()->paid!=null ) active @endif" href="/admin/chefs-financial-accounts/{{$chef->id}}?paid=0 @if(request()->page!=null) &page={{request()->page}} @endif">{{trans('adminPanel.actions.unpaid')}}</a>
              
            </div>
          </div>
    </div>
    <div class="row">
        <div class="">
            <!-- alert area-->
            <div id="alert">

            </div>
            <!--table-->
            <table id="table" class="bg-white table table-responsive table-striped table-hover nowrap rounded shadow-xs border-xs mt-2"
                data-responsive="" data-has-details-row="" data-has-bulk-actions="" cellspacing="0">
                <thead>
                    <tr>
                        {{-- Table columns --}}
                        <th data-orderable="false" data-priority="" data-visible-in-export="false">
                            {{trans('adminPanel.attributes.order_id')}}
                        </th>
                        <th data-orderable="false" data-priority="" data-visible-in-export="false">
                            {{trans('adminPanel.attributes.earned_balance')}}
                        </th>
                        <th data-orderable="false" data-priority="" data-visible-in-export="false">
                            {{trans('adminPanel.attributes.order_date')}}
                        </th>
                        <th data-orderable="false" data-priority="" data-visible-in-export="false">
                            {{trans('adminPanel.attributes.order_status')}}
                        </th>
                        <th>
                        </th>
                    </tr>
                </thead>
                <tbody id="table_body" >
                   @foreach ($orders as $order)
                    <tr id="chef_{{ $chef->id }}" >
                        <td><a href="/admin/order/{{$order->id}}/show"> {{ $order->id }} </a> </td>
                        <td>{{ $order->meals_cost }} </td>
                        <td>{{ $order->selected_delivery_time }} </td>
                        <td>{{ $order->status }} </td>
                        <td>
                            @if(!$order->paid_to_chef)
                            <form method="post" action="/admin/order/{{$order->id}}/pay-to-chef"> 
                            @csrf
                            <button type="submit" class="btn btn-primary">
                            <i class="las la-hand-holding-usd"></i>
                            {{trans('adminPanel.actions.pay')}}
                            </button>
                            </form>
                            @else
                            <a href="#" class="btn btn-link text-success">
                                {{trans('adminPanel.actions.paid')}}
                                <i class="las la-check-double"></i>
                            </a>
                            @endif
                        </td>
                    </tr>
                   @endforeach
                </tbody>
            </table>
            <!--pagination-->
            {{ $orders->links() }}


        </div>

    </div>
@endsection

@section('after_scripts')
    <script>
    </script>
@endsection