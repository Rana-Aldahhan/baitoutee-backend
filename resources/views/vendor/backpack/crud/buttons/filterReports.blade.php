<div class="dropdown show mt-3 mb-3">
    <a class="btn btn-primary dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        <i class="las la-filter"></i>
        {{trans('adminPanel.actions.filter')}}
    </a>
  
    <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
      <a class="dropdown-item @if(request()->filter==null) active @endif" href="/admin/report @if(request()->page!=null) &page={{request()->page}} @endif">{{trans('adminPanel.actions.all_results')}}</a>
      <a class="dropdown-item @if(request()->filter=='seen') active @endif" href="/admin/report?filter=seen @if(request()->page!=null) &page={{request()->page}} @endif">{{trans('adminPanel.actions.seen')}}</a>
      <a class="dropdown-item @if(request()->filter=='unseen') active @endif" href="/admin/report?filter=unseen @if(request()->page!=null) &page={{request()->page}} @endif">{{trans('adminPanel.actions.unseen')}}</a>
      
    </div>
  </div>

