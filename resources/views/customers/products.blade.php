@extends('layouts.default')

@section('page-module-menu')
	<li><a href="/customer/getShow/{{$customer->id}}">General</a></li>
	<li><a href="/customer/getHistory/{{$customer->id}}">History</a></li>
	<li><a href="/customer/getProducts/{{$customer->id}}">Products</a></li>
@stop


@section('page-crumbs')
	<ul id="breadcrumbs" class="breadcrumb">
		<li>
			<i class="icon-home"></i>
			<a href="/dashboard">Dashboard</a>
		</li>
		<li>
			<a href="/customer/getIndex" title="">Customers</a>
		</li>
		<li class="current">
			<a href="/customer/getShow/{{$customer->id}}" title="">Details</a>
		</li>
	</ul>

	<ul class="crumb-buttons">
        @if(has_role('customers_pricelist'))
        <li><a href="/customer/getPricelist/{{ $customer->id }}" class="" title=""><i class="icon-table"></i><span>Pricelist</span></a></li>
        @endif
        @if(has_role('customers_opos'))
       <li><a href="/customer/getOpos/{{ $customer->id }}" class="" title=""><i class="icon-table"></i><span>OPOS</span></a></li>
        @endif
		<li>
			<a href="javascript:void(0);" title=""><i class="icon-calendar"></i><span><?=date('F d, Y \(\K\W:W) H:i:s');?></span></a>
		</li>
	</ul>
@stop


@section('page-header')
<?php
?>
	<div class="page-header">
		<div class="page-title">
		</div>
		<ul class="page-stats">
			<!-- if(has_role('invoices')) -->
			<li>
				<div class="summary">
					<span>Outstandings</span>
					<h3></h3>
				</div>
			</li>
			<!-- endif -->
		</ul>
	</div>
@stop

@section('content')

			{{ Form::open(array("url"=>"/customers/destroy/$customer->id","method"=>"post","class"=>"form-inline","id"=>"delete")) }}
			</form>

			<form class="form-inline" id="create" action="/customers/create" method="POST">
			</form>

			<form class="form-inline" id="duplicate" action="/customers/duplicate/{{$customer->id}}" method="POST">
			</form>

<div class="row">
	<div class="col-md-12">
		<div class="widget box">
			<div class="widget-header">
				<h4><i class="icon-reorder"></i> Products bought by this customer</h4>
				<div class="toolbar no-padding">
					<div class="btn-group">
					</div>
				</div>
			</div>
			<div class="widget-content">
                <table class="table table-striped table-bordered table-hover table-chooser datatable" data-dataTable='{"aaSorting": [[ 5, "desc" ]]}'>
					<thead>
						<tr>
							<th>Product Code</th>
							<th>Product Name</th>
@foreach($years as $year)
							<th>{{ $year }}</th>
@endforeach
<th>-</th>
						</tr>
					</thead>
                    <tbody>
                    @if(count($customer_products)>0)
					@foreach($customer_products as $product_id => $qty_years)
<!-- <?php
	// $product = Product::find($product_id);
?> -->
						<tr>
								<td>{{ $product->product_code }}</td>
								<td>{{ substr($product->product_name,0,60) }}</td>
								@foreach($qty_years as $qty_year)
									<td>{{ $qty_year }}</td>
								@endforeach
<td>
	<a href="/product/getShow/{{ $product->id }}">Show</a>
</td>
						</tr>
					@endforeach
					@else
						<tr>
							<td colspan="3">Nothing found</td>
						</tr>
					@endif
					</tbody>
				</table>
			</div>
		</div>
		<!-- /Simple Table -->
	</div>
</div>

<!-- Modal dialog -->
<div class="modal fade" id="modal_add_contact">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title">Add new Contact</h4>
			</div>
			<form autocomplete="off" enctype="multipart/form-data" id="customer_contact" class="form-validate1" action="/customers/contact-add/{{$customer->id}}" method="POST">
			{!!Form::hidden('customer_id', $customer->id, array("class"=>"form-control")) !!}
			<div class="modal-body">
					<div class="form-group">
						<div class="row">
							<div class="col-md-3">
								<label class="control-label">Contact Name</label>
								{!!Form::text('contact_name', "", array("class"=>"form-control required")) !!}
							</div>
							<div class="col-md-3">
								<label class="control-label">E-Mail</label>
								{!! Form::text('username', "", array("class"=>"form-control required")) !!}
							</div>
							<div class="col-md-3">
								<label class="control-label">Mobile</label>
								{!! Form::text('contact_mobile', "", array("class"=>"form-control")) !!}
							</div>
							<div class="col-md-3">
								<label class="control-label">Skype</label>
								{!!Form::text('contact_skype', "", array("class"=>"form-control")) !!}
							</div>
						</div>
						&nbsp;
						<div class="row">
							<div class="col-md-3">
								<label class="control-label">Job Title</label>
								{!! Form::text('position', "", array("class"=>"form-control")) !!}
							</div>
                            <div class="col-md-3">
								<label class="control-label">Can Login</label>
                                {{ Form::select('can_login', array('0' => 'No','1' => 'Yes'), 1, array("class"=>"form-control")) }}
                            </div>
                            <div class="col-md-6">
								<label class="control-label">System E-Mails</label>
							{!!Form::select('system_emails', array('0' => 'No E-Mails','1' => 'Newsletter only','2' => 'Order Status only','3' => 'Newsletter+Order Status'), 3, array("class"=>"form-control")) !!}
                            </div>
						</div>
					</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
				<input type="submit" class="btn btn-primary" value="Submit">
			</div>
			</form>
		</div><!-- /.modal-content -->
	</div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<!-- Modal dialog -->
<div class="modal fade" id="modal_add_address">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title">Add new Delivery Address</h4>
			</div>
			<form enctype="multipart/form-data" id="customer_address" class="form-validate2" action="/customers/address-add/{{$customer->id}}" method="POST">
			{!! Form::hidden('customer_id', $customer->id, array("class"=>"form-control")) !!}
			<div class="modal-body">
					<div class="form-group">
						<div class="row">
							<div class="col-md-4">
								<label class="control-label">Description</label>
								{!! Form::text('description', "", array("class"=>"form-control required")) !!}
							</div>
							<div class="col-md-4">
								<label class="control-label">Street 1</label>
								{!! Form::text('address1', "", array("class"=>"form-control required")) !!}
							</div>
							<div class="col-md-4">
								<label class="control-label">City</label>
								{!! Form::text('city', "", array("class"=>"form-control required")) !!}
							</div>
						</div>
						&nbsp;
						<div class="row">
							<div class="col-md-4">
								<label class="control-label">Postal Code</label>
								{!! Form::text('postal_code', "", array("class"=>"form-control")) !!}
							</div>
							<div class="col-md-4">
								<label class="control-label">Province</label>
								{!! Form::text('province', "", array("class"=>"form-control")) !!}
							</div>
							<div class="col-md-4">
								<label class="control-label">Country</label>
								{!!Form::text('country', "" , array("class"=>"form-control")) !!}
							</div>
						</div>
					</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
				<input type="submit" class="btn btn-primary" value="Submit">
			</div>
			</form>
		</div><!-- /.modal-content -->
	</div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<div class="row">
	<div class="col-md-12 no-padding">
		<p class="record_status">Created: {{$customer->created_at}} | Created by: {{$created_user}} | Updated: {{$customer->updated_at}} | Updated by: {{$updated_user}} | <a href="/customers/changelog/{{ $customer->id }}">Changelog</a></p>
	</div>
</div>


@stop
