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
				<a class="btn btn-success btn-lg form-submit-conf" href="javascript:void(0);" data-target-form="create"><i class="icon-plus-sign"></i> New Product</a>
			</form>
			<!-- endif -->
		</div>
	</div>
@stop

@section('content')


	<div class="row">
		<div class="col-md-12">
			<div class="panel panel-default">
				<div class="">
					<div class="panel-heading"><i class="icon-reorder"></i> Product Index</div>
					{{--<div class="toolbar no-padding">--}}
						{{--<div class="btn-group">--}}
							{{--<span class="btn btn-xs widget-collapse"><i class="icon-angle-down"></i></span>--}}
						{{--</div>--}}
					{{--</div>--}}
				</div>
				<div class="panel-body">
					<table class="table table-hover table-bordered table-striped" id="product_table" style="width: 100%">
						<thead>
						<tr class="table_head" style="width:10px;">
							<th class="cell-tight">Code</th>
							<th class="cell-tight">Status</th>
							<th class="cell-tight">MPN</th>
							<th class="cell-tight">Product Name</th>
							<th class="cell-tight">Sort No.</th>
							<th class="cell-tight">Stock</th>
							<th class="cell-tight">View</th>
						</tr>
						</thead>
						<tbody>
						{{--@foreach($products as $product)--}}
							{{--<tr>--}}
								{{--<td>{{$product->product_code}}</td>--}}
								{{--<td>{{$product->status}}</td>--}}
								{{--<td>{{$product->mpn}}</td>--}}
								{{--<td>{{$product->product_name}}</td>--}}
								{{--<td>{{$product->pricelist_sort}}</td>--}}
								{{--<td>{{$product->stock}}</td>--}}
								{{--<td><a href="/products/{{ $product->id }}" class="bs-tooltip" title="View"><i class="icon-search"></i></a></td>--}}
							{{--</tr>--}}
						{{--@endforeach--}}
						</tbody>
					</table>

				</div>
			</div>
		</div>
	</div>
	{{--<script>--}}
        {{--$(document).ready(function(){--}}
            {{--$('product_table').dataTable();--}}
        {{--})--}}
	{{--</script>--}}
@stop

@push('scripts')
	<script src="https://cdn.datatables.net/1.10.12/js/jquery.dataTables.min.js"></script>
	<script type="text/javascript">
        // jquery getting data for purchase table
        $(function() {
            $('#product_table').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{!! route('products/getdata') !!}',
                columns: [
                    { data: 'product_code', name: 'product_code' },
                    { data: 'status', name: 'status' },
                    { data: 'mpn', name: 'mpn' },
                    { data: 'product_name', name: 'product_name' },
                    { data: 'pricelist_sort', name: 'pricelist_sort' },
                    { data: 'stock', name: 'stock' },
                    {data: 'action', name: 'action', orderable: false, searchable: false}
                ]
            });
        });
	</script>
@endpush
