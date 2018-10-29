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
			<h3>Ad-hoc generated reports</h3>
			<span>Business Analysis</span>
		</div>
	</div>

@stop

@section('content')


<div class="row">
	<div class="col-md-12">
		<div class="widget box">
			<div class="widget-header">
				<h4><i class="icon-reorder"></i> Download</h4>
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
							<th>Report Name</th>
							<th>Format</th>
							<th>Download</th>
						</tr>
					</thead>
					<tbody>
						@if(has_role('company_admin'))
						<tr>
							<td>Customer E-Mail addresses that agree to newsletter</td>
							<td>Excel</td>
							<td>
								<a href="/exports/newsletter-export" class="btn btn-sm btn-success">Download</a>
							</td>
						</tr>
						<tr>
							<td>Stocklist</td>
							<td>Excel</td>
							<td>
								<a href="/exports/stocklist-export" class="btn btn-sm btn-success">Download</a>
							</td>
						</tr>
						<tr>
							<td>Expenses</td>
							<td>Excel</td>
							<td>
								<a href="/exports/expenses-export" class="btn btn-sm btn-success">Download</a>
							</td>
						</tr>


						@endif
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>

@stop
