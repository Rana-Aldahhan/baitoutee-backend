<div class="dropdown show mt-3 mb-3">
    <a class="btn btn-primary dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        <i class="las la-filter"></i>
        {{trans('adminPanel.actions.filter_status')}}
    </a>
  
    <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
      <a class="dropdown-item @if(request()->status==null) active @endif" href="/admin/order ">{{trans('adminPanel.actions.all_results')}}</a>
      <a class="dropdown-item @if(request()->status=='pending') active @endif" href="/admin/order?status=pending">{{trans('adminPanel.status.order.pending')}}</a>
      <a class="dropdown-item @if(request()->status=='approved') active @endif" href="/admin/order?status=approved">{{trans('adminPanel.status.order.approved')}}</a>
      <a class="dropdown-item @if(request()->status=='notApproved') active @endif" href="/admin/order?status=notApproved">{{trans('adminPanel.status.order.notApproved')}}</a>
      <a class="dropdown-item @if(request()->status=='prepared') active @endif" href="/admin/order?status=prepared">{{trans('adminPanel.status.order.prepared')}}</a>
      <a class="dropdown-item @if(request()->status=='failedAssigning') active @endif" href="/admin/order?status=failedAssigning">{{trans('adminPanel.status.order.failedAssigning')}}</a>
      <a class="dropdown-item @if(request()->status=='picked') active @endif" href="/admin/order?status=picked">{{trans('adminPanel.status.order.picked')}}</a>
      <a class="dropdown-item @if(request()->status=='delivered') active @endif" href="/admin/order?status=delivered">{{trans('adminPanel.status.order.delivered')}}</a>
      <a class="dropdown-item @if(request()->status=='notDelivered') active @endif" href="/admin/order?status=notDelivered">{{trans('adminPanel.status.order.notDelivered')}}</a>
      <a class="dropdown-item @if(request()->status=='canceled') active @endif" href="/admin/order?status=canceled">{{trans('adminPanel.status.order.canceled')}}</a>
    </div>
  </div>

