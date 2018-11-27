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
			<h3>Reports</h3>
			<span>Business Analysis</span>
		</div>
	</div>
@stop

@section('content')

	<!--=== Statboxes ===-->
	<div class="row row-bg"> <!-- .row-bg -->

		<div class="col-sm-6 col-md-3">
			<div class="statbox widget box box-shadow">
				<div class="widget-content">
					<div class="visual cyan">
						<i class="icon-truck"></i>
					</div>
					<div class="title">MNGMT DASHBOARD</div>
					<div class="value">&nbsp;</div>
					<a class="more" href="/reports/dashboard">View <i class="pull-right icon-angle-right"></i></a>
				</div>
			</div> <!-- /.smallstat -->
		</div> <!-- /.col-md-3 -->


		<div class="col-sm-6 col-md-3">
			<div class="statbox widget box box-shadow">
				<div class="widget-content">
					<div class="visual cyan">
						<i class="icon-money"></i>
					</div>
					<div class="title">KPIs</div>
					<div class="value">&nbsp;</div>
					<a class="more" href="/reports/kpi">View <i class="pull-right icon-angle-right"></i></a>
				</div>
			</div> <!-- /.smallstat -->
		</div> <!-- /.col-md-3 -->

	</div> <!-- /.row -->
	<!-- /Statboxes -->

	<div class="row">
		<div class="col-md-6">
			<div class="widget box">
				<div class="widget-header">
					<h4><i class="icon-reorder"></i> Basic Reports</h4>
					{{--<div class="toolbar no-padding">--}}
						{{--<div class="btn-group">--}}
							{{--<span class="btn btn-xs widget-collapse"><i class="icon-angle-down"></i></span>--}}
						{{--</div>--}}
					{{--</div>--}}
				</div>
				<div class="widget-content">
                    @if(has_role('products'))
                        {{--<a href="/reports/customer-products">Product by Customer (Orders)</a><br />--}}
                        {{--<a href="/reports/products-customer">Customer by Product (Orders)</a><br />--}}
                        <a href="/reports/getTopCustomer">Top 50 Customer (Orders)</a><br />
                        <a href="/reports/getTopProducts">Top 50 Products (Order value)</a><br />
                        {{--<a href="/reports/top-products/q">Top 50 Products (Quantities)</a><br />--}}
                        <a href="/reports/getStocklist">Stocklist</a><br />
                        <a href="/reports/getExpensesByCategory">Expenses by category</a><br />
                    @endif
				</div>
			</div>
		</div>
	</div>

@stop
