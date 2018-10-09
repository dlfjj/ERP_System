@extends('layouts.default')

@section('page-module-menu')
@stop

@section('page-crumbs')
    <ul id="breadcrumbs" class="breadcrumb">
        <li>
            <i class="icon-home"></i>
            <a href="/">Dashboard</a>
        </li>
        <li>
            <a href="/purchases/" title="">Purchases</a>
        </li>
        <li>
            <a href="/purchases/{{$purchase->id}}" title="">Details</a>
        </li>
        <li class="current">
            <a href="/purchases/line_item_add/{{$purchase->id}}" title="">Add Items</a>
        </li>
    </ul>

    <ul class="crumb-buttons">
        <li>
            <a href="javascript:void(0);" title=""><i class="icon-calendar"></i><span><?=date('F d, Y \(\K\W:W) H:i:s');?></span></a>
        </li>
    </ul>
@stop


@section('page-header')
    <div class="hidden" id="purchase_id2" data-item="{{$purchase->id}}"></div>
    <div class="page-header">
        <div class="page-title">
        </div>
        <!-- Page Stats -->
        <ul class="page-stats">
            <li>
                <div class="summary">
                    <span>Status</span>
                    <h3>{{$purchase->status}}</h3>
                </div>
            </li>
            <li>
                <div class="summary">
                    <span>Purchase Total</span>
                    <h3>{{$purchase->currency_code}} {{$purchase->gross_total}}</h3>
                </div>
            </li>

        </ul>
        <!-- /Page Stats -->

    </div>
@stop

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="widget box">
                <div class="widget-header">
                    <h4><i class="icon-reorder"></i> Choose Purchase Item</h4>
                </div>
                <div class="panel-body">
                    <table class="table table-striped table-bordered table-hover" id="product_table_for_purchase" data-dataTable='{"bServerSide": true, "bPaginate": true, "sAjaxSource": "/purchases/dt-available-products"}'>
                        <thead>
                        <tr>
                            <th class="">Part Number</th>
                            <th>Product Title</th>
                            <th class="width-1">P/U</th>
                            <th class="width-1">UOM</th>
                            <th style="width: 140px;">
                                <a href="/purchases/{{$purchase->id}}" class="btn btn-xs"><i class="icon-check"> Done</i></a>
                            </th>
                        </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

@stop

@push('scripts')
    <script>
        // jquery getting data for product table
        $(function() {
            $('#product_table_for_purchase').DataTable({
                "oLanguage": {

                    "sSearch": "<i class='icon-search icon-large table-search-icon'></i>"

                },
                processing: true,
                serverSide: true,
                ajax: '{!! route('purchase_line_items/getdata',[$purchase->id]) !!}',
                columns: [
                    { data: 'product_code', name: 'product_code' },
                    { data: 'product_name', name: 'product_name' },
                    { data: 'pack_unit', name: 'pack_unit' },
                    { data: 'pack_unit_hq', name: 'pack_unit_hq' },
                    {
                        // attach order id using jquery after the form being render in the view
                        data: 'action',
                        createdCell: function (td, cellData, rowData, row, col) {
                            var id= $("#purchase_id2").attr('data-item');
                            var input = $("<input>")
                                .attr("type", "hidden")
                                .attr("name", "purchase_id").val(id);
                            console.log(id);
                            $(td).find('.form').append(input);
                        },
                        name: 'action',
                        orderable: false,
                        searchable: false
                    }
                ]
            });
        });

    </script>
@endpush
