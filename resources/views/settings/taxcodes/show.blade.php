@extends('layouts.default')

@section('page-crumbs')
	<ul id="breadcrumbs" class="breadcrumb">
		<li>
			<i class="icon-home"></i>
			<a href="/">Dashboard</a>
		</li>
		<li>
			<a href="/settings/" title="">Settings</a>
		</li>
		<li class="current">
			<a href="taxcodes/" title="">Tax Codes Detail</a>
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
					<span>Tax Code</span>
					<h3>{{ $taxcode->name }}</h3>
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
				<h4><i class="icon-reorder"></i> Update Tax Code</h4>
			</div>
			<div class="widget-content">
                {!! Form::open(['method'=>'PUT', 'action'=> ['TaxcodeController@update', $taxcode->id], 'class'=>'form-horizontal row-border form-validate','id'=>'main','enctype'=>'multipart/form-data']) !!}
				{{--<form enctype="multipart/form-data" id="main" class="form-horizontal row-border form-validate" action="" method="POST">--}}
					<input type="hidden" name="id" class="" value="{{ $taxcode->id}}">
					<div class="form-group">
						<div class="col-md-12">
							<div class="row">
								<div class="col-md-3">
									{{ Form::text('name', $taxcode->name, array("class"=>"form-control")) }}
									<span class="help-block">Description</span>
								</div>
								<div class="col-md-2">
									{{ Form::text('percent', $taxcode->percent, array("class"=>"form-control")) }}
									<span class="help-block">Percent %</span>
								</div>
								<div class="col-md-2">
									{{ Form::text('sort_no', $taxcode->sort_no, array("class"=>"form-control")) }}
									<span class="help-block">Sort</span>
								</div>
								<div class="col-md-3">
								</div>
							</div>
						</div>
					</div>
					<div class="form-actions">
						<input type="submit" value="Save" class="btn btn-success pull-right">
						<a href="/settings/taxcodes" class="btn btn-default pull-right">Cancel</a>
					</div>
                {{--</form>--}}
                {!! Form::close() !!}
            </div>
		</div>
	</div>
</div>


@stop
