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
<div class="col-md-12">
		<div class="widget box">
			<div class="widget-header">
				<h4><i class="icon-reorder"></i> Set Product Prices</h4>
			</div>
			<div class="widget-content">

				<form enctype="multipart/form-data" id="main" class="form-vertical row-border form-validate" action="" method="POST">
					<div class="form-group">
						<div class="row">
							<div class="col-md-2">
								<label class="control-label">Currency</label>
								{{ Form::text('currency_code', $product->currency_code, array("class"=>"form-control")) }}
                                {{ Form::hidden('action', "update_base_price", array()) }}
                            </div>
							<div class="col-md-2">
								<label class="control-label">Cost Price 20'</label>
								{{ Form::text('base_price_20', $product->base_price_20, array("class"=>"form-control")) }}
							</div>
							<div class="col-md-2">
								<label class="control-label">Cost Price 40'</label>
								{{ Form::text('base_price_40', $product->base_price_40, array("class"=>"form-control")) }}
							</div>
							<div class="col-md-2">
								<label class="control-label">Landed Factor 20'</label>
								{{ Form::text('landed_20', $product->landed_20, array("class"=>"form-control")) }}
							</div>
							<div class="col-md-2">
								<label class="control-label">Landed Factor 40'</label>
								{{ Form::text('landed_40', $product->landed_40, array("class"=>"form-control")) }}
							</div>
						</div>
                    </div>

					<div class="form-group">
                        <div class="row">
							<div class="col-md-2">
								<label class="control-label">Local Currency</label>
								{{ Form::text('', $product->company->currency_code, array("class"=>"form-control","readonly")) }}
                            </div>
							<div class="col-md-2">
								<label class="control-label">Landed Price 20'</label>
                                {{ Form::hidden('action', "update_base_price", array()) }}
								{{ Form::text('', convert_currency($product->currency_code, $product->company->currency_code,$product->base_price_20 * $product->landed_20), array("class"=>"form-control","readonly")) }}
							</div>
							<div class="col-md-2">
								<label class="control-label">Landed Price 40'</label>
								{{ Form::text('', convert_currency($product->currency_code, $product->company->currency_code,$product->base_price_40 * $product->landed_40), array("class"=>"form-control","readonly")) }}
							</div>
							<div class="col-md-2">
								<label class="control-label">Sales Basis 20'</label>
								{{ Form::text('sales_base_20', $product->sales_base_20, array("class"=>"form-control")) }}
							</div>
							<div class="col-md-2">
								<label class="control-label">Sales Basis 40'</label>
								{{ Form::text('sales_base_40', $product->sales_base_40, array("class"=>"form-control")) }}
							</div>
						</div>
					</div>

					<div class="form-group">
                        <div class="row">
@if(Auth::user()->id == 35 || has_role('admin'))
							<div class="col-md-2">
								<label class="control-label">New Price</label>
								{{ Form::text('', $product->new_price, array("class"=>"form-control","readonly")) }}
                            </div>
@endif
						</div>

						@if(has_role('products_edit_prices'))
							<div class="form-actions">
								<input type="submit" value="Update Base Prices" class="btn btn-sm btn-success pull-right">
							</div>
						@endif
					</div>


				</div>

			</form>
		</div>
	</div>
</div>


<div class="row">
	<div class="col-md-12">
		<div class="widget box">
			<div class="widget-header">
				<h4><i class="icon-reorder"></i> Add Factors for each Customer Group</h4>
				<div class="toolbar no-padding">
					<div class="btn-group">
					</div>
				</div>
			</div>
			<div class="widget-content">
				<table class="table table-hover">
					<thead>
						<tr>
							<th>Group Name</th>
							<th>Factor 20'</th>
							<th>Factor 40'</th>
							<th>Price 20'</th>
							<th>Price 40'</th>
							<th class="align-right"></th>
						</tr>
					</thead>
					<tbody>
					@if($product->prices->count()>0)

					@foreach($group_prices as $group)
						<tr>
							<td>{{ $group->group->group }}</td>
							<td>{{ $group->surcharge_20 }}</td>
							<td>{{ $group->surcharge_40 }}</td>
                            <td>
                                {{ round($product->sales_base_20 / $group->surcharge_20,2) }}
                            </td>
                            <td>
                                {{ round($product->sales_base_40 / $group->surcharge_40,2) }}
                            </td>
							<td class="align-right">
								@if(has_role('products_edit'))
                                        <a href="/products/prices-group-delete/{{ $group->id }}" class="btn btn-xs">Remove</a>
									</span>
								@endif
							</td>
						</tr>
					@endforeach
                    @else
						<tr>
							<td colspan="4">Nothing found</td>
						</tr>
					@endif
                    {{ Form::open() }}
                    {{ Form::hidden('action', "group_add", array()) }}
                    <tr>
                        <td>
                            {{ Form::select('select_groups', $select_groups, "", array("class"=>"form-control")) }}
                        </td>
                        <td>
                            {{ Form::text('surcharge_20', "", array("class"=>"form-control")) }}
                        </td>
                        <td>
                            {{ Form::text('surcharge_40', "", array("class"=>"form-control")) }}
                        </td>
