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

	<?php
		$cur_year = date("Y");
		$cur_month= date("m");

		$unpaid_amount = 0;
		$unpaid_pos = Purchase::where('status','!=','VOID')
			->where('payment_status','!=','PAID')
			->get();
		foreach($unpaid_pos as $unpaid_po) {
			$unpaid_amount += convert_currency($unpaid_po->currency_code,"CNY",$unpaid_po->total,date("Y-m-d"));
			foreach($unpaid_po->payments as $payment){
				$unpaid_amount -= convert_currency($payment->currency_code,"CNY",$payment->amount,date("Y-m-d"));
			}
		}

		$open_order_amount = 0;
		$open_orders = Order::where('status','!=','VOID')
			->get();
		foreach($open_orders as $open_order){
			$open_order_amount = convert_currency($open_order->currency_code,"CNY",$open_order->total,date("Y-m-d"));	
		}

		$invoices_amount = 0;
		$invoices = Invoice::where('status','!=','VOID')
			->where('status','!=','PAID')
			->get();

		foreach($invoices as $invoice){
			$invoices_amount += convert_currency($invoice->currency_code,"CNY",$invoice->total,date("Y-m-d"));	
		}

		$expenses_amount = 0;
		$expenses = DB::table('expenses')
			->whereRaw("YEAR(date_created) = $cur_year")
			->whereRaw("MONTH(date_created) = $cur_month")
			->get();

		foreach($expenses as $expense){
			$expenses_amount += convert_currency($expense->currency_code, "CNY",$expense->amount,date("Y-m-d"));
		}

	?>

	<!--=== Statboxes ===-->
	<div class="row row-bg"> <!-- .row-bg -->
		<div class="col-sm-6 col-md-3">
			<div class="statbox widget box box-shadow">
				<div class="widget-content">
					<div class="visual cyan">
						<i class="icon-truck"></i>
					</div>
					<div class="title">Unpaid P.O's</div>
					<div class="value">{{ number_format($unpaid_amount,2) }}</div>
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
					<div class="title">Open Orders</div>
					<div class="value">{{ number_format($open_order_amount,2) }}</div>
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
					<div class="title">Open Invoices</div>
					<div class="value">{{ number_format($invoices_amount,2) }}</div>
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
					<div class="title">Expenses this month</div>
					<div class="value">{{ number_format($expenses_amount,2) }}</div>
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
					<h4><i class="icon-reorder"></i> Turnover by Customer based on Paid Invoices</h4>
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

					?>
					<table class="table table-striped table-bordered table-hover datatable" data-dataTable='{"aaSorting": [[ 1, "desc" ]]}'>
						<thead>
							<tr>
								<th>Customer Name</th>
								<th>Amount</th>
								<th>Percentage</th>
							</tr>
						</thead>
						<tbody>
							@foreach($customers as $customer)
								<tr>
									<td>{{ $customer->company_name }}</td>
									<td>
										<?php if(isset($invoice_totals[$customer->id])):?>
											<?php $customer_total = $invoice_totals[$customer->id]; ?>
										<?php else:?>
											<?php $customer_total = 0; ?>
										<?php endif;?>
										{{ $report_currency_code}} {{ number_format($customer_total,2) }}
									</td>
									<td>
										<?php
											if($customer_total > 0 && $total_total > 0){
												$percentage = $customer_total / $total_total * 100;
												$percentage = round($percentage,2);
											} else {
												$percentage = 0;	
											}
										?>
										% {{ $percentage }}
									</td>
								</tr>
							@endforeach
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>



@stop
