@layout('layouts.default')
@section('page-module-menu')
	<li><a href="/reports">Reports</a></li>
@stop

@section('page-crumbs')
	<ul id="breadcrumbs" class="breadcrumb">
		<li class="current">
			<i class="icon-home"></i>
			<a href="/">Dashboard</a>
		</li>
		<li>
			<a href="/dashboards/reports">Reports</a>
		</li>
		<li>
			<a href="/dashboards/reports/expenses">Expenses</a>
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
			<h3>Reports</h3>
			<span>Expenses between {{ $date_start }} and {{ $date_end }}</span>
		</div>
	</div>
@stop

@section('content')

<div class="row">
	<div class="col-md-12">
		<div class="widget box">
			<div class="widget-header">
				<h4><i class="icon-reorder"></i> Query details</h4>
			</div>
			<div class="widget-content">
				<form enctype="multipart/form-data" id="main" class="form-horizontal row-border form-validate" action="" method="POST">
					<div class="form-group">
						<div class="col-md-12">
							<div class="row">
								<div class="col-md-2">
									<label class="control-label">Start Date</label>
									{{ Form::text('date_start', $date_start, array("class"=>"form-control datepicker")) }}
								</div>
								<div class="col-md-2">
									<label class="control-label">End Date</label>
									{{ Form::text('date_end', $date_end, array("class"=>"form-control datepicker")) }}
								</div>
							</div>
						</div>
					</div>

					<div class="row">
						<div class="col-md-12">
						</div>
					</div>
					<div class="form-actions">
						<input type="submit" value="Recalculate" class="btn btn-success pull-right">
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
					<h4><i class="icon-reorder"></i> Expenses by category</h4>
					<div class="toolbar no-padding">
						<div class="btn-group">
							<span class="btn btn-xs widget-collapse"><i class="icon-angle-down"></i></span>
						</div>
					</div>
				</div>
				<div class="widget-content no-padding">
					<?php
					?>
					<table class="table table-striped table-bordered table-hover">
						<thead>
							<tr>
								<th>Expense</th>
								<th>Amount {{ $currency_code }}</th>
								<th>Percentage</th>
							</tr>
						</thead>
						<tbody>
							@foreach($dataset as $category => $data)
								<tr>
									<td>{{ $category }}</td>
									<td>${{ number_format($data['amount'],2) }}</td>
									<td>%{{ number_format($data['percentage'],2) }}</td>
								</tr>
							@endforeach
							<tr>
								<td><strong>Total</strong></td>
								<td><strong>${{ number_format($total,2) }}</strong></td>
								<td></td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>

@stop
