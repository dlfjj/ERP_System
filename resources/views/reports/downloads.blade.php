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
			<h3>System generated reports</h3>
			<span>Business Analysis</span>
		</div>
	</div>
@stop

@section('content')


<div class="row">
	<div class="col-md-12">
		<div class="widget box">
			<div class="widget-header">
				<h4><i class="icon-reorder"></i> Download scheduled reports</h4>
				<div class="toolbar no-padding">
					<div class="btn-group">
						<span class="btn btn-xs widget-collapse"><i class="icon-angle-down"></i></span>
					</div>
				</div>
			</div>
			<div class="widget-content no-padding">
				<table class="table table-striped table-bordered table-hover">
					<thead>
						<tr>
							<th>Filename</th>
							<th>Filesize</th>
							<th>Last updated</th>
							<th>Download</th>
							<th>Daily</th>
							<th>Mondays</th>
							<th>1st of Month</th>
						</tr>
					</thead>
					<tbody>
					@foreach($files as $filename)
						<?php
							if(stristr($filename,".pdf") != false){
								continue;
							}
							
							$subscription = ReportSubscription::where('user_id',Auth::user()->id)->where('file_name',$filename)->first();

							if($filename == "customer_specific_prices.xlsx"){
								if(return_company_id() != 1){
									continue;
								}
							}

						?>
						<tr>
							<td>{{ $filename }}</td>
							<td>{{ formatSizeUnits(filesize($file_path . "/" . $filename)) }}</td>
							<td>{{ date ("Y-m-d H:i:s", filemtime($file_path . "/" . $filename)) }}</td>
							<td>
								<form method="POST">
									<input type="hidden" name="file_name" value='{{ $file_path ."/", $filename }}'>
									<input type="submit" name="action" value="Download" class="btn btn-xs btn-default">
								</form>
							</td>
							<td>
								@if($subscription && $subscription->schedule == 'daily')
									<form method="POST">
										<input type="hidden" name="schedule" value='daily' />
										<input type="hidden" name="file_name" value='{{ $file_path ."/", $filename }}'>
										<input type="submit" name="action" value="Unsubscribe" class="btn btn-xs btn-warning">
									</form>
								@else
									@if(!$subscription)
									<form method="POST">
										<input type="hidden" name="schedule" value='daily' />
										<input type="hidden" name="file_name" value='{{ $file_path ."/", $filename }}'>
										<input type="submit" name="action" value="Subscribe" class="btn btn-xs btn-default">
									</form>
									@endif
								@endif
							</td>
							<td>
								@if($subscription && $subscription->schedule == 'weekly')
									<form method="POST">
										<input type="hidden" name="schedule" value='weekly' />
										<input type="hidden" name="file_name" value='{{ $file_path ."/", $filename }}'>
										<input type="submit" name="action" value="Unsubscribe" class="btn btn-xs btn-warning">
									</form>
								@else
									@if(!$subscription)
									<form method="POST">
										<input type="hidden" name="schedule" value='weekly' />
										<input type="hidden" name="file_name" value='{{ $file_path ."/", $filename }}'>
										<input type="submit" name="action" value="Subscribe" class="btn btn-xs btn-default">
									</form>
									@endif
								@endif
							</td>
							<td>
								@if($subscription && $subscription->schedule == 'monthly')
									<form method="POST">
										<input type="hidden" name="schedule" value='monthly' />
										<input type="hidden" name="file_name" value='{{ $file_path ."/", $filename }}'>
										<input type="submit" name="action" value="Subscribe" class="btn btn-xs btn-warning">
									</form>
								@else
									@if(!$subscription)
									<form method="POST">
										<input type="hidden" name="schedule" value='monthly' />
										<input type="hidden" name="file_name" value='{{ $file_path ."/", $filename }}'>
										<input type="submit" name="action" value="Unsubscribe" class="btn btn-xs btn-default">
									</form>
									@endif
								@endif
							</td>
						</tr>
					@endforeach
					@if(count($files) == 0)
						<tr>
							<td colspan="7">Nothing available</td>
						</tr>
					@endif
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>

@stop
