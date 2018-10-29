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
					<h4><i class="icon-reorder"></i> Purchases where there are Deliveries earlier than the required Date</h4>
					<div class="toolbar no-padding">
						<div class="btn-group">
							<span class="btn btn-xs widget-collapse"><i class="icon-angle-down"></i></span>
						</div>
					</div>
				</div>
				<div class="widget-content no-padding">
					<?php
						$early_deliveries = Purchase::whereIn('STATUS',["UNDELIVERED,UNPAID","UNDELIVERED,PARTIAL","UNDELIVERED,PAID","PARTIAL,UNPAID","PARTIAL,PARTIAL","PARTIAL,PAID"])
							->where('date_required','>',date("Y-m-d"))
							->get();
					?>
					<table class="table table-striped table-bordered table-hover datatable" data-dataTable='{"aaSorting": [[ 2, "desc" ]]}'>
						<thead>
							<tr>
								<th>PO ID</th>
								<th>Placed</th>
								<th>Required</th>
								<th>Vendor Code</th>
								<th>Vendor Name</th>
								<th>CUR</th>
								<th>Gr. Total</th>
								<th></th>
							</tr>
						</thead>
						<tbody>
							@foreach($early_deliveries as $early_delivery)
								<?php
									if($early_delivery->deliveries->count() == 0){
										continue;
									}
									$po_value = convert_currency($early_delivery->currency_code,Auth::user()->company->currency_code,$early_delivery->gross_total);
									$po_value = round($po_value,2);
								?>
								<tr>
									<td>{{ $early_delivery->id }}</td>
									<td>{{ $early_delivery->date_placed }}</td>
									<td>{{ $early_delivery->date_required }}</td>
									<td>{{ $early_delivery->vendor->code }}</td>
									<td>{{ $early_delivery->vendor->company_name }}</td>
									<td>{{ Auth::user()->company->currency_code }}</td>
									<td>
										{{ $po_value }}
									</td>
									<td><a href="/purchases/receive/{{$early_delivery->id}}">View</a></td>
								</tr>
							@endforeach
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>

@stop
