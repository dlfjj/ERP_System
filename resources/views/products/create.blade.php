@extends('layouts.default')


@section('page-crumbs')

    <ul id="breadcrumbs" class="breadcrumb">

        <li>

            <i class="icon-home"></i>

            <a href="/dashboard">Dashboard</a>

        </li>

        <li>

            <a href="/products" title="">Products</a>

        </li>

        <li class="current">

            <a href="#" title="">Create New Products</a>

        </li>

    </ul>



    <ul class="crumb-buttons">

        <li>

            <a href="javascript:void(0);" title=""><i class="icon-calendar"></i><span><?=date('F d, Y \(\K\W:W) H:i:s');?></span></a>

        </li>
    </ul>

@stop

@section('content')

    <div class="row">

        <!--=== Vertical Forms ===-->

        <div class="col-md-12">

            <div class="widget box">

                <div class="widget-header">

                    <h4><i class="icon-reorder"></i> Product Details</h4>

                </div>

                <div class="widget-content">
                    {!! Form::open(['method'=>'POST','action'=>['ProductController@store'],'files' =>false], array('enctype'=>'multipart/form-data','id'=>'main','class' => 'form-vertical row-border form-validate','autocomplete'=>'off')) !!}

                                    <div class="form-group">

                                        <div class="row">

                                            <div class="col-md-2">

                                                <label class="control-label">Serial #</label>

                                                {{ Form::text('id', "", array("class"=>"form-control","readonly")) }}

                                            </div>

                                            <div class="col-md-2">

                                                <label class="control-label">Part Number</label>

                                                {{ Form::text('product_code', "", array("class"=>"form-control")) }}

                                            </div>

                                            <div class="col-md-2">

                                                <label class="control-label">MPN</label>

                                                {{ Form::text('mpn', "", array("class"=>"form-control")) }}

                                            </div>

                                            <div class="col-md-2">
                                                <label class="control-label">Category</label>
                                                {!! $select_categories !!}
                                            </div>

                                            <div class="col-md-3">

                                            </div>

                                            <div class="col-md-1 text-right">

                                                {{--@if($product->picture != "")--}}

                                                    {{--<img src="/products/view-main-image/{{$product->id}}" />--}}

                                                {{--@else--}}

                                                    {{--<img src="http://placehold.it/100x100">--}}

                                                {{--@endif--}}

                                            </div>

                                        </div>

                                    </div>

                                    <div class="form-group">

                                        <div class="row">

                                            <div class="col-md-4">

                                                <label class="control-label">Short Product Name</label>

                                                {{ Form::text('product_name', "", array("class"=>"form-control")) }}

                                            </div>

                                            <div class="col-md-4">

                                                <label class="control-label">Localized Product Name</label>

                                                {{ Form::text('product_name_local', "", array("class"=>"form-control")) }}

                                            </div>

                                            <div class="col-md-4">

                                                <label class="control-label">Remarks</label>

                                                {{ Form::textarea('remarks', "", array("rows"=>"3","cols"=>"5","class"=>"form-control")) }}

                                            </div>

                                        </div>

                                    </div>

                                    <div class="form-group">

                                        <div class="row">

                                            <div class="col-md-2">

                                                <label class="control-label">UOM</label>

                                                {{ Form::select('uom', $select_uom, "", array("class"=>"form-control")) }}

                                            </div>

                                            <div class="col-md-2">

                                                <label class="control-label">Size</label>

                                                {{ Form::text('size', "", array("class"=>"form-control")) }}

                                            </div>

                                            <div class="col-md-2">

                                                <label class="control-label">CSC #</label>

                                                {{ Form::text('commodity_code',"", array("class"=>"form-control")) }}

                                            </div>

                                            <div class="col-md-2">

                                                <label class="control-label">Manufacturer</label>

                                                {{ Form::select('manufacturer', $select_manufacturer, "", array("class"=>"form-control")) }}

                                            </div>

                                            <div class="col-md-2">

                                                <label class="control-label">Unit Weight</label>

                                                {{ Form::text('weight', "", array("class"=>"form-control")) }}

                                            </div>

                                            <div class="col-md-2">

                                                <label class="control-label">MOQ</label>

                                                {{ Form::text('moq', "", array("class"=>"form-control")) }}

                                            </div>

                                        </div>

                                    </div>

                                    <div class="form-group">

                                        <div class="row">

                                            <div class="col-md-2">

                                                <label class="control-label">Origin</label>

                                                {{ Form::select('origin', $select_origin , "", array("class"=>"form-control")) }}

                                            </div>

                                            <div class="col-md-2">

                                            </div>

                                            <div class="col-md-2">

                                            </div>

                                            <div class="col-md-2">

                                            </div>

                                            <div class="col-md-2">

                                            </div>

                                            <div class="col-md-2">

                                                {{ Form::label('user_id', "Status") }}

                                                {{ Form::select('status', array('Active' => 'Active','Inactive' => 'Inactive', 'Draft' => 'Draft'), "", array("class"=>"form-control")) }}

                                            </div>

                                        </div>

                                    </div>

                                    <div class="form-group">

                                        <div class="row">

                                            <div class="col-md-2">

                                                <label class="control-label">Pack Unit</label>

                                                {{ Form::text('pack_unit', "", array("class"=>"form-control")) }}

                                            </div>

                                            <div class="col-md-2">

                                                <label class="control-label">PU per Pallet</label>

                                                {{ Form::text('units_per_pallette', "", array("class"=>"form-control")) }}

                                            </div>

                                            <div class="col-md-2">

                                                <label class="control-label">PU Nt Weight</label>

                                                {{ Form::text('pack_unit_net_weight', "", array("class"=>"form-control")) }}

                                            </div>

                                            <div class="col-md-2">

                                                <label class="control-label">PU Gr Weight</label>

                                                {{ Form::text('pack_unit_gross_weight', "", array("class"=>"form-control")) }}

                                            </div>

                                            <div class="col-md-2">

                                                <label class="control-label">Pallet Nt Weight</label>

                                                {{ Form::text('pallet_net_weight', "", array("class"=>"form-control")) }}

                                            </div>

                                        </div>

                                    </div>



                                    <div class="form-group">

                                        <div class="row">

                                            <div class="col-md-2">

                                                <label class="control-label">HQ Pack available</label>

                                                {{ Form::select('is_hq_pack', array('0' => 'No','1' => 'Yes'), "", array("class"=>"form-control")) }}

                                            </div>

                                            <div class="col-md-2">

                                                <label class="control-label">Ctn Width (cm)</label>

                                                {{ Form::text('carton_size_w', "", array("class"=>"form-control")) }}

                                            </div>

                                            <div class="col-md-2">

                                                <label class="control-label">Ctn Depth (cm)</label>

                                                {{ Form::text('carton_size_d', "", array("class"=>"form-control")) }}

                                            </div>

                                            <div class="col-md-2">

                                                <label class="control-label">Ctn Height (cm)</label>

                                                {{ Form::text('carton_size_h', "", array("class"=>"form-control")) }}

                                            </div>

                                            <div class="col-md-2">

                                                <label class="control-label">Pallet Size</label>

                                                {{ Form::text('pallet_size', "", array("class"=>"form-control")) }}

                                            </div>

                                        </div>

                                    </div>

                                        <div class="form-group">

                                            <div class="row">

                                                <div class="col-md-2">

                                                    <label class="control-label">Pack Unit HQ</label>

                                                    {{ Form::text('pack_unit_hq', "", array("class"=>"form-control")) }}

                                                </div>

                                                <div class="col-md-2">

                                                    <label class="control-label">PU per Pallet HQ</label>

                                                    {{ Form::text('units_per_pallette_hq',"", array("class"=>"form-control")) }}

                                                </div>

                                                <div class="col-md-2">

                                                    <label class="control-label">PU Nt Weight HQ</label>

                                                    {{ Form::text('pack_unit_net_weight_hq', "", array("class"=>"form-control")) }}

                                                </div>

                                                <div class="col-md-2">

                                                    <label class="control-label">PU Gr Weight HQ</label>

                                                    {{ Form::text('pack_unit_gross_weight_hq', "", array("class"=>"form-control")) }}

                                                </div>

                                                <div class="col-md-2">

                                                    <label class="control-label">Pallet Nt Weight HQ</label>

                                                    {{ Form::text('pallet_net_weight_hq', "", array("class"=>"form-control")) }}

                                                </div>

                                            </div>

                                        </div>



                                        <div class="form-group">

                                            <div class="row">

                                                <div class="col-md-2">

                                                    <label class="control-label">HQ MPN</label>

                                                    {{ Form::text('mpn_hq', "", array("class"=>"form-control")) }}

                                                </div>

                                                <div class="col-md-2">

                                                    <label class="control-label">HQ Ctn Width (cm)</label>

                                                    {{ Form::text('carton_size_w_hq', "", array("class"=>"form-control")) }}

                                                </div>

                                                <div class="col-md-2">

                                                    <label class="control-label">HQ Ctn Depth (cm)</label>

                                                    {{ Form::text('carton_size_d_hq', "", array("class"=>"form-control")) }}

                                                </div>

                                                <div class="col-md-2">

                                                    <label class="control-label">HQ Ctn Height (cm)</label>

                                                    {{ Form::text('carton_size_h_hq', "", array("class"=>"form-control")) }}

                                                </div>

                                                <div class="col-md-2">

                                                    <label class="control-label">HQ Pallet Size</label>

                                                    {{ Form::text('pallet_size_hq',"", array("class"=>"form-control")) }}

                                                </div>

                                            </div>

                                        </div>

                                    <div class="form-group">

                                        <div class="form-actions">
                                            <input type="submit" value="Create" class="btn btn-success pull-right">
                                            {{ Form::button('RESET', ['type' => 'reset', 'class' => 'btn btn-default pull-right'] )  }}

                                        </div>

                                    </div>
                    {{ Form::close() }}

                </div>

            </div>

        </div>

        <!-- /Vertical Forms -->
        {{ Form::close() }}
    </div>


@stop
