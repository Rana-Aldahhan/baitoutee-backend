@extends(backpack_view('blank'))

@php
$defaultBreadcrumbs = [
    trans('backpack::crud.admin') => url(config('backpack.base.route_prefix'), 'dashboard'),
    trans('adminPanel.titles.chefs_financial_accounts') => false,
];
$breadcrumbs = $breadcrumbs ?? $defaultBreadcrumbs;
$title=trans('adminPanel.titles.chefs_financial_accounts');
@endphp

@section('header')
    <div class="container-fluid">
        <h2>
            <span class="text-capitalize"> {{trans('adminPanel.titles.chefs_financial_accounts')}}</span>
        </h2>
    </div>
@endsection

@section('content')
    <!-- Default box -->
    <div class="row">

        <!-- THE ACTUAL CONTENT -->

        <!-- TODO put these into work :) -->
        <div class="">
            <div class="row mb-0">
                <!--search bar-->
                <div class="col-sm-9">
                    <div id="datatable_search_stack" class="mt-sm-0 mt-2 d-print-none">
                        <div class="dataTables_filter">
                            <input id="search" name="searchKey" placeholder="{{trans('adminPanel.actions.search')}}....." class="form-control" oninput="searchChefAccounts()">
                        </div>
                    </div>
                </div>
            </div>

            <!-- alert area-->
            <div id="alert">

            </div>

            <!--table-->
            <table id="table" class="bg-white table table-responsive  table-striped table-hover nowrap rounded shadow-xs border-xs mt-2"
                data-responsive="" data-has-details-row="" data-has-bulk-actions="" cellspacing="0">
                <thead>
                    <tr>
                        {{-- Table columns --}}
                        <th data-orderable="false" data-priority="" data-visible-in-export="false">
                            {{trans('adminPanel.attributes.id')}}
                        </th>
                        <th data-orderable="false" data-priority="" data-visible-in-export="false">
                            {{trans('adminPanel.attributes.chef_name')}}
                        </th>
                        <th data-orderable="false" data-priority="" data-visible-in-export="false">
                            {{trans('adminPanel.attributes.phone_number')}}
                        </th>
                        <th data-orderable="false" data-priority="" data-visible-in-export="false">
                            {{trans('adminPanel.attributes.unpaid_orders_count')}}
                        </th>
                        <th data-orderable="false" data-priority="" data-visible-in-export="false">
                            {{trans('adminPanel.attributes.earned_balance')}}
                        </th>
                        <th>
                        </th>
                    </tr>
                </thead>
                <tbody id="table_body">
                    @foreach ($chefs as $chef)
                        <tr id="chef_{{ $chef->id }}">
                            <td>{{ $chef->id }} </td>
                            <td><a href="/admin/chef/{{$chef->id}}/show"> {{ $chef->name }} </a> </td>
                            <td>{{ $chef->phone_number }} </td>
                            <td>{{ $chef->orders->where('paid_to_chef',false)->count() }} </td>
                            <td>{{ $chef->balance }} </td>
                            <td> <a href="/admin/chefs-financial-accounts/{{$chef->id}}" class="btn btn-link text-success">
                                <i class="lar la-eye"></i>
                                {{trans('adminPanel.actions.show_details')}}
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <!--pagination-->
            {{ $chefs->links() }}


        </div>

    </div>
@endsection

@section('after_scripts')
    <script>
        function searchChefAccounts(){
            var searchValue=document.getElementById('search').value;
            var xhr = new XMLHttpRequest();
            xhr.open('GET', '/admin/chefs-financial-accounts/search?search='+searchValue, true);

            xhr.onload = function(){
                if(this.status == 200){
                var chefs = JSON.parse(this.responseText);
                var output = '';
                
                for(var i=0; i<chefs.length;i++){
                    var chef=chefs[i];
                    output += `<tr>
                            <td>${chef.id} </td>
                            <td><a href="/admin/chef/${chef.id}/show"> ${chef.name} </a> </td>
                            <td>${chef.phone_number} </td>
                            <td>${chef.unpaid_orders_count} </td>
                            <td>${chef.balance} </td>
                            <td> <a href="/admin/chefs-financial-accounts/${chef.id}" class="btn btn-link text-success">
                                <i class="lar la-eye"></i>
                                عرض التفاصيل
                                </a>
                            </td>
                        </tr>`;
                }

                document.getElementById('table_body').innerHTML = output;
                }
            }

            xhr.send();
        }
    </script>
@endsection
