@extends('layouts.default')

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
            <a href="/orders/show/{{$order->id}}" title="">Details</a>
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
                    <h3>{{$order->currency_code}} {{$order->gross_total}}</h3>
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
                    <h4><i class="icon-reorder"></i> Update Order Order Line Item</h4>
                </div>
                <div class="widget-content">
                    {!! Form::open(['action' => ["OrderController@getLineItemUpdate",$line_item->id],'id'=>'main', 'enctype' => 'multipart/form-data', 'method' => 'PATCH', 'class' => 'form-horizontal row-border form-validate', 'autocomplete' => 'off']) !!}
                    {{--<form autocomplete="off" enctype="multipart/form-data" id="main" class="form-horizontal row-border form-validate" action="" method="POST">--}}
                        <div class="form-group">
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="col-md-12">
                                        <input type="hidden" name="order_id" class="" value="{{ $order->id}}">
                                        <input type="hidden" name="id" class="" value="{{ $line_item->id}}">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-2">
                                        <input type="text" name="line_no" class="form-control" value="{{ $line_item->line_no }}" readonly>
                                        <span class="help-block">Sort No</span>
                                    </div>
                                    <div class="col-md-2">
                                        <input type="text" name="" class="form-control" value="{{ $product_code }}" readonly>
                                        <span class="help-block">Part Number</span>
                                    </div>
                                    <div class="col-md-2">
                                        <input type="text" name="quantity" class="form-control" value="{{ $line_item->quantity }}">
                                        <span class="help-block">Quantity</span>
                                    </div>
                                    <div class="col-md-2">
                                        <input type="text" name="unit_price_net" class="form-control" value="{{ $line_item->unit_price_net }}">
                                        <span class="help-block">Price</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                {{ Form::textarea('', $product_name, array("rows"=>"2","cols"=>"5","class"=>"form-control","readonly")) }}
                                <span class="help-block">Line Item Description</span>
                            </div>
                            <div class="col-md-6">
                                <input type="text" name="remark" class="form-control" value="{{ $line_item->remark}}">
                                <span class="help-block">Line Item Remarks</span>
                            </div>
                        </div>
                        <div class="form-actions">
                            <div class="btn-group">
                                <input type="reset" class="btn btn-default pull-right" value="RESET">
                                <a href="/orders/{{$order->id}}" class="btn btn-default pull-right">Back</a>
                            </div>
                            <input type="submit" value="SAVE" class="btn btn-success pull-right">
{{--                            {{ Form::button('RESET', ['type' => 'reset', 'class' => 'btn btn-default pull-right'] )  }}--}}

                        </div>
                    {{ Form::open() }}
                </div>
                {{--</form>--}}
            </div>
        </div>
    </div>

@stop
