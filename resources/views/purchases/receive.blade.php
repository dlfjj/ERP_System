@extends('layouts.default')

<?php

/*
     * PASS         = QUANTITY PASSED TO WH
     * REJECT       = QUANTITY REJECTED   INTO QC HOLDING
     * RECONCILE    = JUST DOWNADJUST OPEN QUANTITY IN CASE VENDOR DONT WANT TO DELIVER
     * OPEN         = ORDERED - PASSED - PASSED NP - REWORKED - REWORKED NP - RECONCILED
 */

$select_status = array();

if(has_role('purchases_pass_goods')){
    $select_status["PASSED"] = "PASS";
    $select_status["REJECTED"] = "REJECT";
    $select_status["RECONCILED"] = "RECONCILE";
}
?>

@section('page-module-menu')
    @include('purchases.top_menu')
@stop

@section('page-crumbs')
    @include('purchases.bread_crumbs')
@stop

@section('page-header')
    @include('purchases.page_header')
@stop

@section('content')

    @if($purchase->date_required > date("Y-m-d"))
        <div class="row">
            <div class="col-md-12">
                <div class="alert alert-warning fade in">
                    <i class="icon-remove close" data-dismiss="alert"></i>
                    <strong>Warning!</strong> Required Date later than todays Date!
                </div>
            </div>
        </div>
    @endif

    <div class="row">
        <div class="col-md-12">
            <div class="widget box">
                <div class="widget-header">
                    <h4><i class="icon-reorder"></i> P.O LINE ITEMS</h4>
                    <div class="toolbar no-padding">
                        <div class="btn-group">
                        </div>
                    </div>
                </div>
                <div class="widget-content no-padding">
                    {{--<form autocomplete="off" class="" style="margin: 0px; padding: 0px;" action="" method="POST">--}}
                    {!! Form::open(['method'=>'POST', 'action'=> ['PurchaseController@postReceive', $purchase->id],'autocomplete'=>'off','style'=>'margin: 0px; padding: 0px;']) !!}
                        <table class="table table-bordered table-highlight-head" style="border-bottom: 1px solid #CCC;">
                            <thead>
                            <tr>
                                <th style="width: 110px;" class="no-wrap">DATE</th>
                                <th style="">REMARK</th>
                                <th style="width: 1%;" class="no-wrap text-right"></th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td>
                                    {{ Form::text('date', date('Y-m-d'), array("class"=>"form-control datepicker")) }}
                                </td>
                                <td>{{ Form::text('remark', "", array("class"=>"form-control","tabindex"=>2)) }}</td>
                                <td>
                                    @if(has_role('purchases_edit'))
                                        <input type="submit" value="Receive" class="btn btn-sm btn-success pull-right">
                                    @else
                                        -
                                    @endif
                                </td>
                            </tr>
                            </tbody>
                        </table>

                        <table class="table table-hover table-bordered table-highlight-head">
                            <thead>
                            <tr>
                                <th style="width: 1%;" class="no-wrap">PARTNUMBER</th>
                                <th style="width: 1%;">ORDERED</th>
                                <th style="width: 1%;">DELIVERED</th>
                                <th style="width: 1%;">RECONCILED</th>
                                <th style="width: 1%;">OPEN</th>
                                <th style="width: 1%;">TO PASS</th>
                                <th style="width: 1%;">TO RECONCILE</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            $product_ids = array();
                            ?>
                            @if(count($purchase->items)>0)
                                @foreach($purchase->items as $purchase_item)
                                    <tr>
                                        <td class="no-wrap">
                                            <a href="/products/show/{{ $purchase_item->product_id }}">{{$purchase_item->product->product_code }}</a>
                                            {{ Form::hidden('purchase_item_ids[]', $purchase_item->id, array("class"=>"form-control")) }}
                                        </td>
                                        <td class="no-wrap">{{ $purchase_item->getQuantityOrdered() }}</td>
                                        @if($purchase_item->product->track_stock)
                                            <td>{{ $purchase_item->getQuantityDelivered() }}</td>
                                            <td>{{ $purchase_item->getQuantityReconciled() }}</td>
                                            <td>{{ $purchase_item->getQuantityOpen() }}</td>
                                            <td>{{ Form::text('delivered_quantities[]', "", array("class"=>"form-control","tabindex" => 3)) }}</td>
                                            <td>{{ Form::text('reconciled_quantities[]', "", array("class"=>"form-control","tabindex" => 4)) }}</td>
                                        @else
                                            <td colspan="7">
                                                No Stock Tracking for this Line Item
                                                {{ Form::hidden('quantities[]', "", array("class"=>"form-control","tabindex" => 3)) }}
                                            </td>
                                        @endif
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="9">Nothing found</td>
                                </tr>
                            @endif
                            </tbody>
                        </table>
                    {{--</form>--}}
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
        <!-- /Vertical Forms -->



        <!--=== Vertical Forms ===-->
        <div class="col-md-12">
            <div class="widget box">
                <div class="widget-header">
                    <h4><i class="icon-reorder"></i> DELIVERIES FOR THIS P.O</h4>
                </div>
                <div class="widget-content no-padding">
                    <form style="margin: 0px; padding: 0px;" action="/purchases/receive/{{$purchase->id}}" method="POST">
                        <table class="table table-hover table-bordered table-highlight-head">
                            <thead>
                            <tr>
                                <th>-</th>
                                <th class="no-wrap">TIMESTAMP</th>
                                <th class="no-wrap">PARTNUMBER</th>
                                <th>DESCRIPTION</th>
                                <th>DELIVERED</th>
                                <th>RECONCILED</th>
                                <th></th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            $old_uid = "";
                            ?>
                            @foreach($purchase->deliveries as $delivery)
                                <tr>
                                    @if($delivery->uid != $old_uid)
                                        <?php $old_uid = $delivery->uid; ?>
                                        <td style="width: 1%; white-space:nowrap;">
                                            <a href="/pdf/purchases-goods-receipt/{{$delivery->uid}}" class="btn btn-sm btn-default">Receipt</a>
                                        </td>
                                    @else
                                        <td style="width: 1%; white-space:nowrap;">
                                        </td>
                                    @endif
                                    <td style="width: 1%; white-space:nowrap;">
                                        {{$delivery->created_at}}
{{--                                        <p style="font-size: 11px;">by: {{User::where('id',$delivery->created_by)->first()->username}}</p>--}}
                                    </td>
                                    <td>{{$delivery->product->product_code }}</td>
                                    <td>
                                        {{$delivery->product->product_name}}<br />
                                        @if($delivery->remarks != "")
                                            <p style="font-size: 11px;">Remark: {{ $delivery->remarks }}</p>
                                        @endif
                                    </td>
                                    <td>{{ $delivery->delivered }}</td>
                                    <td>{{ $delivery->reconciled }}</td>
                                    <td class="text-center">
                                        @if(has_role('admin') ||  $delivery->created_by == Auth::user()->id)
                                            <a href="/purchases/delivery-delete/{{$delivery->id}}" class="btn btn-xs btn-danger">X</a>
                                        @else
                                            -
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </form>
                </div>
            </div>
        </div>
    </div>

@stop
