@extends(backpack_view('blank'))

@php
$defaultBreadcrumbs = [
    trans('backpack::crud.admin') => url(config('backpack.base.route_prefix'), 'dashboard'),
    trans('adminPanel.titles.manage_profit_values') => false,
];

// if breadcrumbs aren't defined in the CrudController, use the default breadcrumbs
$breadcrumbs = $breadcrumbs ?? $defaultBreadcrumbs;
@endphp

@section('header')
    <div class="container-fluid">
        <h2>
            <span class="text-capitalize"> </span>
        </h2>
    </div>
@endsection

@section('content')
    <!-- Default box -->
    

        <!-- THE ACTUAL CONTENT -->

        <form method="post" action="/admin/profit-values/edit" id="form">
            @csrf 
            <!--edit/save button-->
            <div class="row mb-5 ml-3 mt-3" id="primaryButton">
                <button  class="btn btn-primary"  onclick="replaceTextsWithInput()"> 
                    <i class="las la-pen"></i> 
                    {{trans('adminPanel.actions.edit_values')}}
                </button>

            </div>
            <!-- alert area-->
            <div id="alert">
                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        @foreach ($errors->all() as $error)
                                {{ $error }} <br>
                         @endforeach
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                          <span aria-hidden="true">&times;</span>
                        </button>
                      </div>
                @endif
            </div>

            <div class="container" style="align-content: center"> 
            <div class="row">
                <div class="col-sm-6">
                  <div class="card"  style="width: 293px;height: 178px">
                    <div class="card-body">
                      <h5 class="card-title">{{trans('adminPanel.attributes.mealProfit')}}</h5>
                      <p class="card-text" id="text1">{{$mealProfit}} S.P</p>
                    </div>
                  </div>
                </div>
                <div class="col-sm-6">
                  <div class="card"  style="width: 293px;height: 178px">
                    <div class="card-body">
                      <h5 class="card-title">{{trans('adminPanel.attributes.deliveryPercentage')}}</h5>
                      <p class="card-text" id="text2">{{$deliveryPercentage}} %</p>
                    </div>
                  </div>
                </div>
            </div>

              <div class="row">
                <div class="col-sm-6">
                  <div class="card"  style="width: 293px;height: 178px">
                    <div class="card-body">
                      <h5 class="card-title">{{trans('adminPanel.attributes.kmCost')}}</h5>
                      <p class="card-text" id="text3">{{$kmCost}} S.P  
                    </p>
                      
                    </div>
                  </div>
                </div>
                <div class="col-sm-6">
                  <div class="card" style="width: 293px;height: 178px;">
                    <div class="card-body">
                      <h5 class="card-title">{{trans('adminPanel.attributes.balance')}}</h5>
                      <p class="card-text">{{$balance}} S.P</p>
                    </div>
                  </div>
                </div>
              </div>

            </div>
            </form>


@endsection

@section('after_scripts')
    <script>
      function replaceTextsWithInput(){
        document.getElementById(`primaryButton`).innerHTML =
            `<button type="submit" class="btn btn-success" "> 
                <i class="las la-save"></i>
                    {{trans('adminPanel.actions.save')}}
            </button>`;
        document.getElementById(`text1`).innerHTML =
            `<input type="text" class="form-control" id="mealProfit" placeholder="meal profit" name="meal_profit" value="{{$mealProfit}}">`;
        document.getElementById(`text2`).innerHTML =
        `<input type="text" class="form-control" id="deliveryPercentage" placeholder="delivery percentage" name="delivery_profit_percentage" value="{{$deliveryPercentage}}">`;
        document.getElementById(`text3`).innerHTML =
            `<input type="text" class="form-control" id="kmCost" placeholder="km cost" name="cost_of_one_km" value="{{$kmCost}}">`;
      }

    </script>
@endsection
