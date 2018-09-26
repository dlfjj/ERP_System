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
                    {{--<form autocomplete="off" enctype="multipart/form-data" id="main" class="form-vertical row-border form-validate" action="" method="POST">--}}
                        {!! Form::open(['method'=>'POST', 'action'=> ['OrderController@postPayments', $order->id], 'class'=>'form-vertical row-border form-validate','id'=>'main','enctype'=>'multipart/form-data','autocomplete'=>'off']) !!}

                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-2">
                                    <label class="control-label">Date</label>
                                    {{ Form::text('date_created', date("Y-m-d"), array("class"=>"form-control datepicker")) }}
                                    {{ Form::hidden('account_id', "13", array("class"=>"form-control")) }}
                                </div>
                                <div class="col-md-2">
                                    <label class="control-label">Transaction #</label>
                                    {{ Form::text('transaction_reference', "", array("class"=>"form-control")) }}
                                </div>
                                <div class="col-md-2">
                                    <label class="control-label">Remark</label>
                                    {{ Form::text('remark', "", array("class"=>"form-control")) }}
                                </div>
                                <div class="col-md-2">
                                    <label class="control-label">Currency</label>
                                    {{ Form::select('currency_code', $select_currency_codes, $order->currency_code, array("class"=>"form-control","readonly")) }}
                                </div>
                                <div class="col-md-2">
                                    <label class="control-label">Amount</label>
                                    {{ Form::text('amount', "", array("class"=>"form-control")) }}
                                </div>
                                <div class="col-md-2">
                                    <label class="control-label">Bank Charges</label>
                                    {{ Form::text('bank_charges', "", array("class"=>"form-control")) }}
                                </div>
                            </div>
                            <div class="form-actions">
                                <input type="submit" value="Record Payment" class="btn btn-sm btn-success pull-right">
                            </div>
                        </div>
                </div>
                {{--</form>--}}
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
                    {{--<form style="margin: 0px; padding: 0px;" action="/orders/{{$order->id}}/receive" method="POST">--}}
{{--                        {!! Form::open(['method'=>'POST', 'action'=> ['OrderController@postPayments', $order->id], 'style'=>'margin: 0px; padding: 0px']) !!}--}}

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
                                            {{$payment->bank_charges }}
                                        </td>
                                        <td class="align-center">
{{--                                            <a href="/orders/payment-delete/{{$payment->id}}" class="btn btn-xs btn-danger conf">X</a>--}}
                                            {{ Form::submit('X', ['class' => 'btn btn-xs btn-danger']) }}
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

@stop
