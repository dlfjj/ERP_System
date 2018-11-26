@extends('layouts.default')

@section('page-module-menu')
	<li><a href="/reports">Reports</a></li>
@stop

@section('page-crumbs')
	<ul id="breadcrumbs" class="breadcrumb">
		<li>
			<i class="icon-home"></i>
			<a href="/">Dashboard</a>
		</li>
		<li>
			<a href="/reports">Reports</a>
		</li>
		<li class="current">
			<a>Overview</a>
		</li>
	</ul>

	<ul class="crumb-buttons">
		<li><a href="#modal_set_dates" data-toggle="modal" class="" title=""><i class="icon-cog"></i><span>Report Scope</span></a></li>
		<li>
			<a href="javascript:void(0);" title=""><i class="icon-calendar"></i><span><?=date('F d, Y \(\K\W:W) H:i:s');?></span></a>
		</li>
	</ul>
@stop

@section('page-header')
	<div class="page-header">
		<div class="page-title">
			<h3>Overview</h3>
			<span>Business KPI's {{ $date_start }} - {{ $date_end }}</span>
		</div>
	</div>
@stop

@section('content')
	<div class="row row-bg"> <!-- .row-bg -->
        <div class="col-sm-6 col-md-3">
			<div class="statbox widget box box-shadow">
				<div class="widget-content">
					<div class="visual cyan">
						<i class="icon-money"></i>
					</div>
					<div class="title">GROSS PROFIT</div>
					<div class="value">{{ $currency_code }} {{ number_format($gross_profit,2) }}</div>
					<a class="more" href="javascript:void(0);">View More <i class="pull-right icon-angle-right"></i></a>
				</div>
			</div>
		</div>
    </div>

	<!--=== Statboxes ===-->


	<div class="row row-bg"> <!-- .row-bg -->
		<div class="col-sm-6 col-md-3">
			<div class="statbox widget box box-shadow">
				<div class="widget-content">
					<div class="visual cyan">
						<i class="icon-money"></i>
					</div>
					<div class="title">PO'S PLACED</div>
					<div class="value">{{ $currency_code }} {{ number_format($po_placed_total,2) }}</div>
					<a class="more" href="javascript:void(0);">View More <i class="pull-right icon-angle-right"></i></a>
				</div>
			</div> <!-- /.smallstat -->
		</div> <!-- /.col-md-3 -->

		<div class="col-sm-6 col-md-3">
			<div class="statbox widget box box-shadow">
				<div class="widget-content">
					<div class="visual cyan">
						<i class="icon-money"></i>
					</div>
					<div class="title">PO'S PAID</div>
					<div class="value">{{ $currency_code }} {{ number_format($po_payments_total,2) }}</div>
					<a class="more" href="javascript:void(0);">View More <i class="pull-right icon-angle-right"></i></a>
				</div>
			</div> <!-- /.smallstat -->
		</div> <!-- /.col-md-3 -->

		<div class="col-sm-6 col-md-3">
			<div class="statbox widget box box-shadow">
				<div class="widget-content">
					<div class="visual cyan">
						<i class="icon-money"></i>
					</div>
					<div class="title">EXPENSES</div>
					<div class="value">{{ $currency_code }} {{ number_format($expenses_total,2) }}</div>
					<a class="more" href="/reports/dashboard-expenses">View More <i class="pull-right icon-angle-right"></i></a>
				</div>
			</div> <!-- /.smallstat -->
		</div> <!-- /.col-md-3 -->

<!--
		<div class="col-sm-6 col-md-3">
			<div class="statbox widget box box-shadow">
				<div class="widget-content">
					<div class="visual cyan">
						<i class="icon-money"></i>
					</div>
					<div class="title">&nbsp;</div>
					<div class="value">&nbsp;</div>
					<a class="more" href="javascript:void(0);">View More <i class="pull-right icon-angle-right"></i></a>
				</div>
			</div>
		</div>
-->


	</div> <!-- /.row -->
	<!-- /Statboxes -->

	<!--=== Statboxes ===-->
	<!-- /Statboxes -->


    <div class="row row-bg"> <!-- .row-bg -->

        <div class="col-sm-6 col-md-3 hidden-xs">
			<div class="statbox widget box box-shadow">
				<div class="widget-content">
					<div class="visual green">
						<i class="icon-money"></i>
					</div>
					<div class="title">INVOICES WRITTEN</div>
					<div class="value" title="">{{ $currency_code }} {{ number_format($invoices_written,2) }}</div>
					<a class="more" href="javascript:void(0);">View More <i class="pull-right icon-angle-right"></i></a>
				</div>
			</div> <!-- /.smallstat -->
		</div> <!-- /.col-md-3 -->

        <div class="col-sm-6 col-md-3 hidden-xs">
			<div class="statbox widget box box-shadow">
				<div class="widget-content">
					<div class="visual green">
						<i class="icon-money"></i>
					</div>
					<div class="title">GOODS SHIPPED</div>
					<div class="value" title="">{{ $currency_code }} {{ number_format($invoices_shipped,2) }}</div>
					<a class="more" href="javascript:void(0);">View More <i class="pull-right icon-angle-right"></i></a>
				</div>
			</div> <!-- /.smallstat -->
		</div> <!-- /.col-md-3 -->



		<div class="col-sm-6 col-md-3 hidden-xs">
			<div class="statbox widget box box-shadow">
				<div class="widget-content">
					<div class="visual green">
						<i class="icon-money"></i>
					</div>
					<div class="title">PAYMENTS RECEIVED</div>
					<div class="value">{{ $currency_code }} {{ number_format($invoice_payments_received,2) }}</div>
					<a class="more" href="javascript:void(0);">View More <i class="pull-right icon-angle-right"></i></a>
				</div>
			</div> <!-- /.smallstat -->
		</div> <!-- /.col-md-3 -->
	</div> <!-- /.row -->
	<!-- /Statboxes -->



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
	<div class="col-md-12 no-padding">
		<p class="record_status">Page Generated in {{ $seconds_used }} seconds</p>
	</div>
</div>

<div class="modal fade" id="modal_set_dates" style="overflow:hidden;">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title">Adjust Report Paramenters</h4>
			</div>
			<form autocomplete="off" enctype="multipart/form-data" class="" action="" method="POST">

			<div class="modal-body">
                <div class="form-group">
                    <div class="row">
                        <div class="col-md-6">
							{{ Form::text('date_start',$date_start, array("class"=>"form-control datepicker")) }}
							<span class="help-block">Date end</span>
                        </div>
                        <div class="col-md-6">
							{{ Form::text('date_end',$date_end, array("class"=>"form-control datepicker")) }}
							<span class="help-block">Date end</span>
                        </div>
                    </div>
                </div>

			</div>

			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
				<input type="submit" class="btn btn-primary" value="Submit">
			</div>

			</form>

        </div>
    </div>
</div>




@stop
