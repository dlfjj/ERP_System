@extends('layouts.default')



@section('page-module-menu')

    @include('customers.top_menu')

@stop



@section('page-crumbs')

    <ul id="breadcrumbs" class="breadcrumb">

        <li>

            <i class="icon-home"></i>

            <a href="/dashboard">Dashboard</a>

        </li>

        <li>

            <a href="/customers">Customers</a>

        </li>

        <li>

            <a href="/customers/{{$customer->id}}">Details</a>

        </li>

        <li class="current">

            <a href="/customers/history/{{$customer->id}}" title="">History</a>

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

        </div>



        <ul class="page-stats">

            <li>

                <div class="summary">

                    <span>Status</span>

                    <h3>{{$customer->status}}</h3>

                </div>

            </li>

        </ul>

    </div>

@stop



@section('content')



    @if(has_role('orders'))

        <div class="row">

            <div class="col-md-12">

                <div class="widget box">

                    <div class="widget-header">

                        <h4><i class="icon-reorder"></i> Order History</h4>

                    </div>

                    <div class="panel-body">

                        <table class="table table-striped table-bordered table-hover" id="order_history_table" style="width:100%;">

                            <thead>

                            <tr>
                                <th>ID</th>

                                <th>Status</th>

                                <th>Date</th>

                                <th>C.O.N</th>

                                <th>Currency</th>

                                <th>Amount</th>

                                <th>Open</th>

                                <th>-</th>

                            </tr>

                            </thead>

                            <tbody>

                            </tbody>

                        </table>

                    </div>

                </div>

            </div>

        </div>

    @endif
@stop

@push('scripts')
    <script>
        // jquery getting data for purchase table
        $(function() {
            $('#order_history_table').DataTable({

                processing: true,
                serverSide: true,
                ajax: '{!! route('history/getdata',[$customer->id]) !!}',
                columns: [
                    { data: 'id', name: 'id' },
                    { data: 'name', name: 'order_status.name' },
                    { data: 'order_date', name: 'order_date' },
                    { data: 'customer_order_number', name: 'customer_order_number' },
                    { data: 'currency_code', name: 'currency_code' },
                    { data: 'total_gross', name: 'total_gross' },
                    { data: 'total_paid', name: 'total_paid' },
                    { data: 'action', name: 'action', orderable: false, searchable: false}
                ],
            });
        });
    </script>
@endpush

