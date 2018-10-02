@extends('layouts.default')

@section('page-module-menu')
    @include('products.top_menu')
@stop

@section('page-crumbs')
    @include('products.bread_crumbs')
@stop

@section('page-header')
    @include('products.page_header')
@stop

@section('content')
    <div class="row">
        {{--{{ Form::open(array("url"=>"/products/destroy/$product->id","method"=>"post","class"=>"form-inline","id"=>"delete")) }}--}}
        {{--</form>--}}

        {{--<form class="form-inline" id="create" action="/products/create" method="POST">--}}
        {{--</form>--}}

        {{--<form class="form-inline" id="duplicate" action="/products/duplicate/{{$product->id}}" method="POST">--}}
        {{--</form>--}}

        <!--=== Vertical Forms ===-->
        <div class="col-md-12">
            <div class="widget box">
                <div class="widget-header">
                    <h4><i class="icon-reorder"></i> Product Details</h4>
                </div>
                <div class="widget-content">
                    {!! Form::open(['method'=>'PATCH','action'=>['SetupController@update', $product->id],'class'=>'form-vertical row-border form-validate','id'=>'main','enctype'=>'multipart/form-data']) !!}
                    {{--<form autocomplete="off" enctype="multipart/form-data" id="main" class="form-vertical row-border form-validate" action="/products/setup/{{$product->id}}" method="POST">--}}
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-2">
                                    <label class="control-label">Serial #</label>
                                    {{ Form::text('id', $product->id, array("class"=>"form-control","readonly")) }}
                                </div>
                                <div class="col-md-2">
                                    <label class="control-label">Part Number</label>
                                    {{ Form::text('', $product->product_code, array("class"=>"form-control","readonly")) }}
                                </div>
                                <div class="col-md-3">
                                    <label class="control-label">Show in Webshop / Pricelist</label>
                                    {{ Form::select('is_visible', array("0" => "No", "1" => "Yes"), $product->is_visible, array("class"=>"form-control")) }}
                                </div>
                                <div class="col-md-2">
                                    <label class="control-label">Webshop featured</label>
                                    {{ Form::select('is_featured', array("0" => "No", "1" => "Yes"), $product->is_featured, array("class"=>"form-control")) }}
                                </div>
                                <div class="col-md-2">
                                    <label class="control-label">Pricelist sort</label>
                                    {{ Form::text('pricelist_sort', $product->pricelist_sort, array("class"=>"form-control")) }}
                                </div>
                            </div>
                            <p>&nbsp;</p>
                            <div class="form-actions">
                                @if(has_role('products_edit'))
                                    <input type="submit" value="Save" class="btn btn-sm btn-success pull-right">
                                @endif
                            </div>
                        </div>
                    {{--</form>--}}
                    {{ Form::close() }}
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12 no-padding">
            <p class="record_status">Created: {{$product->created_at}} | Created by: {{$user_created}} | Updated: {{$product->updated_at}} | Updated by: {{$user_updated}} | <a href="/products/changelog/{{ $product->id }}">Changelog</a></p>
        </div>
    </div>

@stop
