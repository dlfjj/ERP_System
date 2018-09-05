@extends('layouts.default')



@section('page-module-menu')

	<li><a href="/customers/show/{{$customer->id}}">General</a></li>

	<li><a href="/customers/history/{{$customer->id}}">History</a></li>

	<li><a href="/customers/products/{{$customer->id}}">Products</a></li>

@stop



@section('page-crumbs')

	<ul id="breadcrumbs" class="breadcrumb">

		<li>

			<i class="icon-home"></i>

			<a href="/dashboard">Dashboard</a>

		</li>

		<li>

			<a href="/customers">Customers</a>

		</li>

		<li>

			<a href="/customers/show/{{$customer->id}}">Details</a>

		</li>

		<li class="current">

			<a href="/customers/history/{{$customer->id}}" title="">History</a>

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

		</div>



		<ul class="page-stats">

			<li>

				<div class="summary">

					<span>Status</span>

					<h3>{{$customer->status}}</h3>

				</div>

			</li>

		</ul>

	</div>

@stop



@section('content')



@if(has_role('orders'))

<div class="row">

	<div class="col-md-12">

		<div class="widget box">

			<div class="widget-header">

				<h4><i class="icon-reorder"></i> Order History</h4>

				<div class="toolbar no-padding">

					<div class="btn-group">

						<span class="btn btn-xs widget-collapse"><i class="icon-angle-down"></i></span>

					</div>

				</div>

			</div>

			<div class="widget-content no-padding">

				<table class="table table-striped table-bordered table-hover datatable" data-dataTable='{"bServerSide": true, "sAjaxSource": "/customers/dt-orders/{{$customer->id}}"  }'>

					<thead>

						<tr>

							<th>ID</th>

							<th>Status</th>

							<th>Date</th>

							<th>C.O.N</th>

							<th>Currency</th>

							<th>Amount</th>

							<th>Open</th>

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

