<div class="dropdown show mt-3 mb-3">
    <a class="btn btn-primary dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        <i class="las la-filter"></i>
        {{trans('adminPanel.actions.filter_availability')}}
    </a>
  
    <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
      <a class="dropdown-item @if(request()->available==null) active @endif" href="/admin/deliveryman ">{{trans('adminPanel.actions.all_results')}}</a>
      <a class="dropdown-item @if(request()->available==1) active @endif" href="/admin/deliveryman?available=1">{{trans('adminPanel.attributes.is_available')}}</a>
      <a class="dropdown-item @if(request()->available==0 && request()->available!=null) active @endif" href="/admin/deliveryman?available=0">{{trans('adminPanel.attributes.not_available')}}</a>
      
    </div>
  </div>

