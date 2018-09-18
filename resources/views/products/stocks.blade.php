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
				<h4><i class="icon-reorder"></i> Product Stock Settings</h4>
			</div>
			<div class="widget-content">
				<form enctype="multipart/form-data" id="main" class="form-vertical row-border form-validate" action="/products/stocks/{{$product->id}}" method="POST">
					<div class="form-group">
						<div class="row">
							<div class="col-md-2">
								<label class="control-label">Track Stock?</label>
								{{ Form::hidden('id', $product->id, array("class"=>"form-control")) }}
								{{ Form::select('track_stock', array('1' => 'Yes', '0' => 'No'), $product->track_stock, array("class"=>"form-control")) }}
							</div>
							<div class="col-md-2">
								<label class="control-label">Minimum Stock</label>
								{{ Form::text('stock_min', $product->stock_min, array("class"=>"form-control")) }}
							</div>
							<div class="col-md-2">
								<label class="control-label">Location</label>
								{{ Form::text('location', $product->location, array("class"=>"form-control")) }}
							</div>
							<div class="col-md-2 pull-right">
								<label class="control-label">Current Stock</label>
								{{ Form::text('', $product->getStockOnHand(), array("class"=>"form-control","readonly")) }}
							</div>
							<div class="col-md-2 pull-right">
								<label class="control-label">On Order</label>
								{{ Form::text('', $product->getOnOrder(), array("class"=>"form-control","readonly")) }}
							</div>
						</div>
						@if(has_role('products_edit') || has_role('products_location_edit'))
							<div class="form-actions">
								<input type="submit" value="Save" class="btn btn-sm btn-success pull-right">
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
				<h4><i class="icon-reorder"></i> Adjust Stock</h4>
			</div>
			<div class="widget-content">
				<form enctype="multipart/form-data" id="main" class="form-vertical row-border form-validate" action="/products/stock-adjust/{{$product->id}}" method="POST" autocomplete="off">
					<div class="form-group">
						<div class="row">
							<div class="col-md-2">
								<label class="control-label">Adjust by Quantity</label>
								{{ Form::text('quantity', "", array("class"=>"form-control")) }}
							</div>
							<div class="col-md-4">
								<label class="control-label">Remark</label>
								{{ Form::text('remark', "", array("class"=>"form-control")) }}
							</div>
						</div>
						@if(has_role('products_edit') || has_role('products_location_edit'))
							<div class="form-actions">
								<input type="submit" value="Save" class="btn btn-sm btn-success pull-right">
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
				<h4><i class="icon-reorder"></i> Last 100 Transactions for this Stock</h4>
				<div class="toolbar no-padding">
					<div class="btn-group">
						<span class="btn btn-xs widget-collapse"><i class="icon-angle-down"></i></span>
					</div>
				</div>
			</div>
			<div class="widget-content no-padding">
				<table class="table table-striped table-bordered table-hover table-no-break">
					<thead>
						<tr>
							<th class="cell-tight">Transaction #</th>
							<th class="cell-tight">Timestamp</th>
							<th class="cell-tight">By</th>
							<th class="cell-tight">Quantity In</th>
							<th class="cell-tight">Quantity Out</th>
							<th>Remark</th>
						</tr>
					</thead>
					<tbody>

						@foreach($transactions as $transaction)
							<tr>
								<td>{{ $transaction->id }}</td>
								<td>{{ $transaction->created_at }}</td>
								<td>{{ $transaction->createdBy()}}</td>
                                @if($transaction->quantity >= 0)
                                    <td>{{ $transaction->quantity }}</td>
                                    <td></td>
                                @else
                                    <td></td>
                                    <td>{{ $transaction->quantity }}</td>
                                @endif
								<td>{{ $transaction->remark }}</td>
							</tr>
						@endforeach
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>
@stop
