@layout('layouts.default')
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
		<li><a href="/reports/customer-products">Product by Customer</a></li>
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
				<form id="main" class="form-horizontal row-border form-validate" action="" method="POST" autocomplete="off">
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
									<label class="control-label">Product Code</label>
								    {{ Form::text('product_code',$product_code , array("class"=>"form-control")) }}
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
				<h4><i class="icon-restockorder"></i>Query Products purchased by Customer</h4>
			</div>
			<div class="widget-content">
				<div class="tabbable box-tabs">
					<ul class="nav nav-tabs">
						<li class="active"><a href="#box_tab1" data-toggle="tab">Line Itmes</a></li>
					</ul>
					<div class="tab-content">
						<div class="tab-pane active" id="box_tab1">
							<table class="table table-hover">
								<thead>
									<tr>
										<th class="cell-tight">customer name</th>
										<th class="cell-tight">Amount gross (USD)</th>
									</tr>
								</thead>
								<tbody>
<?php
	$grand_total = 0;
?>
										@if(count($results)> 0)

										@foreach($results as $customer_id => $amount)
	<?php
		$customer = Customer::find($customer_id);
		$grand_total += $amount;
	?>
										<tr class="stockorder-form-row">
											<td class="cell-tight"><a href="/customers/show/{{$customer->id}}">{{$customer->customer_name }}</a></td>
											<td class="cell-tight">{{ number_format($amount,2) }}</a></td>
										</tr>
										@endforeach
										<td>Total:</td>
										<td>{{ number_format($grand_total,2) }}</td>
                                        
										@else
										<tr class="stockorder-form-row">
											<td class="cell-tight">not found</td>
										</tr>

                                        @endif

								</tbody>
							</table>
						</div>
					</div>
				</div> <!-- /.tabbable portlet-tabs -->
			</div> <!-- /.widget-content -->
		</div> <!-- /.widget .box -->
	</div> <!-- /.col-md-12 -->
@stop
