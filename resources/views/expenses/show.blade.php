@extends('layouts.default')

@section('page-crumbs')
    <ul id="breadcrumbs" class="breadcrumb">
        <li>
            <i class="icon-home"></i>
            <a href="/">Dashboard</a>
        </li>
        <li>
            <a href="/expenses/" title="">Expenses</a>
        </li>
        <li class="current">
            <a href="/expenses/{{$expense->id}}" title="">Details</a>
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
                    <span>Amount</span>
                    <h3>{{$expense->amount}} {{$expense->currency_code}}</h3>
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
                    <h4><i class="icon-reorder"></i> Update Expense</h4>
                </div>
                <div class="widget-content">

                    {!! Form::model($expense,['class' => 'form-vertical row-border form-validate','method'=>'PATCH','action'=>['ExpenseController@update', $expense->id],'files' =>false]) !!}

                    {{--<form enctype="multipart/form-data" id="main" class="form-vertical row-border form-validate" action="ExpenseController@update  " method="PUT">--}}
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-2">
                                    {{ Form::text('date_created', $expense->date_created, ["class"=>"form-control datepicker"]) }}
                                    <input type="hidden" name="id" class="" value="{{ $expense->id}}">
                                    <span class="help-block">Date</span>
                                </div>
                                <div class="col-md-2">
                                    {{--<select name="account_id">--}}
                                        {{--<option value="{{$expense->account_id}}">{{$expense->account_id}}</option>--}}
                                    {{--</select>--}}
                                    {!! $select_accounts !!}
                                    <span class="help-block">Account Category</span>
                                </div>
                                <div class="col-md-2">
                                    {{ Form::select('bank_account', $select_bank_accounts, $expense->bank_account, ["class"=>"form-control"]) }}
                                    <span class="help-block">Account</span>
                                </div>
                                <div class="col-md-2">
                                    {{ Form::select('currency_code', $select_currency_codes, $expense->currency_code, ["class"=>"form-control"]) }}
                                    <span class="help-block">Currency</span>
                                </div>
                                <div class="col-md-2">
                                    <input type="text" name="amount" class="form-control" value="{{$expense->amount}}">
                                    <span class="help-block">Amount</span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-4">
                                    <input type="text" name="description" class="form-control" value="{{$expense->description}}">
                                    <span class="help-block">Description</span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-2">
                                    {{ Form::text('transaction_reference', $expense->transaction_reference, array("class"=>"form-control")) }}
                                    <span class="help-block">Transaction Ref</span>
                                </div>
                                <div class="col-md-2">
                                    {{ Form::text('purchase_id', "", array("class"=>"form-control")) }}
                                    <span class="help-block">Purchase ID</span>
                                </div>
                                <div class="col-md-2">
                                    {{ Form::text('order_id', "", array("class"=>"form-control")) }}
                                    <span class="help-block">Order ID</span>
                                </div>
                            </div>
                            <div class="form-actions">
                                <input type="submit" value="SAVE" class="btn btn-success pull-right">
                                <a href="/expenses" class="btn btn-default pull-right">CANCEL</a>
                            </div>
                        </div>
                {{--</form>--}}
            </div>
        </div>
    </div>

@stop