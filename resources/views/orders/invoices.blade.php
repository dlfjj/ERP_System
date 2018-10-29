@extends('layouts.default')

@section('page-module-menu')
	<li><a href="/orders/{{$order->id}}">Details</a></li>
	<li><a href="/orders/purchases/{{$order->id}}">Purchases</a></li>
	<li><a href="/orders/work-orders/{{$order->id}}">Work Orders</a></li>
	<li><a href="/orders/invoices/{{$order->id}}">Invoices / Shipments</a></li>
	<li><a href="/orders/records/{{$order->id}}">History</a></li>
@stop



@section('page-crumbs')
	<ul id="breadcrumbs" class="breadcrumb">
		<li>
			<i class="icon-home"></i>
			<a href="/">Dashboard</a>
		</li>
		<li>
			<a href="/orders/" title="">Orders</a>
		</li>
		<li>
			<a href="/orders/{{$order->id}}" title="">Details</a>
		</li>
		<li class="current">
			<a href="/orders/invoices/{{$order->id}}" title="">Invoices / Shipments</a>
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
		<!-- Page Stats -->
		<ul class="page-stats">
			<li>
				<div class="summary">
					<span>Order ID</span>
					<h3>{{$order->id}}</h3>
				</div>
			</li>
			<li>
				<div class="summary">
					<span>Status</span>
					<h3>{{$order->status}}</h3>
				</div>
			</li>
			<li>
				<div class="summary">
					<span>Order Total</span>
					<h3>{{$order->currency_code}} {{$order->gross_total}}</h3>
				</div>
			</li>
		</ul>
		<!-- /Page Stats -->

	</div>
@stop

@section('content')

<div class="row">
	<div class="col-md-12">
		<div class="widget box">
			<div class="widget-header">
				<h4><i class="icon-reorder"></i> Invoices that directly reference this Order</h4>
			</div>
			<div class="widget-content no-padding">
				<table class="table table-striped table-bordered table-hover">
					<thead>
						<tr>
							<th>Invoice</th>
							<th>Status</th>
							<th>Placed</th>
							<th>Due</th>
							<th>Part Number</th>
							<th>Quantity</th>
							<th>Price</th>
							<th>Amount</th>
						</tr>
					</thead>
					<tbody>
						@foreach($order->items as $order_item)
							@foreach($order_item->invoiceItems as $invoice_item)
								<tr>
									<td>
										@if(has_role('invoices'))
											<a href="/invoices/{{ $invoice_item->invoice_id }}">{{ $invoice_item->invoice_id }}</a>
										@else
											{{ $invoice_item->invoice_id }}
										@endif
									</td>
									<td>{{ $invoice_item->invoice->status }}</td>
									<td>{{ $invoice_item->invoice->date_issued }}</td>
									<td>{{ $invoice_item->invoice->date_due }}</td>
									<td>{{ $invoice_item->part_number }}</td>
									<td>{{ $invoice_item->quantity }}</td>
									<td>{{ $invoice_item->gross_price }}</td>
									<td>{{ $invoice_item->gross_total }}</td>
								</tr>
							@endforeach
						@endforeach
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>

@stop
