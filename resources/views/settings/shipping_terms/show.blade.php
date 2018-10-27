@extends('layouts.default')

@section('page-crumbs')
	<ul id="breadcrumbs" class="breadcrumb">
		<li>
			<i class="icon-home"></i>
			<a href="/">Dashboard</a>
		</li>
        <li>
            <a href="/settings/shipping_terms">Shipping Terms</a>
        </li>
		<li class="current">
			<a href="/settings/shipping_terms/{{ $shipping_term->id }}" title="">Detail</a>
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
					<span>Shipping Term</span>
					<h3>{{ $shipping_term->name }}</h3>
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
				<h4><i class="icon-reorder"></i> Update Shipping Term</h4>
			</div>
			<div class="widget-content">
				{!! Form::open(['method'=>'PUT', 'action'=> ['ShippingTermController@update', $shipping_term->id], 'class'=>'form-horizontal row-border form-validate','enctype'=>'multipart/form-data']) !!}
					<input type="hidden" name="id" class="" value="{{ $shipping_term->id}}">
					<div class="form-group">
						<div class="col-md-12">
							<div class="row">
								<div class="col-md-3">
									{{ Form::text('name', $shipping_term->name, array("class"=>"form-control")) }}
									<span class="help-block">Description</span>
								</div>
								<div class="col-md-2">
									{{ Form::input('number','sort_no', $shipping_term->sort_no, array("class"=>"form-control","step"=>"1")) }}
									<span class="help-block">Sort</span>
								</div>
								<div class="col-md-3">
								</div>
							</div>
						</div>
					</div>
					<div class="form-actions">
						<input type="submit" value="Save" class="btn btn-success pull-right">
						<a href="/settings/shipping_terms" class="btn btn-default pull-right">Cancel</a>
					</div>
                {{ Form::close() }}
			</div>
		</div>
	</div>
</div>


@stop
