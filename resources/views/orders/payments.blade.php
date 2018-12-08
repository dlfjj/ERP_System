@extends('layouts.default')

@section('page-module-menu')
    @include('orders.top_menu')
@stop

@section('page-crumbs')
    @include('orders.bread_crumbs')
@stop

@section('page-header')
    @include('orders.page_header')
@stop


@section('content')

    <div class="row">
        <!--=== Vertical Forms ===-->
        <div class="col-md-12">
            <div class="widget box">
                <div class="widget-header">
                    <h4><i class="icon-reorder"></i> Record new payment</h4>
                </div>
                <div class="widget-content">
                        {!! Form::open(['method'=>'POST', 'action'=> ['OrderController@postPayments', $order->id], 'class'=>'form-vertical row-border form-validate','id'=>'main','enctype'=>'multipart/form-data','autocomplete'=>'off']) !!}
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-2">
                                    <label class="control-label">Date</label>
                                    {{ Form::text('date_created', date("Y-m-d"), array("class"=>"form-control datepicker")) }}
                                    {{ Form::hidden('account_id', "13", array("class"=>"form-control")) }}
                                </div>
                                <div class="col-md-2">
                                    <label class="control-label">Currency</label>
                                    {{ Form::select('currency_code', $select_currency_codes, $order->currency_code, array("class"=>"form-control","readonly")) }}
                                </div>
                                <div class="col-md-2">
                                    <label class="control-label">Amount</label>
                                    {{ Form::input('number','amount', "", array("class"=>"form-control","step"=>"1")) }}
                                </div>
                                <div class="col-md-2">
                                    <label class="control-label">Bank Charges</label>
                                    {{ Form::input('number','bank_charges', "", array("class"=>"form-control","step"=>"1")) }}
                                </div>
                                <div class="col-md-2">
                                    <label class="control-label">Transaction #</label>
                                    {{ Form::text('transaction_reference', "", array("class"=>"form-control")) }}
                                </div>
                                <div class="col-md-2">
                                    <label class="control-label">Remark</label>
                                    {{ Form::text('remark', "", array("class"=>"form-control")) }}
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-3">
                                    <label class="control-label">Payment Type</label>
                                    {{ Form::select('bank_account', $select_bank_accounts, "", array("class"=>"form-control")) }}
                                </div>
                                {{--<div class="col-md-3">--}}
                                {{--<label class="control-label">Account Categories</label>--}}

                                {{--{!! $select_accounts !!}--}}
                                {{--</div>--}}
                            </div>
                            <div class="form-actions">
                                <input type="submit" value="Record Payment" class="btn btn-sm btn-success pull-right">
                            </div>
                        </div>
                </div>
                {!! Form::close() !!}

            </div>
        </div>
        <!-- /Vertical Forms -->

        <!--=== Vertical Forms ===-->
        <div class="col-md-12">
            <div class="widget box">
                <div class="widget-header">
                    <h4><i class="icon-reorder"></i> Payment history</h4>
                </div>
                <div class="widget-content no-padding">
                        <table class="table table-hover table-bordered table-highlight-head">
                            <thead>
                            <tr>
                                <th>Date</th>
                                <th>By</th>
                                <th>Type</th>
                                <th>Transaction Ref#</th>
                                <th>Remark</th>
                                <th>CUR</th>
                                <th>Amount</th>
                                <th>Bank Charges</th>
                                <th></th>
                            </tr>
                            </thead>
                            <tbody>
                            @if(count($order->payments)>0)
                                @foreach($order->payments as $payment)
                                    {{ Form::open(['method' => 'DELETE', 'action' => ['OrderController@deletePayment', $payment->id],'style'=>'margin: 0px; padding: 0px']) }}

                                    <tr>
                                        <td>
                                            {{$payment->date}}
                                        </td>
                                        <td>
                                            {{$payment->createdBy->username }}
                                        </td>
                                        <td>
                                            {{$payment->type }}
                                        </td>
                                        <td>
                                            {{$payment->transaction_reference}}
                                        </td>
                                        <td>
                                            {{$payment->remark}}
                                        </td>
                                        <td>
                                            {{$payment->currency_code}}
                                        </td>
                                        <td>
                                            {{$payment->amount}}
                                        </td>
                                        <td>
                                            {{$payment->bankCharge->amount }}
                                        </td>
                                        <td class="align-center">
                                            {{ Form::submit('DELETE', ['class' => 'btn btn-xs btn-danger']) }}
                                        </td>
                                    </tr>
                                    {!! Form::close() !!}
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="7">Nothing found</td>
                                </tr>
                            @endif
                            </tbody>
                        </table>
                    {{--</form>--}}

                </div>
            </div>
        </div>
        <!-- /Vertical Forms -->
    </div>



    {{--<div class="modal fade" id="modal_Transaction">--}}
        {{--<div class="modal-dialog">--}}
            {{--<div class="modal-content">--}}
                {{--<div class="modal-header">--}}
                    {{--<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>--}}
                    {{--<h4 class="modal-title">Record New Transaction</h4>--}}
                {{--</div>--}}
                {{--<form autocomplete="off" enctype="multipart/form-data" id="customer_contact" class="form-validate1" action="ExpenseController@create" method="GET">--}}
                {{--{!! Form::open(['method'=>'POST','action'=>['ExpenseController@store'],'files' =>false], array('enctype'=>'multipart/form-data','id'=>'customer_contact','class' => 'form-validate1')) !!}--}}

                {{--<div class="modal-body">--}}
                    {{--<div class="form-group">--}}
                        {{--<div class="row">--}}
                            {{--<div class="col-md-6">--}}
                                {{--{{ Form::text('date_created', date("Y-m-d"), array("class"=>"form-control datepicker")) }}--}}
                                {{--<span class="help-block">Date</span>--}}
                                {{--</div>--}}
                                {{--<div class="col-md-6">--}}
                                {{--<div style="text-overflow: ellipsis;">--}}
                                {{--                                        {{ Form::select('cash_flow_type', ['income' => 'Income', 'expense' => 'Expense'], 'income', array("class"=>"dropdown-toggle form-control")) }}--}}

                                {{--                                        {{ Form::select('account_id', $account_name, null, array("class"=>"dropdown-toggle form-control")) }}--}}
                                {{--</div>--}}
                                {{--<span class="help-block">Cash Flow Type</span>--}}
                                {{--</div>--}}
                            {{--</div>--}}
                        {{--</div>--}}
                        {{--<div class="form-group">--}}
                            {{--<div class="row">--}}
                                {{--<div class="col-md-6">--}}
                                    {{--{{ Form::select('bank_account', $select_bank_accounts, "", array("class"=>"form-control")) }}--}}
                                    {{--<span class="help-block">Account</span>--}}
                                {{--</div>--}}
                                {{--<div class="col-md-6">--}}
                                    {{--{!! $select_accounts !!}--}}
                                    {{--<span class="help-block">Account Categories</span>--}}
                                {{--</div>--}}
                            {{--</div>--}}
                        {{--</div>--}}
                        {{--<div class="form-group">--}}
                            {{--<div class="row">--}}
                                {{--<div class="col-md-6">--}}
                                    {{--{{ Form::select('currency_code', $select_currency_codes, Auth::user()->company->currency_code, array("class"=>"form-control")) }}--}}
                                    {{--<span class="help-block">Currency</span>--}}
                                {{--</div>--}}
                                {{--<div class="col-md-6">--}}
                                    {{--<input type="text" name="amount" class="form-control" value="">--}}
                                    {{--<span class="help-block">Amount</span>--}}
                                {{--</div>--}}
                            {{--</div>--}}
                        {{--</div>--}}
                        {{--<div class="form-group">--}}
                            {{--<div class="row">--}}
                                {{--<div class="col-md-6">--}}
                                    {{--{{ Form::text('transaction_reference', "", array("class"=>"form-control")) }}--}}
                                    {{--<span class="help-block">Transaction Reference</span>--}}
                                {{--</div>--}}
                                {{--<div class="col-md-6">--}}
                                    {{--<input type="text" name="description" class="form-control" value="">--}}
                                    {{--<span class="help-block">Description</span>--}}
                                {{--</div>--}}
                            {{--</div>--}}
                        {{--</div>--}}

                    {{--</div>--}}
                    {{--<div class="modal-footer">--}}
                        {{--<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>--}}
                        {{--<input type="submit" class="btn btn-primary" value="Submit">--}}
                    {{--</div>--}}
                    {{--{!! Form::close() !!}--}}
                {{--</div>--}}
            {{--</div>--}}
        {{--</div>--}}
    {{--</div>--}}


@stop
