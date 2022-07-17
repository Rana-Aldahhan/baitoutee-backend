@extends(backpack_view('blank'))

@php
$defaultBreadcrumbs = [
    trans('backpack::crud.admin') => url(config('backpack.base.route_prefix'), 'dashboard'),
    trans('adminPanel.titles.deliverymen_financial_accounts') => url(config('backpack.base.route_prefix'),'deliverymen-financial-accounts'),
    trans('adminPanel.titles.deliveryman_financial_account')=>false
];
$breadcrumbs = $breadcrumbs ?? $defaultBreadcrumbs;
@endphp

@section('header')
    <div class="container-fluid">
        <h2>
            <span class="text-capitalize"> {{trans('adminPanel.titles.deliveryman_financial_account')}} : {{$deliveryman->name}}</span>
        </h2>
    </div>
@endsection

@section('content')
    <!-- balance cards -->
    <div class="row">
        <div class="col-sm-6">
        <div class="card text-white bg-success mb-3">
            <div class="card-body">
               {{ trans('adminPanel.attributes.earned_balance')}} : {{$deliveryman->balance}} S.P
            </div>
          </div>
        </div>
        <div class="col-sm-6">
            <div class="card text-white bg-warning mb-3">
                <div class="card-body">
                   {{ trans('adminPanel.attributes.collected_fees')}} : {{$deliveryman->total_collected_order_costs}} S.P
                </div>
              </div>
            </div>
    </div>
    
    {{-- tabs --}}
    <div class="container-fluid">
        <div class="row-fluid">
                <ul class="nav nav-tabs">
                    <li class="nav-item ">
                         <a class="nav-link @if(request()->target=='deliveries' || request()->target==null )active @endif" href="#deliveries" data-target="#deliveries" data-toggle="tab">{{ trans('adminPanel.attributes.deliveries_history')}}</a>
                    </li>
                    <li class="nav-item">
                        <a  class="nav-link @if(request()->target=='orders' )active @endif" href="#orders" data-target="#orders" data-toggle="tab">{{ trans('adminPanel.attributes.orders_history')}}</a>
                    </li>
                </ul>
                <div class="tab-content">
                    <div class=" tab-pane @if(request()->target=='deliveries' || request()->target==null )active @endif" id="deliveries">
                        {{-- deliveries content --}}
                        {{-- filter --}}
                        <div class="row mt-2">
                            <div class="dropdown show ml-3">
                                <a class="btn btn-primary dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="las la-filter"></i>
                                    {{trans('adminPanel.actions.filter')}}
                                </a>
                              
                                <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                                  <a class="dropdown-item @if(request()->paid==null) active @endif" href="/admin/deliverymen-financial-accounts/{{$deliveryman->id}} @if(request()->page!=null) &page={{request()->page}} @endif">{{trans('adminPanel.actions.all_results')}}</a>
                                  <a class="dropdown-item @if(request()->paid) active @endif" href="/admin/deliverymen-financial-accounts/{{$deliveryman->id}}?paid=1 @if(request()->page!=null) &page={{request()->page}} @endif">{{trans('adminPanel.actions.paid')}}</a>
                                  <a class="dropdown-item @if(!request()->paid && request()->paid!=null ) active @endif" href="/admin/deliverymen-financial-accounts/{{$deliveryman->id}}?paid=0 @if(request()->page!=null) &page={{request()->page}} @endif">{{trans('adminPanel.actions.unpaid')}}</a>
                                  
                                </div>
                              </div>
                        </div>
                        <div class="row">
                            <div class="">
                                <!--table-->
                                <table id="table" class="bg-white table table-responsive table-striped table-hover nowrap rounded shadow-xs border-xs mt-2"
                                    data-responsive="" data-has-details-row="" data-has-bulk-actions="" cellspacing="0">
                                    <thead>
                                        <tr>
                                            {{-- Table columns --}}
                                            <th data-orderable="false" data-priority="" data-visible-in-export="false">
                                                {{trans('adminPanel.attributes.delivery_id')}}
                                            </th>
                                            <th data-orderable="false" data-priority="" data-visible-in-export="false">
                                                {{trans('adminPanel.attributes.earned_balance')}}
                                            </th>
                                            <th data-orderable="false" data-priority="" data-visible-in-export="false">
                                                {{trans('adminPanel.attributes.orders_count')}}
                                            </th>
                                            <th data-orderable="false" data-priority="" data-visible-in-export="false">
                                                {{trans('adminPanel.attributes.delivery_date')}}
                                            </th>
                                            <th>
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody id="table_body" >
                                       @foreach ($deliveries as $delivery)
                                        <tr id="delivery_{{ $delivery->id }}" >
                                            <td><a href="/admin/delivery/{{$delivery->id}}/show"> {{ $delivery->id }} </a> </td>
                                            <td>{{ $delivery->deliveryman_cost_share * $delivery->orders->count() }} </td>
                                            <td>{{$delivery->orders->count() }} </td>
                                            <td>{{ $delivery->created_at }} </td>
                                            <td>
                                                @if(!$delivery->paid_to_deliveryman)
                                                <form method="post" action="/admin/delivery/{{$delivery->id}}/pay-to-deliveryman"> 
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
                                {{$deliveries->appends(Arr::except(Request::query(), 'deliveries-page'))->appends(['target'=>'deliveries'])->links(); }}
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane @if(request()->target=='orders' )active @endif" id="orders">
                        {{-- orders content --}}
                         {{-- filter --}}
                         <div class="row mt-2">
                            <div class="dropdown show ml-3">
                                <a class="btn btn-primary dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="las la-filter"></i>
                                    {{trans('adminPanel.actions.filter')}}
                                </a>
                              
                                <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                                  <a class="dropdown-item @if(request()->taken==null) active @endif" href="/admin/deliverymen-financial-accounts/{{$deliveryman->id}}@if(request()->page!=null) &page={{request()->page}} @else?@endif target=orders">{{trans('adminPanel.actions.all_results')}}</a>
                                  <a class="dropdown-item @if(request()->taken) active @endif" href="/admin/deliverymen-financial-accounts/{{$deliveryman->id}}?taken=1 @if(request()->page!=null) &page={{request()->page}} @endif&target=orders">{{trans('adminPanel.actions.taken')}}</a>
                                  <a class="dropdown-item @if(!request()->taken && request()->taken!=null ) active @endif" href="/admin/deliverymen-financial-accounts/{{$deliveryman->id}}?taken=0 @if(request()->page!=null) &page={{request()->page}} @endif&target=orders">{{trans('adminPanel.actions.not_taken')}}</a>
                                  
                                </div>
                              </div>
                        </div>
                        <div class="row">
                            <div class="">
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
                                                {{trans('adminPanel.attributes.total_cost')}}
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
                                        <tr id="order_{{ $order->id }}" >
                                            <td><a href="/admin/order/{{$order->id}}/show"> {{ $order->id }} </a> </td>
                                            <td>{{ $order->total_cost }} </td>
                                            <td>{{ $order->selected_delivery_time }} </td>
                                            <td>{{ $order->status }} </td>
                                            <td>
                                                @if(!$order->paid_to_accountant)
                                                <form method="post" action="/admin/order/{{$order->id}}/pay-to-accountant"> 
                                                @csrf
                                                <button type="submit" class="btn btn-primary">
                                                <i class="las la-hand-holding-usd"></i>
                                                {{trans('adminPanel.actions.take_money')}}
                                                </button>
                                                </form>
                                                @else
                                                <a href="#" class="btn btn-link text-success">
                                                    {{trans('adminPanel.actions.taken')}}
                                                    <i class="las la-check-double"></i>
                                                </a>
                                                @endif
                                            </td>
                                        </tr>
                                       @endforeach
                                    </tbody>
                                </table>
                                <!--pagination-->
                                {{$orders->appends(Arr::except(Request::query(), 'orders-page'))->appends(['target'=>'orders'])->links(); }}
                    
                    
                            </div>
                    
                        </div>
                    </div>
                </div>

        </div>
    </div>


@endsection

@section('after_scripts')
    <script>
    </script>
@endsection