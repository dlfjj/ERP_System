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
			@if(has_role('products_edit'))
			{{--<form class="form-inline" id="create" action="/product/getExport" method="POST"><!--remove product/create path and and product/getExport-->--}}
				{{--<a class="btn btn-success form-submit-conf" href="javascript:void(0);" data-target-form="create"><i class="icon-plus-sign"></i> New Product</a>--}}
			{{--</form>--}}
				<a class="btn btn-success" href="/products/create"><i class="icon-plus-sign"></i> New Product</a>
                {{--<a class="btn btn-success" href="/customers/create"><i class="icon-plus-sign"></i> New Customer</a>--}}
			@endif
		</div>
	</div>
@stop

@section('content')


	<div class="row">
		<div class="col-md-12">
			<div class="panel panel-default">
                <div class="panel-heading"><i class="icon-reorder"></i> Product Index</div>
				<div class="panel-body">
					<table class="table table-hover table-bordered table-striped" id="product_table" style="width: 100%;">
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
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
@stop

@push('scripts')
	<script>
        // jquery getting data for purchase table
        $(function() {
            $('#product_table').DataTable({
                // "oLanguage": {

                    // "sSearch": "<i class='icon-search icon-large table-search-icon'></i>",
                    // "oPaginate": {
                    //     "sNext": "<i class='icon-chevron-right icon-large'></i>",
                    //     "sPrevious": "<i class='icon-chevron-left icon-large'></i>",
                        // "sFirst ": "<i class='icon-backward icon-large'></i>"
                    // }

                // },
                "pagingType": "full_numbers",
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
