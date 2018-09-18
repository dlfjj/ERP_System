@layout('layouts.default')

@section('page-module-menu')
	<li><a href="/products/show/{{$product->id}}">General</a></li>
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
			<a href="/products/" title="">Products</a>
		</li>
		<li class="current">
			<a href="/products/show/{{$product->id}}" title="">Details</a>
		</li>
	</ul>
	<ul class="crumb-buttons">
		@if(has_role('products_edit'))
		<li>
			<a href="javascript:void(0);" class="form-submit-conf" data-target-form="duplicate" title=""><i class="icon-double-angle-right"></i><span>Duplicate</span></a>
		</li>
		@endif
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
				<h4><i class="icon-reorder"></i> Changelog for Product "{{ $product->part_number }}"</h4>
				<div class="toolbar no-padding">
					<div class="btn-group">
					</div>
				</div>
			</div>
			<div class="widget-content">
				<table class="table table-hover">
					<thead>
						<tr>
							<th>Timestamp</th>
							<th>Username</th>
							<th>Module</th>
							<th>Fieldname</th>
							<th>Old Value</th>
							<th>New Value</th>
							<th></th>
						</tr>
					</thead>
					<tbody>
					@foreach(Changelog::where('parent_model','Product')->where('parent_id',$product->id)->get() as $history)
						<tr>
								<td>{{$history->created_at}}</td>
								<td>{{$history->user->username }}</td>
								<td>{{$history->model_type}}</td>
								<td>{{$history->field_name}}</td>
								@if($history->action == 'created' || $history->action == 'deleted')
									<td colspan='3'><em>{{ $history->message }}</em></td>
								@else
									<td>{{nl2br($history->old_value)}}</td>
									<td>{{nl2br($history->new_value)}}</td>
									<td>
										<button class="btn btn-xs bs-popover" data-trigger="hover" data-placement="top" data-content="{{ $history->message }}" data-original-title="Message">?</button>
									</td>
								@endif
						</tr>
					@endforeach
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>

@stop
