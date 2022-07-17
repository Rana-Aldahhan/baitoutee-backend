@if($entry->status != 'canceled')
<form method="post" action="{{ url($crud->route.'/'.$entry->getKey().'/cancel') }}">
    @csrf
<button class="btn btn-xs btn-danger m-2">
    {{trans('adminPanel.actions.cancel_order')}}
    <i class="las la-ban"></i>
</button>
</form>
@endif
