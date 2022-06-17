@extends(backpack_view('blank'))

@php
  $defaultBreadcrumbs = [
    trans('backpack::crud.admin') => url(config('backpack.base.route_prefix'), 'dashboard'),
    'إدرارة طلبات الطعام الجديدة'=> false
  ];

  // if breadcrumbs aren't defined in the CrudController, use the default breadcrumbs
  $breadcrumbs = $breadcrumbs ?? $defaultBreadcrumbs;
@endphp

@section('header')
  <div class="container-fluid">
    <h2>
      <span class="text-capitalize">الطلبات الجديدة المعلّقة</span>
      {{-- <small id="datatable_info_stack">{!! $crud->getSubheading() ?? '' !!}</small> --}}
    </h2>
  </div>
@endsection

@section('content')
    <!-- Default box -->
  <div class="row">

    <!-- THE ACTUAL CONTENT -->

    <!-- TODO put these into work :) -->
    <div class="">
         <!--refresh button-->
        <div class="row mb-0">
          <div class="col-sm-6">
            <button class="btn btn-primary" onclick="location.reload()">
                 <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-clockwise" viewBox="0 0 16 16">
                <path fill-rule="evenodd" d="M8 3a5 5 0 1 0 4.546 2.914.5.5 0 0 1 .908-.417A6 6 0 1 1 8 2v1z"/>
                <path d="M8 4.466V.534a.25.25 0 0 1 .41-.192l2.36 1.966c.12.1.12.284 0 .384L8.41 4.658A.25.25 0 0 1 8 4.466z"/>
              </svg> تحديث </button>
          </div>
        <!--search bar-->
          <div class="col-sm-6">
            <div id="datatable_search_stack" class="mt-sm-0 mt-2 d-print-none">
                <div class="dataTables_filter">
                    <input name="searchKey"  placeholder="بحث...." class="form-control">
                </div>  
            </div>
          </div>
        </div>

       <!-- alert area-->
       <div id="alert">
        
       </div>
     
         <!--table-->
        <table
          id="table"
          class="bg-white table table-striped table-hover nowrap rounded shadow-xs border-xs mt-2"
          data-responsive=""
          data-has-details-row=""
          data-has-bulk-actions=""
          cellspacing="0">
            <thead>
              <tr>
                {{-- Table columns --}}
               
                    <th data-orderable="false"data-priority=""data-visible-in-export="false">
                        رقم الطلب 
                    </th>
                    <th data-orderable="false"data-priority=""data-visible-in-export="false">
                        رقم الطالب 
                    </th>
                    <th data-orderable="false"data-priority=""data-visible-in-export="false">
                        رقم الطاهي 
                    </th>
                    <th data-orderable="false"data-priority=""data-visible-in-export="false">
                        اسم الطاهي 
                    </th>
                    <th data-orderable="false"data-priority=""data-visible-in-export="false">
                        وقت إنشاء الطلب 
                    </th>
                    <th data-orderable="false"data-priority=""data-visible-in-export="false">
                        التكلفة الإجمالية 
                    </th>
                    <th data-orderable="false"data-priority=""data-visible-in-export="false">
                       الربح من الطلب 
                    </th>
                    <th data-orderable="false"data-priority=""data-visible-in-export="false">
                       الأفعال 
                    </th>
              </tr>
            </thead>
            <tbody>
                @foreach ($orders as $order)
                <tr id="order_{{$order->id}}"> 
                    <td>{{$order->id}} </td>
                    <td>{{$order->user_id}} </td>
                    <td>{{$order->chef_id}} </td>
                    <td>{{$order->chef->name}} </td>
                    <td>{{$order->selected_delivery_time}} </td>
                    <td>{{$order->total_cost}} </td>
                    <td>{{$order->profit}} </td>
                    <td> 
                        <a href="#" class="btn btn-xs btn-success" onclick="approve({{$order->id}})">
                            قبول
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-check-lg" viewBox="0 0 16 16">
                            <path d="M12.736 3.97a.733.733 0 0 1 1.047 0c.286.289.29.756.01 1.05L7.88 12.01a.733.733 0 0 1-1.065.02L3.217 8.384a.757.757 0 0 1 0-1.06.733.733 0 0 1 1.047 0l3.052 3.093 5.4-6.425a.247.247 0 0 1 .02-.022Z"/>
                         
                        </svg>
                        </a>
                        <a href="#" class="btn btn-xs btn-danger" onclick="reject({{$order->id}})">
                            رفض
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x" viewBox="0 0 16 16">
                            <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/>
                          </svg>
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
          </table>
          {{$orders->links()}}

    </div>

  </div>

@endsection

@section('after_scripts')
<script>

    function approve(id){
       
        //send request of approval
        var xhr = new XMLHttpRequest();
        var csrfToken = "{{ csrf_token() }}";
        xhr.open('POST', `/admin/new-orders/${id}/approve`, true);
        xhr.setRequestHeader('X-CSRF-TOKEN', csrfToken);

        xhr.onload = function(){
            if(this.status == 200){
                console.log('anything');
            //hide row
            hideOrderRow(id);
            //append success alert
           appendSuccessAlert();
            }
            else {
                console.console.log('failed '+this.status);
            }
        }
        xhr.send();
        
    }
    function reject(id){
       
       //send request of approval
       var xhr = new XMLHttpRequest();
       var csrfToken = "{{ csrf_token() }}";
       xhr.open('POST', `/admin/new-orders/${id}/reject`, true);
       xhr.setRequestHeader('X-CSRF-TOKEN', csrfToken);

       xhr.onload = function(){
           if(this.status == 200){
               console.log('anything');
           //hide row
           hideOrderRow(id);
           //append success alert
          appendSuccessAlert();
           }
           else {
               console.console.log('failed '+this.status);
           }
       }
       xhr.send();
       
   }
    function hideOrderRow(id){
        document.getElementById(`order_${id}`).outerHTML = "";
    }
    function appendSuccessAlert(){
        document.getElementById('alert').innerHTML = 
            `<div class="alert alert-success alert-dismissible fade show mt-2" role="alert">
                تمت العملية بنجاح!
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            `;
    }
    
</script>
@endsection