<td></td>
<td></td>
                        <td>
                            <input type="submit" value="ADD/CHANGE" class="btn btn-xs pull-right">
                        </td>
                    </tr>
                    {{ Form::close() }}

					</tbody>
				</table>
			</div>
		</div>
		<!-- /Simple Table -->
	</div>
</div>





<div class="row">
	<div class="col-md-12">
		<div class="widget box">
			<div class="widget-header">
				<h4><i class="icon-reorder"></i> Set a fixed price for a customer</h4>
				<div class="toolbar no-padding">
					<div class="btn-group">
					</div>
				</div>
			</div>
			<div class="widget-content">
				<table class="table table-hover">
					<thead>
						<tr>
							<th>Customer Name</th>
							<th>Sales Price 20'</th>
							<th>Sales Price 40'</th>
							<th>Status</th>
							<th class="align-right"></th>
						</tr>
					</thead>
					<tbody>
					@if(count($product->priceOverrides)>0)
					@foreach($product->priceOverrides->sortBy('customer_id') as $customer)
						<tr>
							<td>{{$customer->customer->customer_name }}</td>
							<td>{{$customer->base_price_20}}</td>
							<td>{{$customer->base_price_40}}</td>
							<td>
								@if($customer->customer->status == 'Active')
									<span class="label label-success">ACTIVE</span>
								@else
									<span class="label label-default">INACTIVE</span>
								@endif
							</td>
							<td class="align-right">
								@if(has_role('products_edit'))
                                    <a href="/products/prices-delete/{{$customer->id}}" class="btn btn-xs"><i class="icon-trash"></i></a>
								@endif
								<a href="/customers/show/{{$customer->customer_id}}" class="btn btn-xs"><i class="icon-search"></i></a>
							</td>
						</tr>
					@endforeach
					@else
						<tr>
							<td colspan="6">Nothing found</td>
						</tr>
					@endif
                    {{ Form::open() }}
                    {{ Form::hidden('action', "customer_add", array()) }}
                    <tr>
                        <td>
                            {{ Form::select('customer_id', $select_customers, (isset($product_customer->customer_id) ? $product_customer->customer_id : ""), array("class"=>"select2 col-md-12 full-width-fix")) }}
                        </td>
                        <td>
                            {{ Form::text('base_price_20', "", array("class"=>"form-control")) }}
                        </td>
                        <td>
                            {{ Form::text('base_price_40', "", array("class"=>"form-control")) }}
                        </td>
                        <td>
                        </td>
                        <td>
                            <input type="submit" value="ADD/CHANGE" class="btn btn-xs pull-right">
                        </td>
                    </tr>
                    {{ Form::close() }}
					</tbody>
				</table>
			</div>
		</div>
		<!-- /Simple Table -->
	</div>
</div>

<div class="row">
	<div class="col-md-12">
		<div class="widget box">
			<div class="widget-header">
				<h4><i class="icon-reorder"></i> Set customer specific visibility</h4>
				<div class="toolbar no-padding">
					<div class="btn-group">
					</div>
				</div>
			</div>
			<div class="widget-content">
				<table class="table table-hover">
					<thead>
						<tr>
							<th>Customer Name</th>
							<th class="align-right"></th>
						</tr>
					</thead>
					<tbody>
					@if(count($product->customerSpecifics)>0)
					@foreach($product->customerSpecifics->sortBy('customer_id') as $customer)
						<tr>
							<td>{{$customer->customer->customer_name }}</td>
							<td class="align-right">
								@if(has_role('products_edit'))
                                    <a href="/products/customer-specific-delete/{{$customer->id}}" class="btn btn-xs"><i class="icon-trash"></i></a>
								@endif
								<a href="/customers/show/{{$customer->customer_id}}" class="btn btn-xs"><i class="icon-search"></i></a>
							</td>
						</tr>
					@endforeach
					@else
						<tr>
							<td colspan="2">Nothing found</td>
						</tr>
					@endif
                    {{ Form::open() }}
                    {{ Form::hidden('action', "customer_specific_add", array()) }}
                    <tr>
                        <td>
                            {{ Form::select('customer_id', $select_customers, (isset($product_customer->customer_id) ? $product_customer->customer_id : ""), array("class"=>"select2 col-md-12 full-width-fix")) }}
                        </td>
                        <td>
                            <input type="submit" value="ADD" class="btn btn-xs pull-right">
                        </td>
                    </tr>
                    {{ Form::close() }}
<tr>
    <td colspan="2">
        <p class='text-muted'>If set, only customers in this list can see or order this product</p>
    </td>
</tr>
					</tbody>
				</table>
			</div>
		</div>
		<!-- /Simple Table -->
	</div>
</div>



@stop
