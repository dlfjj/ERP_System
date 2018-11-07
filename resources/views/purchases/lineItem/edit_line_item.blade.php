@extends('layouts.default')

@section('page-crumbs')

    <ul id="breadcrumbs" class="breadcrumb">
        <li>
            <i class="icon-home"></i>
            <a href="/">Dashboard</a>
        </li>
        <li>
            <a href="/purchases/" title="">Purchases</a>
        </li>
        <li class="current">
            <a href="/purchases/{{$purchase->id}}" title="">Details</a>
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
                    <h4><i class="icon-reorder"></i> Update purchase Line Item </h4>
                </div>
                <div class="widget-content">
                    {!! Form::open(['action' => ["PurchaseController@getLineItemUpdate",$line_item->id],'id'=>'main', 'enctype' => 'multipart/form-data', 'method' => 'PATCH', 'class' => 'form-horizontal row-border form-validate', 'autocomplete' => 'off']) !!}
                    {{--<form autocomplete="off" enctype="multipart/form-data" id="main" class="form-horizontal row-border form-validate" action="/purchases/line-item-update/{{$line_item->id}}" method="POST">--}}
                        <div class="form-group">
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="col-md-12">
                                        <input type="hidden" name="purchase_id" class="" value="{{ $purchase->id}}">
                                        <input type="hidden" name="id" class="" value="{{ $line_item->id}}">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-2">
                                        <input type="text" name="sort_no" class="form-control" value="{{ $line_item->sort_no}}">
                                        <span class="help-block">Sort No</span>
                                    </div>
                                    <div class="col-md-2">
                                        <input type="text" name="" class="form-control" value="{{ $line_item->product->product_code }}" readonly>
                                        <span class="help-block">Part Number</span>
                                    </div>
                                    <div class="col-md-2">
                                        <input type="text" name="" class="form-control" value="{{ $line_item->product->pack_unit}}" readonly>
                                        <span class="help-block">Pack Unit</span>
                                    </div>
                                    <div class="col-md-2">
                                        <input type="number" name="quantity" class="form-control" value="{{ $line_item->quantity }}" step="1">
                                        <span class="help-block">Quantity</span>
                                    </div>
                                    <div class="col-md-2">
                                        <input type="number" name="gross_price" class="form-control" value="{{ round($line_item->gross_price,4) }}" step="any">
                                        <span class="help-block">Price (gross)</span>
                                    </div>
                                    <div class="col-md-2">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                {{ Form::textarea('', $line_item->product->product_name, array("rows"=>"2","cols"=>"5","class"=>"form-control","readonly")) }}
                                <span class="help-block">Line Item Description</span>
                            </div>
                            <div class="col-md-6">
                                {{ Form::textarea('remarks', $line_item->remarks, array("rows"=>"2","cols"=>"5","class"=>"form-control")) }}
                                <span class="help-block">Line Item Remarks</span>
                            </div>
                        </div>

                        <div class="form-actions">
                            <input type="submit" value="Save" class="btn btn-success pull-right">
                            <a href="/purchases/{{$purchase->id}}" class="btn btn-default pull-right">Cancel</a>
                            {{ Form::button('RESET', ['type' => 'reset', 'class' => 'btn btn-default pull-right'] )  }}
                        </div>
                    {{ Form::close() }}
                </div>
            </div>
        </div>
    </div>

@stop
