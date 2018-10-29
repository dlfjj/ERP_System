@layout('layouts.default')

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
					<div class="title">Open P.O's</div>
					<div class="value">1</div>
					<a class="more" href="/purchases">View More <i class="pull-right icon-angle-right"></i></a>
				</div>
			</div> <!-- /.smallstat -->
		</div> <!-- /.col-md-3 -->

		<div class="col-sm-6 col-md-3">
			<div class="statbox widget box box-shadow">
				<div class="widget-content">
					<div class="visual yellow">
						<i class="icon-truck"></i>
					</div>
					<div class="title">Assigned to Me</div>
					<div class="value"></div>
					<a class="more" href="/purchases">View More <i class="pull-right icon-angle-right"></i></a>
				</div>
			</div> <!-- /.smallstat -->
		</div> <!-- /.col-md-3 -->

		<div class="col-sm-6 col-md-3 hidden-xs">
			<div class="statbox widget box box-shadow">
				<div class="widget-content">
					<div class="visual green">
						<i class="icon-thumbs-up-alt"></i>
					</div>
					<div class="title">Closed Count</div>
					<div class="value"></div>
					<a class="more" href="/purchases">View More <i class="pull-right icon-angle-right"></i></a>
				</div>
			</div> <!-- /.smallstat -->
		</div> <!-- /.col-md-3 -->

		<div class="col-sm-6 col-md-3 hidden-xs">
			<div class="statbox widget box box-shadow">
				<div class="widget-content">
					<div class="visual red">
						<i class="icon-user"></i>
					</div>
					<div class="title">Total Products</div>
					<div class="value"></div>
					<a class="more" href="/products">View More <i class="pull-right icon-angle-right"></i></a>
				</div>
			</div> <!-- /.smallstat -->
		</div> <!-- /.col-md-3 -->
	</div> <!-- /.row -->
	<!-- /Statboxes -->



	<div class="row">
		<div class="col-md-12">
			<div class="widget box">
				<div class="widget-header">
					<h4><i class="icon-reorder"></i> Payments received by Month based on recorded Payments</h4>
					<div class="toolbar no-padding">
						<div class="btn-group">
							<span class="btn btn-xs widget-collapse"><i class="icon-angle-down"></i></span>
						</div>
					</div>
				</div>
				<div class="widget-content no-padding">
					<?php
						$customers = Customer::all();
						$report_currency_code = "USD";

						$sql = 'SELECT
							YEAR(payment_date) as pyear,
							MONTH(payment_date) as pmonth,
							currency_code,
							SUM(amount) as amount
							FROM invoice_payments
							GROUP BY pyear,pmonth,currency_code
							ORDER BY pyear,pmonth,currency_code';

						$results = DB::select($sql);

						$invoice_totals = array();
						$total_total    = 0;
						$invoices = Invoice::where('status','PAID')->get();
	
						foreach($invoices as $invoice){
							if(!isset($invoice_totals[$invoice->customer_id])){
								$invoice_totals[$invoice->customer_id] = 0;	
							}
							$total_converted = convert_currency($invoice->currency_code,$report_currency_code,$invoice->total);
							$invoice_totals[$invoice->customer_id] += $total_converted;
							$total_total += $total_converted;
						}

						if(has_role('admin')){
						$income_date = date('Y-m');
						$sql = "
					SELECT
						invoice_id,
						DATE_FORMAT(created_at, '%Y') AS year,	
						DATE_FORMAT(created_at, '%m') AS month,
						currency_code,
						SUM(amount) AS amount
					FROM invoice_payments
					WHERE DATE_FORMAT(created_at, '%Y') = '2014'
					GROUP BY year,month,currency_code 
					ORDER BY year,month,currency_code
					";
						$monthly_income_raw = DB::select($sql);
						$monthly_income_usd = array();
						foreach($monthly_income_raw as $i){
							if(isset($monthly_income_usd[$i->month])){
								$monthly_income_usd[$i->month] += convert_currency($i->currency_code,"USD",$i->amount);
							} else {
								$monthly_income_usd[$i->month] = convert_currency($i->currency_code,"USD",$i->amount);
							}
						}
						}
						arsort($monthly_income_usd);
					?>
					<table class="table table-striped table-bordered table-hover">
						<thead>
							<tr>
								<th>Month</th>
								<th>Amount {{ $report_currency_code }}</th>
							</tr>
						</thead>
						<tbody>
							@foreach($monthly_income_usd as $month=>$amount)
								<tr>
									<td>{{ date("F", mktime(0, 0, 0, $month, 10)) }}</td>
									<td>{{ number_format($amount,2) }}</td>
								</tr>
							@endforeach
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>



@stop
