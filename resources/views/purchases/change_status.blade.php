@extends('layouts.default')

@section('page-crumbs')

	<ul id="breadcrumbs" class="breadcrumb">
		<li>
			<i class="icon-home"></i>
			<a href="/">Dashboard</a>
		</li>
		<li>
			<a href="/purchases/" title="">Purchases</a>
		</li>
		<li>
			<a href="/purchases/{{$purchase->id}}" title="">Details</a>
		</li>
		<li class="current">
			<a href="/purchases/change_status/{{$purchase->id}}" title="">Change Status</a>
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
			<a class="btn btn-default" href="/purchases/{{ $purchase->id }}">Cancel</a>
		</div>
		<!-- Page Stats -->
		<ul class="page-stats">
			<li>
				<div class="summary">
					<span>Purchase ID</span>
					<h3>{{$purchase->id}}</h3>
				</div>
			</li>
			<li>
				<div class="summary">
					<span>Status</span>
					<h3>{{$purchase->status}}</h3>
				</div>
			</li>
			<li>
			<li>
				<div class="summary">
					<span>Purchase Total</span>
					<h3>{{$purchase->currency_code}} {{$purchase->gross_total}}</h3>
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
				<h4><i class="icon-reorder"></i> Please confirm</h4>
			</div>
			<div class="widget-content">
				<h5>Are you sure you want to change the Status of this Purchase? Be careful what you are doing.</h5>
				<ul>
					<li>You can only Void a P.O if there are no deliveries, inspections or payments recorded yet</li>
				</ul>
                {!! Form::open(['method'=>'POST','action'=>['PurchaseController@postChangeStatus',$purchase->id],'files' =>false], array('enctype'=>'multipart/form-data','id'=>'main','class' => 'form-horizontal row-border form-validate','autocomplete'=>'off')) !!}
				{{--<form autocomplete="off" enctype="multipart/form-data" id="main" class="form-horizontal row-border form-validate" action="" method="POST">--}}
					<div class="form-group">
						<div class="col-md-12">
							<div class="row">
								<div class="col-md-12">
									<input type="hidden" name="id" class="" value="{{ $purchase->id }}">
								</div>
							</div>
						</div>
					</div>
					<div class="form-actions">
						<!--
							<input type="submit" name="status" value="OPEN" class="btn btn-default pull-right">
							<input type="submit" name="status" value="CLOSED" class="btn btn-default pull-right">
							<input type="submit" name="status" value="DRAFT" class="btn btn-default pull-right">
						-->
						<input type="submit" name="status" value="VOID" class="btn btn-default pull-right">
					</div>
				{{--</form>--}}
                {{ Form::close() }}
			</div>
		</div>
	</div>
</div>

@stop
