@extends('layouts.default')



@section('page-module-menu')
	<li><a href="/product/getIndex">Products</a></li>
	<!-- if(has_role('products_export')) -->
        <li><a href="/product/getExport">Export</a></li>
	<!-- endif -->
	<!-- if(has_role('products_import')) -->
        <li><a href="/product/getImport">Import</a></li>
	<!-- endif -->
@stop



@section('page-crumbs')

	<ul id="breadcrumbs" class="breadcrumb">

		<li>

			<i class="icon-home"></i>

			<a href="/">Dashboard</a>

		</li>

		<li class="current">

			<a href="/products/" title="">Products</a>

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

			<h3>Data Import</h3>

		</div>

	</div>

@stop



@section('content')



@if($has_file == false)

<div class="row">

	<div class="col-md-12">

		<div class="widget box">

			<div class="widget-header">

				<h4><i class="icon-reorder"></i> Choose File to Import</h4>

				<div class="toolbar no-padding">

					<div class="btn-group">

						<span class="btn btn-xs widget-collapse"><i class="icon-angle-down"></i></span>

					</div>

				</div>

			</div>

			<div class="widget-content">

				<form autocomplete="off" enctype="multipart/form-data" id="main" class="form-vertical row-border form-validate" action="" method="POST">

					<div class="form-group">

						<div class="row">

							<div class="col-md-3">

								<input type="file" name="file" data-style="fileinput">

							</div>

							<div class="col-md-3">

								<input type="submit" class="btn btn-lg btn-warning" name="submit" value="Import" />

							</div>

							<div class="col-md-6">

								<ul>

									<li>File must adhere strictly to format</li>

									<li>Uploading can take time. Dont interrupt the process or close your browser</li>

								</ul>

							</div>

						</div>

					</div>

				</form>

			</div>

		</div>

	</div>

</div>

@else 

<div class="row">

	<div class="col-md-12">

		<div class="widget box">

			<div class="widget-header">

				<h4><i class="icon-reorder"></i> Upload detected!</h4>

				<div class="toolbar no-padding">

					<div class="btn-group">

						<span class="btn btn-xs widget-collapse"><i class="icon-angle-down"></i></span>

					</div>

				</div>

			</div>

			<div class="widget-content">

				<form autocomplete="off" enctype="multipart/form-data" id="main" class="form-vertical row-border form-validate" action="/products/import-do" method="POST">

					<div class="form-group">

						<div class="row">

							<div class="col-md-12">

								<h4>A file is ready for processing!</h4>

							</div>

						</div>

					</div>

					<div class="form-group">

						<div class="row">

							<div class="col-md-2">

								<input type="submit" class="btn btn-lg btn-default" name="action" value="Cancel" />

							</div>

							<div class="col-md-2">

								<input type="submit" class="btn btn-lg btn-warning" name="action" value="Process" />

							</div>

							<div class="col-md-6">

								<ul>

									<li>Processing takes time. Do not interrupt the process or close your browser</li>

								</ul>

							</div>

						</div>

					</div>

				</form>

			</div>

		</div>

	</div>

</div>



@endif



@if($todays_updates->count() > 0 && $has_file == false)

<div class="row">

	<div class="col-md-12">

		<div class="widget box">

			<div class="widget-header">

				<h4><i class="icon-reorder"></i> Todays price updates</h4>

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

							<th class="cell-tight">Product MPN</th>

							<th class="cell-tight">Old Base Price 20</th>

							<th class="cell-tight">New Base Price 20</th>

							<th class="cell-tight">Old Base Price 40</th>

							<th class="cell-tight">New Base Price 40</th>

						</tr>

					</thead>

					<tbody>

						@foreach ($todays_updates as $update)

						<tr>

							<td><a href="/products/show/{{ $update->product->id }}">{{ $update->product->mpn }}</a></td>

							<td>{{ $update->base_price_20 }}</td>

							<td>{{ $update->product->base_price_20 }}</td>

							<td>{{ $update->base_price_40 }}</td>

							<td>{{ $update->product->base_price_40 }}</td>

						</tr>

						@endforeach

					</tbody>

				</table>

			</div>

		</div>

	</div>

</div>

@endif



<div class="row">

	<div class="col-md-12">

		<div class="widget box">

			<div class="widget-header">

				<h4><i class="icon-reorder"></i> Recent System Log</h4>

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

							<th class="cell-tight">Timestamp</th>

							<th class="cell-tight">Module</th>

							<th class="cell-tight">Severity</th>

							<th class="cell-tight">Message</th>

						</tr>

					</thead>

					

					<tbody>

						@foreach ($messages as $message)

						<tr>

							<td>{{ $message->created_at }}</td>

							<td>{{ $message->module }}</td>

							<td>{{ $message->severity }}</td>

							<td>{{ $message->message }}</td>

						</tr>

						@endforeach

					</tbody>

				</table>

			</div>

		</div>

	</div>

</div>







@stop

