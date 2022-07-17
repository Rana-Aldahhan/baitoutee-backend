@if($entry->status=='failedAssigning')
<form method="post" action="{{ url($crud->route.'/'.$entry->getKey().'/reassign-order-to-delivery') }}">
    @csrf
<button class="btn btn-xs btn-success m-2">
    {{trans('adminPanel.actions.reassign_order')}}
    <i class="las la-motorcycle"></i>
</button>
</form>
@endif