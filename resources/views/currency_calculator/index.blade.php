@extends('layouts.default')

@section('page-crumbs')
	<ul id="breadcrumbs" class="breadcrumb">
		<li>
			<i class="icon-home"></i>
			<a href="/">Dashboard</a>
		</li>
		<li class="current">
			<a href="/currency_calculator" title="">Currency Calculator</a>
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
	</div>
@stop

@section('content')

<div class="row">
	<div class="col-md-12">
		<div class="widget box">
			<div class="widget-header">
				<h4><i class="icon-reorder"></i> Currency Calculator</h4>
			</div>
			<div class="widget-content">
				{!! Form::open(['method'=>'PUT', 'action'=> ['CurrencyCalculatorController@update',$user_id], 'class'=>'form-vertical row-border form-validate','id'=>'main','enctype'=>'multipart/form-data']) !!}
				{{--<form autocomplete="off" enctype="multipart/form-data" id="main" class="form-vertical row-border form-validate" action="" method="POST">--}}
					<div class="form-group">
						<div class="row">
							<div class="col-md-2">
								<label class="control-label">Date</label>
								{{ Form::text('date', $date, array("class"=>"form-control datepicker")) }}
							</div>
							<div class="col-md-2">
								<label class="control-label">From</label>
								{{ Form::select('currency_from', $select_currency_codes, $currency_from, array("class"=>"form-control")) }}
							</div>
							<div class="col-md-2">
								<label class="control-label">To</label>
								{{ Form::select('currency_to', $select_currency_codes, $currency_to, array("class"=>"form-control")) }}
							</div>
							<div class="col-md-2">
								<label class="control-label">Amount</label>
								{{ Form::text('amount', $amount, array("class"=>"form-control")) }}
							</div>
							<div class="col-md-2">
								<label class="control-label">Result</label>
								{{ Form::text('result', $result, array("class"=>"form-control","readonly")) }}
							</div>
						</div>
					</div>
					<div class="form-group">
						<div class="row">
							<div class="col-md-2">
								<label class="control-label">Cost Currency</label>
								{{ Form::select('cost_currency', $select_currency_codes, $cost_currency, array("class"=>"form-control")) }}
							</div>
							<div class="col-md-2">
								<label class="control-label">Cost Price</label>
								{{ Form::text('cost', $cost, array("class"=>"form-control")) }}
							</div>
							<div class="col-md-2">
								<label class="control-label">Sales Currency</label>
								{{ Form::select('sale_currency', $select_currency_codes, $sale_currency, array("class"=>"form-control")) }}
							</div>
							<div class="col-md-2">
								<label class="control-label">Sales Price</label>
								{{ Form::text('sale', $sale, array("class"=>"form-control")) }}
							</div>
							<div class="col-md-2">
								<label class="control-label">Amount</label>
								{{ Form::text('margin', $margin, array("class"=>"form-control", "readonly")) }}
							</div>
							<div class="col-md-2">
								<label class="control-label">Amount %</label>
								{{ Form::text('margin_percent', $margin_percent, array("class"=>"form-control","readonly")) }}
							</div>

						</div>
					</div>
					<div class="form-group">
						<div class="row">
							<div class="col-md-12">
								<table class="table table-bordered table-hover">
									<thead>
										<th>Rates table</th>
										@foreach($select_currency_codes as $currency_code)
											<th>{{$currency_code}}</th>
										@endforeach
									</thead>
									@foreach($select_currency_codes as $currency_code_a)
										<tr>
											<td><strong>{{$currency_code_a}}</strong></td>
											@foreach($select_currency_codes as $currency_code_b)
												<td>
													<?php
														$result = convert_currency($currency_code_a,$currency_code_b,$amount,$date);
														$result = number_format($result,5);
													?>
													{{$result}}
												</td>
											@endforeach
										</tr>
									@endforeach
								</table>
								{{--<p><a href="/exchange_rates">View Source Rates</a></p>--}}
							</div>
						</div>
						<div class="form-actions">
							<input type="submit" value="Recalculate" class="btn btn-sm btn-success pull-right">
							{{--<input type="submit" name="flip" value="Flip" class="btn btn-sm btn-success pull-right">--}}
						</div>
					</div>
                {{ Form::close() }}
            </div>
		</div>
	</div>
</div>

@stop
