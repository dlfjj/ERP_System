@extends('layouts.default')
@section('page-module-menu')
	<li><a href="/reports/downloads">Downloads</a></li>
	<li><a href="/reports/exports">Exports</a></li>
@stop

@section('page-crumbs')
	<ul id="breadcrumbs" class="breadcrumb">
		<li>
			<i class="icon-home"></i>
			<a href="/">Dashboard</a>
		</li>
		<li><a href="/reports/">Reports</a></li>
		<li class="current"><a href="/reports/getStocklist">Stocklist</a></li>
	</ul>

	<ul class="crumb-buttons">
		<li><a href="#modal_set_dates" data-toggle="modal" class="" title=""><i class="icon-cog"></i><span>Report Scope</span></a></li>
		<li>
			<a href="javascript:void(0);" title=""><i class="icon-calendar"></i><span><?=date('F d, Y \(\K\W:W) H:i:s');?></span></a>
		</li>
	</ul>
@stop

@section('page-header')
	<div class="page-header">
		<div class="page-title">
			<h3>Current Stocklist</h3>
			<span>Report uses Landed Price to calculate inventory value</span>
		</div>
	</div>
@stop

@section('content')
	<div class="row">
		<div class="col-md-12">
			<div class="widget box">
				<div class="widget-header">
					<h4><i class="icon-reorder"></i> Query results</h4>
					{{--<div class="toolbar no-padding">--}}
						{{--<div class="btn-group">--}}
							{{--<span class="btn btn-xs widget-collapse"><i class="icon-angle-down"></i></span>--}}
						{{--</div>--}}
					{{--</div>--}}
				</div>
				<div class="widget-content">
					<table class="table table-hover">
						<thead>
						<tr>
							<th>Product Code</th>
							<th>MPN</th>
							<th>Product Name</th>
							<th>Location</th>
							<th>Stock Qty</th>
							<th class="cell-tight">Amount 20' (<small>{{ $currency_code }}</small>)</th>
							<th class="cell-tight">Amount 40' (<small>{{ $currency_code }}</small>)</th>
						</tr>
						</thead>
						<tbody>
						@php
							$grand_total_20 = 0;
                            $grand_total_40 = 0;
						@endphp
						@if(count($products)> 0)

							@foreach($products as $product)
								@php

									$value_20 = $product->stock * ($product->base_price_20 * $product->landed_20);
                                    $value_40 = $product->stock * ($product->base_price_40 * $product->landed_40);

                                    $grand_total_20 += $value_20;
                                    $grand_total_40 += $value_40;
								@endphp

								<tr>
									<td>{{ $product->product_code }}</td>
									<td>{{ $product->mpn }}</td>
									<td><a href="/products/{{$product->id}}">{{substr($product->product_name,0,50) }}</a></td>
									<td>{{ $product->location }}</td>
									<td>{{ $product->stock }}</td>
									<td class="cell-tight">{{ number_format($value_20,2) }}</td>
									<td class="cell-tight">{{ number_format($value_40,2) }}</td>
								</tr>

							@endforeach
							<tr>
								<td></td>
								<td></td>
								<td></td>
								<td></td>
								<td>Total:</td>
								<td>{{ number_format($grand_total_20,2) }}</td>
								<td>{{ number_format($grand_total_40,2) }}</td>
							</tr>

						@else
							<tr class="stockorder-form-row">
								<td class="cell-tight">No results / Adjust report parameters to Generate</td>
							</tr>

						@endif

						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>


	<div class="modal fade" id="modal_set_dates" style="overflow:hidden;">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h4 class="modal-title">Adjust report parameters</h4>
				</div>
				<form autocomplete="off" enctype="multipart/form-data" class="" action="" method="POST">

					<div class="modal-body">
						<div class="form-group">
							<div class="row" style="display: none;">
								<div class="col-md-6">
									{{ Form::hidden('date_start',$date_start, array("class"=>"form-control datepicker")) }}
									<span class="help-block">Date start</span>
								</div>
								<div class="col-md-6">
									{{ Form::hidden('date_end',$date_end, array("class"=>"form-control datepicker")) }}
									<span class="help-block">Date end</span>
								</div>
							</div>
						</div>
						<div class="form-group">
							<div class="row">
								<div class="col-md-6">
									{{ Form::hidden('currency_code',Auth::user()->company->currency_code, array("class"=>"form-control")) }}
								</div>
							</div>
						</div>


					</div>

					<div class="modal-footer">
						<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
						<input type="submit" class="btn btn-primary" value="Generate">
					</div>

				</form>

			</div>
		</div>
	</div>
@stop
