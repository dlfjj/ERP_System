@extends('layouts.default')

@section('page-crumbs')
    <ul id="breadcrumbs" class="breadcrumb">
        <li>
            <i class="icon-home"></i>
            <a href="/">Dashboard</a>
        </li>
        <li>
            <a href="/orders/" title="">Orders</a>
        </li>
        <li class="current">
            <a href="/orders/create" title="">New Order</a>
        </li>
    </ul>

    <ul class="crumb-buttons">
        {{--<li>--}}
            {{--<a href="/orders" title=""><span>Cancel</span></a>--}}
        {{--</li>--}}
        <li>
            <a href="javascript:void(0);" title=""><i class="icon-calendar"></i><span><?=date('F d, Y \(\K\W:W) H:i:s');?></span></a>
        </li>
    </ul>
@stop

@section('page-header')
    <div class="page-header">
        <div class="page-title">
            <a class="btn btn-default" href="/orders/"><i class="icon-plus-sign"></i> Cancel</a>
        </div>
    </div>
@stop

@section('content')
    <!--=== Page Content ===-->
    <div class="row">
        <!--=== Horizontal Forms ===-->
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading"><i class="icon-repurchase"></i> Choosing your customer</div>
                <div class="panel-body">
                    <table class="table table-hover table-bordered table-striped" id="customers-table">
                        <thead>
                            <tr>
                                <th class="cell-tight">Customer Code</th>
                                <th class="cell-tight">Customer Name</th>
                                <th class="cell-tight"></th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <!-- /Horizontal Forms -->
    </div>
@stop

@push('scripts')
    <script>
        // jquery getting data for purchase table
        $(function() {
            $('#customers-table').DataTable({
                "oLanguage": {

                    "sSearch": "<i class='icon-search icon-large table-search-icon'></i>"

                },
                processing: true,
                serverSide: true,
                ajax: '{!! route('cusomtersList/getdata') !!}',
                columns: [
                    { data: 'customer_code', name: 'customer_code' },
                    { data: 'customer_name', name: 'customer_name' },
                    {data: 'action', name: 'action', orderable: false, searchable: false}
                ]
            });
        });
    </script>
@endpush
