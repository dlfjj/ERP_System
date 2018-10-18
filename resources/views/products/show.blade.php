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
        <form class="form-inline" id="create" action="/product/postShow/{{$product->id}}" method="POST">

        </form>



        <form class="form-inline" id="duplicate" action="/products/duplicate/{{$product->id}}" method="POST">

        </form>



        <!--=== Vertical Forms ===-->

        <div class="col-md-12">

            <div class="widget box">

                <div class="widget-header">

                    <h4><i class="icon-reorder"></i> Product Details</h4>

                </div>

                <div class="widget-content">

                    {{--<form autocomplete="off" enctype="multipart/form-data" id="main" class="form-vertical row-border form-validate" action="/products/{{$product->id}}" method="PATCH">--}}
                        {!! Form::open(['action' => ["ProductController@update",$product->id],'id'=>'main', 'enctype' => 'multipart/form-data', 'method' => 'PATCH', 'class' => 'form-vertical row-border form-validate', 'autocomplete' => 'off']) !!}
                        {{csrf_field()}}
                        <div class="tabbable">

                            <ul class="nav nav-tabs">

                                <li class="active"><a href="#box_tab1" data-toggle="tab">General</a></li>

                                <li><a href="#box_tab4" data-toggle="tab">Prices</a></li>

                                <li><a href="#box_tab3" data-toggle="tab">Web Description localized</a></li>

                                <li><a href="#box_tab2" data-toggle="tab">Web Description</a></li>

                            </ul>

                            <div class="tab-content">

                                <div class="tab-pane active" id="box_tab1">

                                    <div class="form-group">

                                        <div class="row">

                                            <div class="col-md-2">

                                                <label class="control-label">Serial #</label>

                                                {{ Form::text('id', $product->id, array("class"=>"form-control","readonly")) }}

                                            </div>

                                            <div class="col-md-2">

                                                <label class="control-label">Part Number</label>

                                                {{ Form::text('product_code', $product->product_code, array("class"=>"form-control")) }}

                                            </div>

                                            <div class="col-md-2">

                                                <label class="control-label">MPN</label>

                                                {{ Form::text('mpn', $product->mpn, array("class"=>"form-control")) }}

                                            </div>

                                            {{--<div class="col-md-2">--}}

                                                {{--<label class="control-label">Category</label>--}}
                                                {{--<select name="category_id" class="form-Control">--}}
                                                    {{--@foreach($tree as $category_name=>$category_id)--}}
                                                        {{--<option value="{{$category_id}}">{{$category_name}}</option>--}}
                                                    {{--@endforeach--}}
                                                {{--</select>--}}

                                            {{--</div>--}}

                                            <div class="col-md-2">
                                                <label class="control-label">Category</label>
                                                {!! $select_categories !!}
                                            </div>

                                            <div class="col-md-3">

                                            </div>

                                            <div class="col-md-1 text-right">

                                                @if($product->picture != "")

                                                    <img src="/products/view-main-image/{{$product->id}}" />

                                                @else

                                                    <img src="http://placehold.it/100x100">

                                                @endif

                                            </div>

                                        </div>

                                    </div>

                                    <div class="form-group">

                                        <div class="row">

                                            <div class="col-md-4">

                                                <label class="control-label">Short Product Name</label>

                                                {{ Form::text('product_name', $product->product_name, array("class"=>"form-control")) }}

                                            </div>

                                            <div class="col-md-4">

                                                <label class="control-label">Localized Product Name</label>

                                                {{ Form::text('product_name_local', $product->product_name_local, array("class"=>"form-control")) }}

                                            </div>

                                            <div class="col-md-4">

                                                <label class="control-label">Remarks</label>

                                                {{ Form::textarea('remarks', $product->remarks, array("rows"=>"3","cols"=>"5","class"=>"form-control")) }}

                                            </div>

                                        </div>

                                    </div>

                                    <div class="form-group">

                                        <div class="row">

                                            <div class="col-md-2">

                                                <label class="control-label">UOM</label>

                                                {{ Form::select('uom', $select_uom, $product->uom, array("class"=>"form-control")) }}

                                            </div>

                                            <div class="col-md-2">

                                                <label class="control-label">Size</label>

                                                {{ Form::text('size', $product->size, array("class"=>"form-control")) }}

                                            </div>

                                            <div class="col-md-2">

                                                <label class="control-label">CSC #</label>

                                                {{ Form::text('commodity_code', $product->commodity_code, array("class"=>"form-control")) }}

                                            </div>

                                            <div class="col-md-2">

                                                <label class="control-label">Manufacturer</label>

                                                {{ Form::select('manufacturer', $select_manufacturer, $product->manufacturer, array("class"=>"form-control")) }}

                                            </div>

                                            <div class="col-md-2">

                                                <label class="control-label">Unit Weight</label>

                                                {{ Form::text('weight', $product->weight, array("class"=>"form-control")) }}

                                            </div>

                                            <div class="col-md-2">

                                                <label class="control-label">MOQ</label>

                                                {{ Form::text('moq', $product->moq, array("class"=>"form-control")) }}

                                            </div>

                                        </div>

                                    </div>

                                    <div class="form-group">

                                        <div class="row">

                                            <div class="col-md-2">

                                                <label class="control-label">Origin</label>

                                                {{ Form::select('origin', $select_origin , $product->origin, array("class"=>"form-control")) }}

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

                                                {{ Form::select('status', array('Active' => 'Active','Inactive' => 'Inactive', 'Draft' => 'Draft'), $product->status, array("class"=>"form-control")) }}

                                            </div>

                                        </div>

                                    </div>



                                    <div class="form-group">

                                        <div class="row">

                                            <div class="col-md-2">

                                                <label class="control-label">Pack Unit</label>

                                                {{ Form::text('pack_unit', $product->pack_unit, array("class"=>"form-control")) }}

                                            </div>

                                            <div class="col-md-2">

                                                <label class="control-label">PU per Pallet</label>

                                                {{ Form::text('units_per_pallette', $product->units_per_pallette, array("class"=>"form-control")) }}

                                            </div>

                                            <div class="col-md-2">

                                                <label class="control-label">PU Nt Weight</label>

                                                {{ Form::text('pack_unit_net_weight', $product->pack_unit_net_weight, array("class"=>"form-control")) }}

                                            </div>

                                            <div class="col-md-2">

                                                <label class="control-label">PU Gr Weight</label>

                                                {{ Form::text('pack_unit_gross_weight', $product->pack_unit_gross_weight, array("class"=>"form-control")) }}

                                            </div>

                                            <div class="col-md-2">

                                                <label class="control-label">Pallet Nt Weight</label>

                                                {{ Form::text('pallet_net_weight', $product->pallet_net_weight, array("class"=>"form-control")) }}

                                            </div>

                                        </div>

                                    </div>



                                    <div class="form-group">

                                        <div class="row">

                                            <div class="col-md-2">

                                                <label class="control-label">HQ Pack available</label>

                                                {{ Form::select('is_hq_pack', array('0' => 'No','1' => 'Yes'), $product->is_hq_pack, array("class"=>"form-control")) }}

                                            </div>

                                            <div class="col-md-2">

                                                <label class="control-label">Ctn Width (cm)</label>

                                                {{ Form::text('carton_size_w', $product->carton_size_w, array("class"=>"form-control")) }}

                                            </div>

                                            <div class="col-md-2">

                                                <label class="control-label">Ctn Depth (cm)</label>

                                                {{ Form::text('carton_size_d', $product->carton_size_d, array("class"=>"form-control")) }}

                                            </div>

                                            <div class="col-md-2">

                                                <label class="control-label">Ctn Height (cm)</label>

                                                {{ Form::text('carton_size_h', $product->carton_size_h, array("class"=>"form-control")) }}

                                            </div>

                                            <div class="col-md-2">

                                                <label class="control-label">Pallet Size</label>

                                                {{ Form::text('pallet_size', $product->pallet_size, array("class"=>"form-control")) }}

                                            </div>

                                        </div>

                                    </div>



                                    @if($product->is_hq_pack == 1)

                                        <div class="form-group">

                                            <div class="row">

                                                <div class="col-md-2">

                                                    <label class="control-label">Pack Unit HQ</label>

                                                    {{ Form::text('pack_unit_hq', $product->pack_unit_hq, array("class"=>"form-control")) }}

                                                </div>

                                                <div class="col-md-2">

                                                    <label class="control-label">PU per Pallet HQ</label>

                                                    {{ Form::text('units_per_pallette_hq', $product->units_per_pallette_hq, array("class"=>"form-control")) }}

                                                </div>

                                                <div class="col-md-2">

                                                    <label class="control-label">PU Nt Weight HQ</label>

                                                    {{ Form::text('pack_unit_net_weight_hq', $product->pack_unit_net_weight_hq, array("class"=>"form-control")) }}

                                                </div>

                                                <div class="col-md-2">

                                                    <label class="control-label">PU Gr Weight HQ</label>

                                                    {{ Form::text('pack_unit_gross_weight_hq', $product->pack_unit_gross_weight_hq, array("class"=>"form-control")) }}

                                                </div>

                                                <div class="col-md-2">

                                                    <label class="control-label">Pallet Nt Weight HQ</label>

                                                    {{ Form::text('pallet_net_weight_hq', $product->pallet_net_weight_hq, array("class"=>"form-control")) }}

                                                </div>

                                            </div>

                                        </div>



                                        <div class="form-group">

                                            <div class="row">

                                                <div class="col-md-2">

                                                    <label class="control-label">HQ MPN</label>

                                                    {{ Form::text('mpn_hq', $product->mpn_hq, array("class"=>"form-control")) }}

                                                </div>

                                                <div class="col-md-2">

                                                    <label class="control-label">HQ Ctn Width (cm)</label>

                                                    {{ Form::text('carton_size_w_hq', $product->carton_size_w_hq, array("class"=>"form-control")) }}

                                                </div>

                                                <div class="col-md-2">

                                                    <label class="control-label">HQ Ctn Depth (cm)</label>

                                                    {{ Form::text('carton_size_d_hq', $product->carton_size_d_hq, array("class"=>"form-control")) }}

                                                </div>

                                                <div class="col-md-2">

                                                    <label class="control-label">HQ Ctn Height (cm)</label>

                                                    {{ Form::text('carton_size_h_hq', $product->carton_size_h_hq, array("class"=>"form-control")) }}

                                                </div>

                                                <div class="col-md-2">

                                                    <label class="control-label">HQ Pallet Size</label>

                                                    {{ Form::text('pallet_size_hq', $product->pallet_size_hq, array("class"=>"form-control")) }}

                                                </div>

                                            </div>

                                        </div>

                                    @endif

                                    <div class="form-group">

                                        <div class="form-actions">

                                            @if(has_role('products_edit'))

                                                <input type="submit" value="Save" class="btn btn-sm btn-success pull-right">

                                            @endif

                                        </div>

                                    </div>

                                </div><!-- End Tab 1 -->

                                <div class="tab-pane" id="box_tab2">

                                    <div class="form-group">

                                        <div class="row">

                                            <div class="col-md-12">

                                                {{ Form::textarea('description', $product->description, array("rows"=>"3","cols"=>"5","class"=>"form-control froala-editor")) }}

                                            </div>

                                        </div>

                                        <div class="form-actions">

                                            @if(has_role('products_edit'))

                                                <input type="submit" value="Save" class="btn btn-sm btn-success pull-right">

                                            @endif

                                        </div>

                                    </div>

                                </div> <!-- Tab 2 end -->

                                <div class="tab-pane" id="box_tab3">

                                    <div class="form-group">

                                        <div class="row">

                                            <div class="col-md-12">

                                                {{ Form::textarea('description_local', $product->description_local, array("rows"=>"3","cols"=>"5","class"=>"form-control froala-editor")) }}

                                            </div>

                                        </div>

                                        <div class="form-actions">

                                            @if(has_role('products_edit'))

                                                <input type="submit" value="Save" class="btn btn-sm btn-success pull-right">

                                            @endif

                                        </div>

                                    </div>





                                </div> <!-- End Tab 4 -->

                                <div class="tab-pane" id="box_tab4">

                                    <div class="form-group">

                                        <div class="row">

                                            <div class="col-md-6">

                                                <table class="table table-hover">

                                                    <thead>

                                                    <tr>

                                                        <th>Group Prices</th>

                                                        <th>Price 20'</th>

                                                        <th>Price 40'</th>

                                                    </tr>

                                                    </thead>

                                                    <tbody>

                                                    @if($product->prices->count()>0)

                                                        @foreach($group_prices as $group)

                                                            <tr>

                                                                <td>{{ $group->group->group }}</td>

                                                                <td>

                                                                    {{ round($product->sales_base_20 / $group->surcharge_20,2) }}

                                                                </td>

                                                                <td>

                                                                    {{ round($product->sales_base_40 / $group->surcharge_40,2) }}

                                                                </td>

                                                            </tr>

                                                        @endforeach

                                                    @else

                                                        <tr>

                                                            <td colspan="3">Nothing found</td>

                                                        </tr>

                                                    @endif

                                                    </tbody>

                                                </table>

                                            </div>

                                        </div>

                                        <div class="row">

                                            <div class="col-md-6">

                                                <table class="table table-hover">

                                                    <thead>

                                                    <tr>

                                                        <th>Group overrides</th>

                                                        <th>Price 20'</th>

                                                        <th>Price 40'</th>

                                                    </tr>

                                                    </thead>

                                                    <tbody>

                                                    @if(count($product->priceOverrides)>0)

                                                        {{--@foreach($product->priceOverrides->sortBy('customer_id') as $customer)--}}

                                                            {{--<tr>--}}

                                                                {{--<td>{{$customer->customer->customer_name }}</td>--}}

                                                                {{--<td>{{$customer->base_price_20 }}</td>--}}

                                                                {{--<td>{{$customer->base_price_40}}</td>--}}

                                                            {{--</tr>--}}

                                                        {{--@endforeach--}}



                                                    @else

                                                        <tr>

                                                            <td colspan="3">Nothing found</td>

                                                        </tr>

                                                    @endif

                                                    </tbody>

                                                </table>

                                            </div>

                                        </div>

                                        <div class="row">

                                            <div class="col-md-6">

                                                <table class="table table-hover">

                                                    <thead>

                                                    <tr>

                                                        <th>Customer Specific</th>

                                                    </tr>

                                                    </thead>

                                                    <tbody>

                                                    @if(count($product->customerSpecifics)>0)

                                                        @foreach($product->customerSpecifics->sortBy('customer_id') as $customer)

                                                            <tr>

                                                                <td>{{$customer->customer->customer_name }}</td>

                                                            </tr>

                                                        @endforeach

                                                    @else

                                                        <tr>

                                                            <td colspan="1">Nothing found</td>

                                                        </tr>

                                                    @endif

                                                    </tbody>

                                                </table>

                                            </div>

                                        </div>

                                    </div>

                                </div> <!-- Tab 3 end -->

                            </div>
                        </div>
                    {{ Form::close() }}
                    {{--</form>--}}

                </div>

            </div>

        </div>

        <!-- /Vertical Forms -->
        {{ Form::close() }}
    </div>



    <div class="row">

        <div class="col-md-12 no-padding">

            <p class="record_status">Created: {{$product->created_at}} | Created by: {{$created_by_user}} | Updated: {{$product->updated_at}} | Updated by: {{$updated_by_user}} | <a href="/products/changelog/{{ $product->id }}">Changelog</a></p>

        </div>

    </div>



@stop
