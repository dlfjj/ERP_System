@extends('layouts.default')

@section('page-module-menu')

    <li><a href="/expenses">Expenses</a></li>
    @if(has_role('expenses_export'))
    @endif

@stop
@section('page-crumbs')
    <ul id="breadcrumbs" class="breadcrumb">
        <li>
            <i class="icon-home"></i>
            <a href="/">Dashboard</a>
        </li>
        <li class="current">
            <a href="/expenses/" title="">Expenses</a>
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
            @if(has_role('expenses_edit'))
                <a class="btn btn-success btn" data-toggle="modal" href="#modal_Transaction"><i class="icon-plus-sign"></i> Add Transaction</a>
            @endif

            {{--@if(has_role('expenses_edit'))--}}
                {{--<a class="btn btn-info btn" data-toggle="modal" href="#modal_income"><i class="icon-plus-sign"></i> Add Income</a>--}}
            {{--@endif--}}
        </div>
        <ul class="page-stats">
            <li>
                <div class="summary">
                    <span>Date</span>
                    <h3>{{ date("Y-m-d") }}</h3>
                </div>
            </li>
        </ul>
    </div>
@stop

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                    <div class="panel-heading"><i class="icon-reorder"></i> Transaction</div>
                <div class="panel-body">
                    <table class="table table-bordered table-hover table-chooser" id="expense_table" style="width: 100%;">
                        <thead>
                        <tr>
                            <th>Serial</th>
                            <th>Type</th>
                            <th>Created</th>
                            <th>By</th>
                            <th>Account</th>
                            <th>CUR</th>
                            <th>Amount</th>
                            <th>Description</th>
                            <th></th>
                        </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modal_Transaction">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title">Record New Transaction</h4>
                </div>
                {{--<form autocomplete="off" enctype="multipart/form-data" id="customer_contact" class="form-validate1" action="ExpenseController@create" method="GET">--}}
                {!! Form::open(['method'=>'POST','action'=>['ExpenseController@store'],'files' =>false], array('enctype'=>'multipart/form-data','id'=>'customer_contact','class' => 'form-validate1')) !!}

                <div class="modal-body">
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-6">
                                    {{ Form::text('date_created', date("Y-m-d"), array("class"=>"form-control datepicker")) }}
                                    <span class="help-block">Date</span>
                                {{--</div>--}}
                                {{--<div class="col-md-6">--}}
                                    {{--<div style="text-overflow: ellipsis;">--}}
{{--                                        {{ Form::select('cash_flow_type', ['income' => 'Income', 'expense' => 'Expense'], 'income', array("class"=>"dropdown-toggle form-control")) }}--}}

{{--                                        {{ Form::select('account_id', $account_name, null, array("class"=>"dropdown-toggle form-control")) }}--}}
                                    {{--</div>--}}
                                    {{--<span class="help-block">Cash Flow Type</span>--}}
                                {{--</div>--}}
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-6">
                                    {{ Form::select('bank_account', $select_bank_accounts, "", array("class"=>"form-control")) }}
                                    <span class="help-block">Account</span>
                                </div>
                                <div class="col-md-6">
                                    {!! $select_accounts !!}
                                    <span class="help-block">Account Categories</span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-6">
                                    {{ Form::select('currency_code', $select_currency_codes, Auth::user()->company->currency_code, array("class"=>"form-control")) }}
                                    <span class="help-block">Currency</span>
                                </div>
                                <div class="col-md-6">
                                    <input type="text" name="amount" class="form-control" value="">
                                    <span class="help-block">Amount</span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-6">
                                    {{ Form::text('transaction_reference', "", array("class"=>"form-control")) }}
                                    <span class="help-block">Transaction Reference</span>
                                </div>
                                <div class="col-md-6">
                                    <input type="text" name="description" class="form-control" value="">
                                    <span class="help-block">Description</span>
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <input type="submit" class="btn btn-primary" value="Submit">
                    </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>
    </div>
@stop

@push('styles')
    <style>
        td.red {
            background-color: #e97278;
        }
    </style>
@endpush

@push('scripts')
    <script>
        // jquery getting data for purchase table
        $(function() {
                $('#expense_table').DataTable({
                // "oLanguage": {
                    // "sSearch": "<i class='icon-search icon-large table-search-icon'></i>",
                    // "oPaginate": {
                    //     "sNext": "<i class='icon-chevron-right icon-large'></i>",
                    //     "sPrevious": "<i class='icon-chevron-left icon-large'></i>",
                        // "sFirst ": "<i class='icon-backward icon-large'></i>"
                    // }
                // },
                "pagingType": "full_numbers",
                processing: true,
                serverSide: false,
                ajax: '{!! route('expenses/getdata') !!}',
                columns: [
                    { data: 'id', name: 'id' },
                    { data: 'type', name: 'type' },
                    { data: 'date', name: 'date' },
                    { data: 'username', name: 'username' },
                    { data: 'account', name: 'account' },
                    { data: 'cur', name: 'cur' },
                    { data: 'amount', name: 'amount' },
                    { data: 'description', name: 'description' },
                    { data: 'action', name: 'action', orderable: false, searchable: false}
                ],
                "createdRow": function( row, data, dataIndex){
                    if( data.type ===  "Expense"){
                        $(row).find('td:eq(1)').css('background-color', '#e97278');
                        // $('td',row).addClass('red');
                    }else if(data.type ===  "Bank"){
                        $(row).find('td:eq(1)').css('background-color', '#8baeff');
                    }
                }

            });
        });
    </script>
@endpush