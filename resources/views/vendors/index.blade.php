@extends('layouts.default')

@section('page-module-menu')
    <li><a href="/vendors">Vendors</a></li>
@stop

@section('page-crumbs')
    <ul id="breadcrumbs" class="breadcrumb">
        <li>
            <i class="icon-home"></i>
            <a href="/dashboard">Dashboard</a>
        </li>
        <li class="current">
            <a href="/vendors" title="">Vendors</a>
        </li>
    </ul>

    <ul class="crumb-buttons">
        <li>
            <a href="javascript:void(0);" title=""><i class="icon-calendar"></i><span><?=date('F d, Y \(\K\W:W) H:i:s');?></span></a>
        </li>
    </ul>
@stop

@section('page-header')
    <div class="page-header">
        <div class="page-title">
            @if(has_role('vendors_edit'))
                <a href="/vendors/create" class="btn btn-success"><i class="icon-plus-sign"></i> New Vendor</a>
            @endif
        </div>

        <ul class="page-stats">
            <li>
                <div class="summary">
                </div>
            </li>
        </ul>
    </div>
@stop

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="widget box">
                <div class="widget-header">
                    <h4><i class="icon-reorder"></i> Vendor Index</h4>
                </div>
                <div class="widget-content">
                    <table class="table table-striped table-bordered"  id="vendors-table">
                        <thead>
                        <tr>
                            <th>Status</th>
                            <th>Company Name</th>
                            <th>Company Name Local</th>
                            <th>City</th>
                            <th>Country</th>
                            <th>View</th>
                        </tr>
                        </thead>
                        {{--<tbody>--}}
                        {{--@foreach($vendors as $vendor)--}}
                        {{--<tr>--}}
                        {{--<td>{{$vendor->status}}</td>--}}
                        {{--<td>{{$vendor->company_name}}</td>--}}
                        {{--<td>{{$vendor->company_name_localized}}</td>--}}
                        {{--<td>{{$vendor->city}}</td>--}}
                        {{--<td>{{$vendor->country}}</td>--}}
                        {{--<td><a href="/vendors/{{ $vendor->id }}" class="bs-tooltip" title="View"><i class="icon-search"></i></a></td>--}}
                        {{--</tr>--}}
                        {{--@endforeach--}}
                        {{--</tbody>--}}
                    </table>
                    <!-- <div class="paginate_links" align="center">
                         $vendors->links()
                    </div> -->
                </div>
            </div>
        </div>
    </div>
    {{--<script>--}}
    {{--$(document).ready(function(){--}}
    {{--$('#vendor-table').dataTable();--}}
    {{--})--}}
    {{--</script>--}}
@stop
@push('scripts')
    <script>
        // jquery getting data for purchase table
        $(function() {
            $('#vendors-table').DataTable({
                "oLanguage": {

                    // "sSearch": "<i class='icon-search icon-large table-search-icon'></i>"

                },
                processing: true,
                serverSide: true,
                ajax: '{!! route('vendors/getdata') !!}',
                columns: [
                    { data: 'status', name: 'status' },
                    { data: 'company_name', name: 'company_name' },
                    { data: 'company_name_localized', name: 'company_name_localized' },
                    { data: 'city', name: 'city' },
                    { data: 'country', name: 'country' },
                    { data: 'action', name: 'action', orderable: false, searchable: false}
                ]
            });
        });
    </script>
@endpush