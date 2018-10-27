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
		<li>
            <a href="/settings/chart_of_accounts/" title="">Chart of Accounts</a>
        </li>
        <li class="current">
            <a href="/settings/chart_of_accounts/{{ $account->id }}" title="">Detail</a>
        </li>
	</ul>

	<ul class="crumb-buttons">
		<li class="dropdown">
			<a href="#" title="" data-toggle="dropdown"><i class="icon-tasks"></i><span>More <strong></strong></span><i class="icon-angle-down left-padding"></i></a>
			<ul class="dropdown-menu pull-right">
				<li>
					<a href="javascript:void(0);" class="form-submit-conf" data-target-form="delete" title=""><i class="icon-double-angle-right"></i><span>Delete</span></a>
				</li>
				<li>
					<a href="javascript:void(0);" class="form-submit-conf" data-target-form="duplicate" title=""><i class="icon-double-angle-right"></i><span>Duplicate</span></a>
				</li>
			</ul>
		</li>
		<li>
			<a href="javascript:void(0);" title=""><i class="icon-calendar"></i><span><?=date('F d, Y \(\K\W:W) H:i:s');?></span></a>
		</li>
	</ul>
@stop


@section('page-header')
	<div class="page-header">
		<div class="page-title">
		</div>
		<!-- Page Stats -->
		<ul class="page-stats">
		</ul>
		<!-- /Page Stats -->
	</div>
@stop

@section('content')

<div class="row">
	<div class="col-md-12">
		<div class="widget box">
			<div class="widget-header">
				<h4><i class="icon-reorder"></i> Update account</h4>
			</div>
			<div class="widget-content">
                {!! Form::open(['method'=>'PATCH', 'action'=> ['ChartOfAccountController@update', $account->id], 'class'=>'form-horizontal row-border form-validate','enctype'=>'multipart/form-data']) !!}

                <form enctype="multipart/form-data" id="main" class="form-horizontal row-border form-validate" action="/chart_of_accounts/update/{{$account->id}}" method="POST">
					<div class="form-group">
						<div class="col-md-12">
							<div class="row">
								<div class="col-md-2">
									<label class="control-label">Code</label>
									{{ Form::text('code', $account->code, array("class"=>"form-control")) }}
									{{ Form::hidden('account_id', $account_id, array("class"=>"form-control")) }}
								</div>
								<div class="col-md-2">
									<label class="control-label">Type</label>
									{{ Form::select('type', $select_account_type, $account->type, array("class"=>"form-control")) }}
								</div>
								<div class="col-md-4">
									<label class="control-label">Account Name</label>
									{{ Form::text('name', $account->name, array("class"=>"form-control")) }}
								</div>
							</div>
						</div>
					</div>
					<div class="form-actions">
						<input type="submit" value="Save" class="btn btn-success pull-right">
						<a href="/settings/chart_of_accounts" class="btn btn-default pull-right">Cancel</a>
					</div>
				</form>
                {!! Form::close() !!}
			</div>
		</div>
	</div>
</div>


@stop
