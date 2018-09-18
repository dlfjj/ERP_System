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

			<h3>Data Export</h3>

		</div>

	</div>

@stop



@section('content')

<div class="row">

	<div class="col-md-12">

		<div class="widget box">

			<div class="widget-header">

				<h4><i class="icon-reorder"></i> Choose Export Scope</h4>

				<div class="toolbar no-padding">

					<div class="btn-group">

						<span class="btn btn-xs widget-collapse"><i class="icon-angle-down"></i></span>

					</div>

				</div>

			</div>

			<div class="widget-content">

				<div class="row">

					<div class="col-md-12">

						<form class="form-inline" action="" method="POST">

							<input type="submit" class="btn btn-lg btn-warning" name="action" value="Export Products" />

<!-- if(has_role('admin')) -->

							<input type="submit" class="btn btn-lg btn-warning" name="action" value="Export Prices" />

<!-- endif -->

						</form>

					</div>

				</div>

			</div>

		</div>

	</div>

</div>

@stop

