@layout('layouts.default')

@section('page-module-menu')
    <li><a href="/products/show/{{$product->id}}">General</a></li>
	<li><a href="/products/attachments/{{$product->id}}">Attachments</a></li>
	<li><a href="/products/attributes/{{$product->id}}">Attributes</a></li>
	<li><a href="/products/prices/{{$product->id}}">Prices</a></li>
	<li><a href="/products/vendors/{{$product->id}}">Vendors</a></li>
	<li><a href="/products/stocks/{{$product->id}}">Stocks</a></li>
	<li><a href="/products/setup/{{$product->id}}">Setup</a></li>
	@if(return_company_id() == 1)<li><a href="/products/sync/{{$product->id}}">Sync</a></li>@endif
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
			<a href="/products/vendors/{{$product->id}}" title="">Vendors</a>
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
<div class="row">
	<div class="col-md-12">
		<div class="widget box">
			<div class="widget-header">
				<h4><i class="icon-reorder"></i> Vendors for Product "{{ $product->part_number }}"</h4>
				<div class="toolbar no-padding">
					<div class="btn-group">
					</div>
				</div>
			</div>
			<div class="widget-content">
				<table class="table table-hover">
					<thead>
						<tr>
							<th>Pref.</th>
							<th>Vendor</th>
							<th>Part #</th>
							<th>Lead(d)</th>
							<th>MOQ</th>
                            <th>Tax</th>
							<th>CUR</th>
							<th>Nt. Price</th>
							<th>Gr. Price</th>
							<th>in {{ Auth::user()->company->currency_code }}</th>
							<th>Status</th>
							<th class="align-right"></th>
						</tr>
					</thead>
					<tbody>
					@if(count($product->vendors)>0)
					@foreach($product->vendors->sortBy('vendor_id') as $vendor)
						<tr>
							<td>{{ substr($vendor->preferred,0,1) }}</td>
							<td><a href="/vendors/show/{{ $vendor->vendor->id }}">{{$vendor->vendor->customer_name}}</a></td>
							<td>{{$vendor->part_number}}</td>
							<td>{{$vendor->lead_time}}</td>
							<td>{{$vendor->moq}}</td>
							<td>{{$vendor->vendor->taxcode->name }}</td>
							<td>{{$vendor->vendor->currency_code}}</td>
							<td>{{round(return_net_price($vendor->price, $vendor->vendor->taxcode->percent),3) }}</td>
							<td>{{$vendor->price}}</td>
							<td>{{ number_format(convert_currency($vendor->vendor->currency_code,Auth::user()->company->currency_code,$vendor->price),4) }}</td>
							<td>
								@if($vendor->status == "ACTIVE" && $vendor->vendor->status == 'ACTIVE')
									<span class="label label-success">ACTIVE</span>
								@else
									<span class="label label-default">INACTIVE</span>
								@endif
							</td>
							<td class="align-right">
								@if(has_role('products_edit'))
								<span class="btn-group">
									{{ Form::open(array("url"=>"/products/vendors-delete/$vendor->id","method"=>"post","class"=>"form-inline","id"=>"v_$vendor->id")) }}
										<a href="javascript:void(0);" class="btn btn-xs form-submit-conf"><i class="icon-trash"></i></a>
									{{ Form::close() }}
								</span>
									@if($vendor->vendor->status == 'ACTIVE')
										<a href="/products/vendors/{{$product->id}}/{{$vendor->id}}" class="btn btn-xs"><i class="icon-pencil"></i></a>
									@endif
								@endif
							</td>
						</tr>
					@endforeach
					@else
						<tr>
							<td colspan="6">Nothing found</td>
						</tr>
					@endif
					</tbody>
				</table>
			</div>
		</div>
		<!-- /Simple Table -->
	</div>
</div>

<div class="row">
	<div class="col-md-12">
		<div class="widget box">
			<div class="widget-header">
				<h4><i class="icon-reorder"></i> {{ ($product_vendor == null) ? "Associate Vendor to Product" : "Update associated vendor" }}</h4>
			</div>
			<div class="widget-content">
				<form enctype="multipart/form-data" id="main" autocomplete="off" class="form-vertical row-border form-validate" action="/products/vendors-update/{{$product->id}}" method="POST">
					<div class="form-group">
						@if($product_vendor != null)
							{{ Form::hidden('id', $product_vendor->id, array()) }}
						@endif
						<div class="row">
							<div class="col-md-2">
								<label class="control-label">Preferred</label>
								{{ Form::select('preferred', array('Yes' => 'Yes','No' => 'No'), (isset($product_vendor->preferred) ? $product_vendor->preferred: ""), array("class"=>"form-control")) }}
							</div>
							<div class="col-md-2">
								<label class="control-label">Vendor Name</label>
								@if($product_vendor != null && isset($select_vendors[$product_vendor->vendor_id]))
									{{ Form::select('vendor_id', $select_vendors, (isset($product_vendor->vendor_id) ? $product_vendor->vendor_id : ""), array("class"=>"select2 col-md-12 full-width-fix")) }}
								@else
									{{ Form::select('vendor_id', $select_vendors, (isset($product_vendor->vendor_id) ? $product_vendor->vendor_id : ""), array("class"=>"select2 col-md-12 full-width-fix")) }}
								@endif
							</div>
							<div class="col-md-2">
								<label class="control-label">Part #</label>
								{{ Form::text('part_number', (isset($product_vendor->part_number) ? $product_vendor->part_number: ""), array("class"=>"form-control")) }}
							</div>
						</div>
					</div>
					<div class="form-group">
						<div class="row">
							<div class="col-md-2">
								<label class="control-label">Lead(d)</label>
								{{ Form::text('lead_time', (isset($product_vendor->lead_time) ? $product_vendor->lead_time: ""), array("class"=>"form-control")) }}
							</div>
							<div class="col-md-2">
								<label class="control-label">MOQ</label>
								{{ Form::text('moq', (isset($product_vendor->moq) ? $product_vendor->moq: ""), array("class"=>"form-control")) }}
							</div>
							<div class="col-md-2">
								<label class="control-label">CUR</label>
								{{ Form::select('currency_code', $select_currency_codes, (isset($product_vendor->currency_code) ? $product_vendor->currency_code: ""), array("class"=>"form-control")) }}
							</div>
							<div class="col-md-2">
								<label class="control-label">Gross Price</label>
								{{ Form::text('price', (isset($product_vendor->price) ? $product_vendor->price : ""), array("class"=>"form-control")) }}
							</div>
							<div class="col-md-2">
								<label class="control-label">Status</label>
								{{ Form::select('status', array('ACTIVE' => 'ACTIVE','INACTIVE' => 'INACTIVE'), (isset($product_vendor->status) ? $product_vendor->status: ""), array("class"=>"form-control")) }}
							</div>
						</div>
						<div class="form-actions">
							@if(has_role('products_edit'))
							<input type="submit" value="Submit" class="btn btn-sm btn-info pull-right">
							@endif
						</div>
					</div>
				</div>
			</form>
		</div>
	</div>
</div>


@stop
