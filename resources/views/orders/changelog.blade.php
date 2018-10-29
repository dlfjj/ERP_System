@extends('layouts.default')

@section('page-module-menu')
	<li><a href="/orders/{{$order->id}}">Details</a></li>
{{--	<li><a href="/orders/purchases/{{$order->id}}">Purchases</a></li>--}}
	{{--<li><a href="/orders/changelog/invoices/{{$order->id}}">Invoices / Shipments</a></li>--}}
	<li><a href="/orders/records/{{$order->id}}">History</a></li>
@stop

@section('page-crumbs')
<ul id="breadcrumbs" class="breadcrumb">
		<li>
			<i class="icon-home"></i>
			<a href="/">Dashboard</a>
		</li>
		<li>
			<a href="/orders/" title="">Orders</a>
		</li>
		<li class="current">
			<a href="/orders/{{$order->id}}" title="">Details</a>
		</li>
	</ul>

	<ul class="crumb-buttons">
		{{--@if(has_role('orders_edit'))--}}
			{{--<li>--}}
				{{--<a href="/orders/change-customer/{{$order->id}}" class="" title=""><i class="icon-pencil"></i><span>Change Customer</span></a>--}}
			{{--</li>--}}
			{{--<li>--}}
				{{--<a href="/orders/change-status/{{$order->id}}" class="" title=""><i class="icon-pencil"></i><span>Change Status</span></a>--}}
			{{--</li>--}}
			{{--<li>--}}
				{{--<a href="/orders/link-to-purchase/{{$order->id}}" class="" title=""><i class="icon-pencil"></i><span>Link to P.O</span></a>--}}
			{{--</li>--}}
		{{--@endif--}}
		{{--<li>--}}
			{{--<a target="_new" href="/pdf/customer-orders-oc/{{$order->id}}" class="" title=""><i class="icon-print"></i><span>OC</span></a>--}}
		{{--</li>--}}
		{{--<li>--}}
			{{--<a target="_new" href="/pdf/customer-orders-pis/{{$order->id}}" class="" title=""><i class="icon-print"></i><span>PIS</span></a>--}}
		{{--</li>--}}
		<li>
			<a href="javascript:void(0);" title=""><i class="icon-calendar"></i><span><?=date('F d, Y \(\K\W:W) H:i:s');?></span></a>
		</li>
	</ul>
@stop

@section('page-header')
	<div class="page-header">
		<div class="page-title">
			@if($order->status == 'DRAFT')
				@if($order->items->count() > 0)
				<a class="btn btn-success btn-lg form-submit-conf" data-target-form="post" href="javascript:void(0);"><i class="icon-th"></i> Post Order</a>
				@endif
			@endif
		</div>
		<!-- Page Stats -->
		<ul class="page-stats">
			<li>
				<div class="summary">
					<span>Status</span>
					<h3>{!! $order->status !!}</h3>
				</div>
			</li>
			<li>
				<div class="summary">
					<span>Order Total</span>
					<h3>{{$order->currency_code}}{{$order->total}}</h3>
				</div>
			</li>
		</ul>
		<!-- /Page Stats -->

	</div>

@stop

@section('content')

<div class="row">
	<div class="col-md-12">
		<div class="widget box">
			<div class="widget-header">
				<h4><i class="icon-reorder"></i> Changelog for Order "{{ $order->id }}"</h4>
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
					@foreach(App\Models\Changelog::where('parent_model','Order')->where('parent_id',$order->id)->get() as $history)
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
