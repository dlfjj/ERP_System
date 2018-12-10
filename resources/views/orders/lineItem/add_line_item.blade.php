@extends('layouts.default')

@section('page-module-menu')
    <li><a href="/orders/{{$order->id}}">Details</a></li>
@stop



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
            <a href="/orders/{{$order->id}}" title="">Details</a>
        </li>
    </ul>

    <ul class="crumb-buttons">
        <li>
            <a href="javascript:void(0);" title=""><i class="icon-calendar"></i><span><?=date('F d, Y \(\K\W:W) H:i:s');?></span></a>
        </li>
    </ul>

@stop


@section('page-header')
    <div class="hidden" id="order_id2" data-item="{{$order->id}}"></div>
    <div class="page-header">
        <div class="page-title">
        </div>
        <!-- Page Stats -->
        <ul class="page-stats">
            <li>
                <div class="summary">
                    <span>Order ID</span>
                    <h3>{{$order->id}}</h3>
                </div>
            </li>
            <li>
                <div class="summary">
                    <span>Status</span>
                    <h3>{{$order->status->name}}</h3>
                </div>
            </li>
            <li>
                <div class="summary">
                    <span>Order Total</span>
                    <h3>{{$order->currency_code}} {{$order->total}}</h3>
                </div>
            </li>
        </ul>

        <!-- /Page Stats -->

    </div>
@stop

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <div><i class="icon-reorder"></i> Order Details</div>
                </div>
                <div class="panel-body">
                    <table class="table table-striped table-bordered table-hover" id="product_table_for_order" style="width:100%">
                        <thead>
                        <tr>
                            <th class="cell-tight">Part Number</th>
                            <th>Product</th>
                            <th class="cell-tight">PU</th>
                            <th class="cell-tight">PU HQ</th>
                            <th class="text-center" style="width: 100px;">
                                <a href="/orders/{{$order->id}}" class="btn btn-xs"><i class="icon-check"> Done</i></a>
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
            $('#product_table_for_order').DataTable({
                "oLanguage": {

                    // "sSearch": "<i class='icon-search icon-large table-search-icon'></i>"

                },
                processing: true,
                serverSide: true,
                ajax: '{!! route('line_items/getdata',[$order->id]) !!}',
                columns: [
                    { data: 'product_code', name: 'product_code' },
                    { data: 'product_name', name: 'product_name' },
                    { data: 'pack_unit', name: 'pack_unit' },
                    { data: 'pack_unit_hq', name: 'pack_unit_hq' },
                    // {data: 'action', name: 'action', orderable: false, searchable: false}
                    {
                        // attach order id using jquery after the form being render in the view
                        data: 'action',
                        createdCell: function (td, cellData, rowData, row, col) {
                            var id= $("#order_id2").attr('data-item');
                            var input = $("<input>")
                                .attr("type", "hidden")
                                .attr("name", "order_id").val(id);
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
