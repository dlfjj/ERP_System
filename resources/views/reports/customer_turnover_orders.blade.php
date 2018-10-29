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
								<div class="col-md-2">
									<label class="control-label">Customer</label>
									{{ Form::select('customer_id', $select_customers,$customer_id, array("class"=>"select2 col-md-12 full-width-fix")) }}
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
					<h4><i class="icon-reorder"></i> Turnover by customer based on Orders issue date</h4>
					<div class="toolbar no-padding">
						<div class="btn-group">
							<span class="btn btn-xs widget-collapse"><i class="icon-angle-down"></i></span>
						</div>
					</div>
				</div>
				<div class="widget-content no-padding">
					<?php
						$customers = Customer::all();

						$order_totals = array();
						$total_total    = 0;
						$orders  		= Order::whereIn('status',['OPEN','PARTIAL','CLOSED'])
											->where('date_placed','>=',$date_start)
											->where('date_placed','<=',$date_end);
                        if($customer_id > 0){
                            $orders = $orders->where('customer_id',$customer_id)->get();
                        } else {
                            $orders = $orders->get();
                        }
	
						foreach($orders as $order){
							if(!isset($order_totals[$order->customer_id])){
								$order_totals[$order->customer_id] = 0;	
							}
							$order_totals[$order->customer_id] += $order->getGrossTotal($currency_code);
							$total_total += $order->getGrossTotal($currency_code);
						}
					?>
					<table class="table table-striped table-bordered table-hover datatable" data-dataTable='{"aaSorting": [[ 1, "desc" ]]}'>
						<thead>
							<tr>
								<th>Customer Name</th>
								<th>Amount {{ $currency_code }}</th>
								<th>Percentage %</th>
							</tr>
						</thead>
						<tbody>
							@foreach($customers as $customer)
								<?php if(!isset($order_totals[$customer->id]) || $order_totals[$customer->id] == 0):?>
									<?php continue;?>
								<?php endif;?>
								<tr>
									<td><a href="/customers/show/{{ $customer->id }}">{{ $customer->company_name }}</a></td>
									<td>
										<?php if(isset($order_totals[$customer->id])):?>
											<?php $customer_total = $order_totals[$customer->id]; ?>
										<?php else:?>
											<?php $customer_total = 0; ?>
										<?php endif;?>
										{{ round($customer_total,2) }}
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
										{{ $percentage }}
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
