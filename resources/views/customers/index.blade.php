@extends('layouts.default')

@section('page-module-menu')
	<li><a href="/customer/getIndex">Customers</a></li>
	<!-- if(has_role('customers_export')) -->
	<!-- endif -->
@stop

@section('page-crumbs')
	<ul id="breadcrumbs" class="breadcrumb">
		<li>
			<i class="icon-home"></i>
			<a href="/dashboard">Dashboard</a>
		</li>
		<li class="current">
			<a href="/customer/getIndex" title="">Customers</a>
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
			@if(has_role('customers_edit'))
			<form class="form-inline" id="create" action="/customer/create" method="GET"><!--remove cutomers/create and replace with customer/getProducts-->
				<a class="btn btn-success btn-lg form-submit-conf" href="javascript:void(0);" data-target-form="create"><i class="icon-plus-sign"></i> New Customer</a>
			</form>
			@endif
		</div>

		<ul class="page-stats">
			<li>
				<div class="summary">
					<span>OUTSTANDINGS</span>
					<h3>{{ $outstanding_balance_currency_code }} {{ number_format($outstanding_balance_amount,2) }}</h3>
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
								<h4><i class="icon-reorder"></i> Customer Index</h4>
								<div class="toolbar no-padding">
									<div class="btn-group">
										<span class="btn btn-xs widget-collapse"><i class="icon-angle-down"></i></span>
									</div>
								</div>
							</div>
							<div class="widget-content no-padding">
								<table class="table table-striped table-bordered table-hover datatable"  id="customer-table">
									<thead>
										<tr>
											<th class="cell-tight">Status</th>
											<th class="cell-tight">Code</th>
											<th>Company Name</th>
											<th>City</th>
											<th>Country</th>
											<th>view</th>
										</tr>
									</thead>
									<tbody>
										@foreach($customers as $customer)
										  <tr>
												<td>{{$customer->status}}</td>
												<td>{{$customer->code}}</td>
												<td>{{$customer->customer_name}}</td>
												<td>{{$customer->inv_city}}</td>
												<td>{{$customer->inv_country}}</td>
												<td><a href="/customer/getShow/{{ $customer->id }}" class="bs-tooltip" title="View"><i class="icon-search"></i></a> </td>
											</tr>
										@endforeach

									</tbody>
								</table>
							</div>
						</div>
					</div>
				</div>
				<!-- /Normal -->


<script>
	$(document).ready(function(){
		$('#customer-table').dataTable();
	})
</script>

@stop
