@layout('layouts.default')

@section('page-crumbs')
	<ul id="breadcrumbs" class="breadcrumb">
		<li>
			<i class="icon-home"></i>
			<a href="/">Dashboard</a>
		</li>
		<li class="current">
			<a href="/payment_terms/" title="">Payment Terms</a>
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
					<span>Payment Term</span>
					<h3>{{ $payment_term->name }}</h3>
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
				<h4><i class="icon-reorder"></i> Update Payment Term</h4>
			</div>
			<div class="widget-content">
				<form enctype="multipart/form-data" id="main" class="form-horizontal row-border form-validate" action="" method="POST">
					<input type="hidden" name="id" class="" value="{{ $payment_term->id}}">
					<div class="form-group">
						<div class="col-md-12">
							<div class="row">
								<div class="col-md-3">
									{{ Form::text('name', $payment_term->name, array("class"=>"form-control")) }}
									<span class="help-block">Description</span>
								</div>
								<div class="col-md-2">
									{{ Form::text('credit', $payment_term->credit, array("class"=>"form-control")) }}
									<span class="help-block">Credit (d)</span>
								</div>
								<div class="col-md-2">
									{{ Form::text('sort_no', $payment_term->sort_no, array("class"=>"form-control")) }}
									<span class="help-block">Sort</span>
								</div>
								<div class="col-md-3">
								</div>
							</div>
						</div>
					</div>
					<div class="form-actions">
						<input type="submit" value="Save" class="btn btn-success pull-right">
						<a href="/payment_terms" class="btn btn-default pull-right">Cancel</a>
					</div>
				</div>
			</form>
		</div>
	</div>
</div>


@stop
