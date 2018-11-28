@extends('layouts.default')

@section('page-crumbs')
	<ul id="breadcrumbs" class="breadcrumb">
		<li class="current">
			<i class="icon-home"></i>
			<a href="/">Dashboard</a>
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
			<h3>Dashboard</h3>
			<span>Business KPI's {{ $date_start }} - {{ $date_end }}, displayed in {{ Auth::user()->company->currency_code }}</span>
		</div>
		<ul class="page-stats">
			<li>
				<div class="summary">
					@if(Auth::user()->company->company_logo != "")
						<img src="/public/global/companies/{{ Auth::user()->company->company_logo }}" alt="logo" width="200px"/>
					@else
						<img src="/assets/img/logo.png" alt="logo" width="200px"/>
					@endif
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
					<h4><i class="icon-reorder"></i> Company Overview</h4>
					<div class="toolbar no-padding">
						<div class="btn-group">
							<span class="btn btn-xs widget-collapse"><i class="icon-angle-down"></i></span>
						</div>
					</div>
				</div>
				<div class="widget-content no-padding">
					<table class="table table-striped table-bordered table-hover">
						<thead>
						<tr>
							<th>Company Name</th>
							<th>P.O written</th>
							<th>P.O paid</th>
							<th>Expenses</th>
							<th>Invoices written</th>
							<th>Payments received</th>
							<th>Gross Profit</th>
							<th></th>
						</tr>
						</thead>
						<tbody>
						@foreach(Company::all() as $company)
							<tr>
								<td>
									@if($company->id == 1)
										<i class="icon-arrow-up"></i>
									@else
										<i class="icon-arrow-right"></i>
									@endif
									{{ $company->name }}
								</td>
								<td><a href="/">{{ $company->getPurchaseOrdersWritten($date_start, $date_end, $currency_code) }}</a></td>
								<td><a href="/">{{ $company->getPurchaseOrdersPaid($date_start, $date_end, $currency_code) }}</a></td>
								<td><a href="/">{{ $company->getExpenses($date_start, $date_end, $currency_code) }}</a></td>
								<td><a href="/">{{ $company->getInvoicesWritten($date_start, $date_end, $currency_code) }}</a></td>
								<td><a href="/">{{ $company->getPaymentsReceived($date_start, $date_end, $currency_code) }}</a></td>
								<td>
									<a href="/">{{ $company->getGrossProfit($date_start, $date_end, $currency_code) }}</a>
								<td>
									<a class="btn btn-xs btn-default pull-right" href="">ADVANCED</a>
								</td>
							</tr>
						@endforeach
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>


	<div class="row">
		<div class="col-md-12">
			<div class="widget box">
				<div class="widget-header">
					<h4><i class="icon-reorder"></i> Please confirm</h4>
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
									<div class="col-md-2">
										<label class="control-label">Currency</label>
										{{ Form::select('currency_code', $select_currency_codes, $currency_code, array("class"=>"form-control")) }}
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




	<!--
    <div class="row">
        <div class="col-md-12">
            <div class="widget box">
                <div class="widget-header">
                    <h4><i class="icon-reorder"></i> Product Details</h4>
                </div>
                <div class="widget-content">
                    <div class="row">
                        <div class="col-md-2">
                            <h2>Content</h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    -->

@stop
