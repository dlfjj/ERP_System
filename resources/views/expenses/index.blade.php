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
                <a class="btn btn-success btn-lg" data-toggle="modal" href="#modal_expense" style="margin-top: 20px;"><i class="icon-plus-sign"></i> Add Expense</a>
            @endif
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
            <div class="widget box">
                <div class="widget-header">
                    <h4><i class="icon-reorder"></i>Expense Index</h4>
                    <div class="toolbar no-padding">
                        <div class="btn-group">
                            <span class="btn btn-xs widget-collapse"><i class="icon-angle-down"></i></span>
                        </div>
                    </div>
                </div>
                <div class="widget-content no-padding">
                    <table class="table table-striped table-bordered table-hover table-chooser datatable" data-dataTable='{"bServerSide": true, "sAjaxSource": "/expenses/dt-index", "aaSorting": [[ 0, "desc" ]]}'>
                        <thead>
                        <tr>
                            <th>Serial</th>
                            <th>Created</th>
                            <th>By</th>
                            <th>Account</th>
                            <th>CUR</th>
                            <th>Amount</th>
                            <th>Description</th>
                            <th></th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($expenses as $expense)
                            <tr>
                                <td>
                                    {{$expense->id}}
                                </td>
                                <td>
                                    {{$expense->date_created}}
                                </td>
                                <td>
                                    {{$expense->username}}
                                </td>
                                <td>
                                    {{$expense->name}}
                                </td>
                                <td>
                                    {{$expense->currency_code}}
                                </td>
                                <td>
                                    {{$expense->amount}}
                                </td>
                                <td>
                                    {{$expense->description}}
                                </td>
                                <td>
                                    <a href="/expenses/{{ $expense->id }}" class="bs-tooltip" title="View"><i class="icon-search"></i></a>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modal_expense">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title">Record new Expense</h4>
                </div>
                {{--<form autocomplete="off" enctype="multipart/form-data" id="customer_contact" class="form-validate1" action="ExpenseController@create" method="GET">--}}
                {!! Form::open(['method'=>'POST','action'=>['ExpenseController@store'],'files' =>false], array('enctype'=>'multipart/form-data','id'=>'customer_contact','class' => 'form-validate1')) !!}

                <div class="modal-body">
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-6">
                                    {{ Form::text('date_created', date("Y-m-d"), array("class"=>"form-control datepicker")) }}
                                    <span class="help-block">Date</span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-6">
                                    {{ Form::select('bank_account', $select_bank_accounts, "", array("class"=>"form-control")) }}
                                    <span class="help-block">Account</span>
                                </div>
                                <div class="col-md-6">
                                    <div style="text-overflow: ellipsis;">
                                        {{ Form::select('account_id', $account_name,null, array("class"=>"dropdown-toggle form-control",'placeholder'=>'Please select ...')) }}
                                    </div>
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

    <script>
        $(document).ready(function(){
            $('#expense_table').dataTable();
        });
    </script>
    <style>
        #wgtmsr{
            width:150px;
        }
    </style>


@stop