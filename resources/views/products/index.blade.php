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
			<a href="/dashboard">Dashboard</a>
		</li>
		<li class="current">
			<a href="/products" title="">Products</a>
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
			<!-- if(has_role('products_edit')) -->
			<form class="form-inline" id="create" action="/product/getExport" method="POST"><!--remove product/create path and andd product/getExport-->
				<!--  <a class="btn btn-success btn-lg form-submit-conf" href="javascript:void(0);" data-target-form="create"><i class="icon-plus-sign"></i> New Product</a> -->
			</form>
			<!-- endif -->
		</div>
	</div>
@stop

@section('content')

	<script type="text/javascript" charset="utf-8">
        /*
        $(document).ready(function() {
            $('table').dataTable( {
                "bServerSide": true,
                "sAjaxSource": "/products/dt-index",
                "aoColumnDefs": [
                    { "bSearchable": true, "bVisible": false, "aTargets": [ 3 ] }
                ]
            });
        });
        */
	</script>

	<div class="row">
		<div class="col-md-12">
			<div class="widget box">
				<div class="widget-header">
					<h4><i class="icon-reorder"></i> Product Index</h4>
					<div class="toolbar no-padding">
						<div class="btn-group">
							<span class="btn btn-xs widget-collapse"><i class="icon-angle-down"></i></span>
						</div>
					</div>
				</div>
				<div class="widget-content no-padding">
					<table class="table table-striped table-bordered table-hover table-chooser datatable" id="product_table">
						<thead>
						<tr class="table_head" style="width:10px;">
							<th class="cell-tight">Code</th>
							<th class="cell-tight">Status</th>
							<th class="cell-tight">MPN</th>
							<th>Product Name</th>
							<th>Sort No.</th>
							<th>Stock</th>
							<th>View</th>
						</tr>
						</thead>
						<tbody>
						@foreach($products as $product)
							<tr>
								<td>{{$product->product_code}}</td>
								<td>{{$product->status}}</td>
								<td>{{$product->mpn}}</td>
								<td>{{$product->product_name}}</td>
								<td>{{$product->pricelist_sort}}</td>
								<td>{{$product->stock}}</td>
								<td><a href="/products/{{ $product->id }}" class="bs-tooltip" title="View"><i class="icon-search"></i></a></td>
							</tr>
						@endforeach
						</tbody>
					</table>

				</div>
			</div>
		</div>
	</div>
	<script>
        $(document).ready(function(){
            $('product_table').dataTable();
        })
	</script>
@stop
