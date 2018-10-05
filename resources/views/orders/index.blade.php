@extends('layouts.default')

@section('page-module-menu')
    <li><a href="/orders">Orders</a></li>
    @if(has_role('orders_export'))
        <li><a href="/orders/export">Export</a></li>
    @endif
@stop

@section('page-crumbs')
    <ul id="breadcrumbs" class="breadcrumb">
        <li>
            <i class="icon-home"></i>
            <a href="/">Dashboard</a>
        </li>
        <li class="current">
            <a href="/orders/" title="">Orders</a>
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
            @if(has_role('orders_edit'))
                <a class="btn btn-success btn-lg" href="/orders/create"><i class="icon-plus-sign"></i> New Order</a>
            @endif
        </div>

        <ul class="page-stats">
            @if(has_role('orders_edit'))
                <li>
                    <div class="summary">
                    </div>
                </li>
            @endif
        </ul>
    </div>

@stop

@section('content')

    <form class="form-inline" id="create" action="/orders/create" method="POST">
    </form>
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="widget-header">
                    <div class="panel-heading"><i class="icon-reorder"></i> Order Index</div>
                    {{--<div class="toolbar no-padding">--}}
                        {{--<div class="btn-group">--}}
                            {{--<span class="btn btn-xs widget-collapse"><i class="icon-angle-down"></i></span>--}}
                        {{--</div>--}}
                    {{--</div>--}}
                </div>
                <div class="panel-body">
                    {{--<table class="table table-striped table-bordered table-hover datatable" data-dataTable='{"bServerSide": true, "sAjaxSource": "/orders/dt-index", "aaSorting": [[ 0, "desc" ]]}'>--}}
                    <table class="table table-hover table-bordered table-striped" id="orders-table" style="width: 100%;">
                    <thead>
                        <tr>
                            <th class="cell-tight">ORDER #</th>
                            <th style="width: 170px;" class="cell-tight">STATUS</th>
                            <th class="cell-tight">C.O.N</th>
                            <th class="cell-tight">PLACED</th>
                            <th>CUSTOMER NAME</th>
                            <th class="cell-tight">EST. FINISH</th>
                            <th class="cell-tight">GR TOTAL</th>
                            <th></th>
                        </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <!-- /Normal -->
@stop

@push('scripts')
    <script src="https://cdn.datatables.net/1.10.12/js/jquery.dataTables.min.js"></script>
    <script type="text/javascript">
        // jquery getting data for purchase table
        $(function() {
            $('#orders-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{!! route('orders/getdata') !!}',
                columns: [
                    { data: 'id', name: 'id' },
                    { data: 'name', name: 'order_status.name' },
                    { data: 'customer_order_number', name: 'customer_order_number' },
                    { data: 'order_date', name: 'order_date' },
                    { data: 'customer_name', name: 'customers.customer_name'},
                    { data: 'estimated_finish_date', name: 'estimated_finish_date' },

                    { data: 'total_gross', name: 'total_gross' },
                    {data: 'action', name: 'action', orderable: false, searchable: false}
                ]
            });
        });
    </script>
@endpush