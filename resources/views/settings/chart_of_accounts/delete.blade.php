@layout('layouts.default')

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
			<a href="/chart_of_accounts/{{$account->id}}" title="">Details</a>
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
				<h4><i class="icon-reorder"></i> Delete Account</h4>
			</div>
			<div class="widget-content">
				<p>Are you sure you want to delete the account <strong><em>{{$account->name}}</em></strong> ? This action cannot be undone.</p>
				<p><strong>NOTE:</strong> This deletion includes all descendants of this account and will happily remove your complete account tree.</p>
				<form enctype="multipart/form-data" id="main" class="form-horizontal row-border form-validate" action="/chart_of_accounts/delete/{{$account->id}}" method="post">
					<input type="hidden" name="account_id" class="" value="{{ $account->id}}">

					<div class="row">
						<div class="col-md-12">
						</div>
					</div>
					<div class="form-actions">
						<input type="submit" value="Yes, delete" class="btn btn-danger pull-right">
						<a href="/chart_of_accounts" class="btn btn-default pull-right">Cancel</a>
					</div>
				</div>
			</form>
		</div>
	</div>
</div>


@stop
