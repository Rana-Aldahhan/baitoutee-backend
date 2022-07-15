@if(!$entry->seen)
<form method="post" action="{{ url($crud->route.'/'.$entry->getKey().'/mark-as-seen') }}">
    @csrf
<button class="btn btn-xs btn-success">
    {{trans('adminPanel.actions.mark_as_seen')}}
    <i class="las la-check"></i>
</button>
</form>
@else 
<a href="#" class="btn btn-link text-success">
    {{trans('adminPanel.actions.seen')}}
    <i class="las la-check-double"></i>
</a>
@endif