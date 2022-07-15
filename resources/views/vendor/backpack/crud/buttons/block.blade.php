@if($entry->deleted_at===null)
<form method="post" action="{{ url($crud->route.'/'.$entry->getKey().'/block') }}" id="block.{{$entry->id}}">
    @csrf
<button  class="btn btn-danger" type="submit">
    <i class="las la-user-slash"></i>
    {{trans('adminPanel.actions.block')}}
</button>
</form>
@else 
<form method="post" action="{{ url($crud->route.'/'.$entry->getKey().'/unblock') }}" id="unblock.{{$entry->id}}">
    @csrf
<button  class="btn btn-success" type="submit">
    <i class="las la-user-slash"></i>
    {{trans('adminPanel.actions.unblock')}} 
</button>
</form>
@endif