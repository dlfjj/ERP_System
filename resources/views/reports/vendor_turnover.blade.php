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
					<h4><i class="icon-reorder"></i> Turnover by vendor based on Purchases issue date</h4>
					<div class="toolbar no-padding">
						<div class="btn-group">
							<span class="btn btn-xs widget-collapse"><i class="icon-angle-down"></i></span>
						</div>
					</div>
				</div>
				<div class="widget-content no-padding">
					<?php
						$vendors = Vendor::all();
						
						$po_states = [
							"UNDELIVERED,UNPAID",
							"UNDELIVERED,PARTIAL",
							"UNDELIVERED,PAID",
							"PARTIAL,UNPAID",
							"PARTIAL,PARTIAL",
							"PARTIAL,PAID",
							"DELIVERED,UNPAID",
							"DELIVERED,PARTIAL",
							"DELIVERED,PAID"
						];

						$purchase_totals = array();
						$total_total    = 0;
						$purchases  		= Purchase::whereIn('status',$po_states)
											->where('date_placed','>=',$date_start)
											->where('date_placed','<=',$date_end)
											->get();
	
						foreach($purchases as $purchase){
							if(!isset($purchase_totals[$purchase->vendor_id])){
								$purchase_totals[$purchase->vendor_id] = 0;	
							}
							$purchase_totals[$purchase->vendor_id] += $purchase->getGrossTotal($currency_code);
							$total_total += $purchase->getGrossTotal($currency_code);
						}
					?>
					<table class="table table-striped table-bordered table-hover datatable" data-dataTable='{"aaSorting": [[ 1, "desc" ]]}'>
						<thead>
							<tr>
								<th>Vendor Name</th>
								<th>Amount {{ $currency_code }}</th>
								<th>Percentage %</th>
							</tr>
						</thead>
						<tbody>
							@foreach($vendors as $vendor)
								<?php if(!isset($purchase_totals[$vendor->id]) || $purchase_totals[$vendor->id] == 0):?>
									<?php continue;?>
								<?php endif;?>
								<tr>
									<td><a href="/vendors/show/{{$vendor->id}}">{{ $vendor->company_name }}</a></td>
									<td>
										<?php if(isset($purchase_totals[$vendor->id])):?>
											<?php $vendor_total = $purchase_totals[$vendor->id]; ?>
										<?php else:?>
											<?php $vendor_total = 0; ?>
										<?php endif;?>
										{{ round($vendor_total,2) }}
									</td>
									<td>
										<?php
											if($vendor_total > 0 && $total_total > 0){
												$percentage = $vendor_total / $total_total * 100;
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
