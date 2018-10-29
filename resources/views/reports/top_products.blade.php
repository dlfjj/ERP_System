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
		<li><a href="/reports/top-products">Top 50 Product</a></li>
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
			<h3>Top 50 Products based on {{ ($report_type == 'value') ? "Order value" : "Quantity" }}</h3>
			<span>Report timeframe is {{ $date_start }} - {{ $date_end }}</span>
		</div>
	</div>
@stop

@section('content')

	<div class="row">
		<div class="col-md-12">
			<div class="widget box">
				<div class="widget-header">
					<h4><i class="icon-reorder"></i> Query results</h4>
					<div class="toolbar no-padding">
						<div class="btn-group">
							<span class="btn btn-xs widget-collapse"><i class="icon-angle-down"></i></span>
						</div>
					</div>
				</div>
               <div class="widget-content">
				<table class="table table-hover">
					<thead>
						<tr>
							<th class="cell-tight">product name</th>
							<th class="cell-tight">quantity</th>
							<th class="cell-tight">Amount gross {{$currency_code}}</th>
						</tr>
					</thead>
					<tbody>
<?php
	$grand_total = 0;

	if($report_type == 'value'){
		$report_base = $results;
	} else {
		$report_base = $quantitys;
	}

?>
										@if(count($report_base)> 0)

										@foreach($report_base as $k => $v)
	<?php
		$product = Product::find($k);
		$grand_total += $results[$k]
	?>
										<tr class="stockorder-form-row">
											<td class="cell-tight"><a href="/products/show/{{$product->id}}">{{$product->product_name }}</a></td>
											<td class="cell-tight">{{ $quantitys[$k]}}</td>
											<td class="cell-tight">{{ number_format($results[$k],2) }}</td>


                                 		</tr>
<?php

	    static $top=0;
		$top++;
		if($top==50){ break;}

?>		
                                      
										@endforeach
										<td>Total:</td>
										<td>{{array_sum($quantitys)}}</td>
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
		</div>
	</div>


<div class="modal fade" id="modal_set_dates" style="overflow:hidden;">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title">Adjust report parameters</h4>
			</div>
			<form autocomplete="off" enctype="multipart/form-data" class="" action="" method="POST">

			<div class="modal-body">
                <div class="form-group">
                    <div class="row">
                        <div class="col-md-6">
							{{ Form::text('date_start',$date_start, array("class"=>"form-control datepicker")) }}
							<span class="help-block">Date start</span>
                        </div>
                        <div class="col-md-6">
							{{ Form::text('date_end',$date_end, array("class"=>"form-control datepicker")) }}
							<span class="help-block">Date end</span>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <div class="row">
                        <div class="col-md-6">
							{{ Form::hidden('currency_code',Auth::user()->company->currency_code, array("class"=>"form-control")) }}
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
