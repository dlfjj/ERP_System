@layout('layouts.default')



@section('page-module-menu')

	<li><a href="/products/getShow/{{$product->id}}">General</a></li>

	<li><a href="/products/customers/{{$product->id}}">Customers</a></li>

	<li><a href="/products/vendors/{{$product->id}}">Vendors</a></li>

	<li><a href="/products/bom/{{$product->id}}">BOM</a></li>

	<li><a href="/products/attachments/{{$product->id}}">Attachments</a></li>

	<li><a href="/products/stocks/{{$product->id}}">Stocks</a></li>

	<li><a href="/products/history/{{$product->id}}">History</a></li>

@stop



@section('page-crumbs')

	<ul id="breadcrumbs" class="breadcrumb">

		<li>

			<i class="icon-home"></i>

			<a href="/">Dashboard</a>

		</li>

		<li>

			<a href="/products">Products</a>

		</li>

		<li>

			<a href="/products/show/{{$product->id}}">Details</a>

		</li>

		<li class="current">

			<a href="/products/history/{{$product->id}}" title="">History</a>

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

			<h3>{{ $product->part_number }}</h3>

			<p class="text-muted">{{ $product->title }}</p>

		</div>



		<ul class="page-stats">

			<li>

				<div class="summary">

					<span>Status</span>

					<h3>{{$product->status}}</h3>

				</div>

			</li>

		</ul>

	</div>

@stop



@section('content')



@if(has_role('purchases'))

<div class="row">

	<div class="col-md-12">

		<div class="widget box">

			<div class="widget-header">

				<h4><i class="icon-reorder"></i> Purchases</h4>

				<div class="toolbar no-padding">

					<div class="btn-group">

						<span class="btn btn-xs widget-collapse"><i class="icon-angle-down"></i></span>

					</div>

				</div>

			</div>

			<div class="widget-content no-padding">

				<table class="table table-striped table-bordered table-hover datatable" data-dataTable='{"bServerSide": true, "sAjaxSource": "/products/dt-purchases/{{$product->id}}"  }'>

					<thead>

						<tr>

							<th>P.O ID</th>

							<th>Status</th>

							<th>Date</th>

							<th>Vendor</th>

							<th>CUR</th>

							<th>Quantity</th>

							<th>Price</th>

							<th>Amount</th>

							<th>-</th>

						</tr>

					</thead>

					<tbody>

					</tbody>

				</table>

			</div>

		</div>

	</div>

</div>

@endif



@if(has_role('orders'))

<div class="row">

	<div class="col-md-12">

		<div class="widget box">

			<div class="widget-header">

				<h4><i class="icon-reorder"></i> Orders</h4>

				<div class="toolbar no-padding">

					<div class="btn-group">

						<span class="btn btn-xs widget-collapse"><i class="icon-angle-down"></i></span>

					</div>

				</div>

			</div>

			<div class="widget-content no-padding">

				<table class="table table-striped table-bordered table-hover datatable" data-dataTable='{"bServerSide": true, "sAjaxSource": "/products/dt-orders/{{$product->id}}"  }'>

					<thead>

						<tr>

							<th>Order ID</th>

							<th>Status</th>

							<th>Date</th>

							<th>Customer</th>

							<th>CUR</th>

							<th>Quantity</th>

							<th>Price</th>

							<th>Amount</th>

							<th>-</th>

						</tr>

					</thead>

					<tbody>

					</tbody>

				</table>

			</div>

		</div>

	</div>

</div>

@endif



@if(has_role('invoices'))

<div class="row">

	<div class="col-md-12">

		<div class="widget box">

			<div class="widget-header">

				<h4><i class="icon-reorder"></i> Invoices</h4>

				<div class="toolbar no-padding">

					<div class="btn-group">

						<span class="btn btn-xs widget-collapse"><i class="icon-angle-down"></i></span>

					</div>

				</div>

			</div>

			<div class="widget-content no-padding">

				<table class="table table-striped table-bordered table-hover datatable" data-dataTable='{"bServerSide": true, "sAjaxSource": "/products/dt-invoices/{{$product->id}}"  }'>

					<thead>

						<tr>

							<th>Invoice ID</th>

							<th>Status</th>

							<th>Date</th>

							<th>Customer</th>

							<th>CUR</th>

							<th>Quantity</th>

							<th>Price</th>

							<th>Amount</th>

							<th>-</th>

						</tr>

					</thead>

					<tbody>

					</tbody>

				</table>

			</div>

		</div>

	</div>

</div>

@endif



@stop

