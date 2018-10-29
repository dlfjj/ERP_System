@extends('layouts.default')
@section('page-module-menu')
	<li><a href="/reports">Reports</a></li>
	<li><a href="/reports/downloads">Downloads</a></li>
	<li><a href="/reports/exports">Exports</a></li>
@stop

@section('page-crumbs')
	<ul id="breadcrumbs" class="breadcrumb">
		<li class="current">
			<i class="icon-home"></i>
			<a href="/">Dashboard</a>
		</li>
		<li><a href="/reports/">Reports</a></li>
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
			<h3>KPI Report</h3>
			<span>Common statistics</span>
		</div>
	</div>
@stop

@section('content')


	<div class="row">
		<div class="col-md-12">
			<div class="widget box">
				<div class="widget-content no-padding">
					<?php
					?>
					<table class="table table-striped table-bordered table-hover">
						<thead>
							<tr>
								<th>-</th>
								<th><?=date("Y")-2;?></th>
								<th><?=date("Y")-1;?></th>
								<th><?=date("Y");?></th>
							</tr>
						</thead>
						<tbody>
<tr>
	<td>Turnover</td>
	<td>{{ number_format($turnover_2,2) }}</td>
	<td>{{ number_format($turnover_1,2) }}</td>
	<td>{{ number_format($turnover_0,2) }}</td>
</tr>

<tr>
	<td>Quantities</td>
	<td>{{ number_format($order_quantities_2) }}</td>
	<td>{{ number_format($order_quantities_1) }}</td>
	<td>{{ number_format($order_quantities_0) }}</td>
</tr>

<tr>
	<td>Orders count</td>
	<td>{{ $orders_count_2 }}</td>
	<td>{{ $orders_count_1 }}</td>
	<td>{{ $orders_count_0 }}</td>
</tr>

<tr>
	<td>Products (Active / Inactive)</td>
	<td colspan="3">{{ $product_count_active }} / {{ $product_count_inactive }}</td>
</tr>

<tr>
	<td>Customers (Active / Inactive)</td>
	<td colspan="3">{{ $customer_count_active }} / {{ $customer_count_inactive }}</td>
</tr>

<tr>
	<td>Unpaid Invoices</td>
	<td colspan="3">{{ number_format($total_unpaid,2) }}</td>
</tr>

<tr>
	<td>Overdue Invoices</td>
	<td colspan="3">{{ number_format($total_overdue,2) }}</td>
</tr>

<tr>
	<td colspan="4"><strong>All monetary amounts are displayed in {{ Auth::user()->company->currency_code }}</strong></td>
</tr>

						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>



@stop
