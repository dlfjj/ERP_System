@extends('layouts.default')

@section('page-crumbs')

	<ul id="breadcrumbs" class="breadcrumb">
		<li>
			<i class="icon-home"></i>
			<a href="/">Dashboard</a>
		</li>
		<li>
			<a href="/customers/" title="">Customers</a>
		</li>
		<li class="current">
			<a href="/customers/show/{{$customer->id}}" title="">Details</a>
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
					<span>Customer</span>
					<h3>{{$customer->company_name}}</h3>
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
				<h4><i class="icon-reorder"></i> Update Customer Contact</h4>
			</div>
			<div class="widget-content">

				{!! Form::open(array('url' => "/customer/contact-edit/$customer_contact->id", 'enctype' => 'multipart/form-data', 'id' => 'main', 'method' => 'post', 'class' => 'orm-horizontal row-border form-validate', 'autocomplete' => 'off')) !!}
					<div class="form-group">
						<div class="col-md-12">
							<div class="row">
								<div class="col-md-12">
									<input type="hidden" name="customer_id" class="" value="{{ $customer->id}}">
									<input type="hidden" name="id" class="" value="{{ $customer_contact->id}}">
								</div>
							</div>
							<div class="row">
								<div class="col-md-2">
									<input type="text" name="contact_name" class="form-control" value="{{ $customer_contact->contact_name}}">
									<span class="help-block">Contact Name</span>
								</div>
								<div class="col-md-2">
									<input type="text" name="username" class="form-control" value="{{ $customer_contact->username}}">
									<span class="help-block">E-Mail</span>
								</div>
								<div class="col-md-2">
									<input type="text" name="contact_skype" class="form-control" value="{{ $customer_contact->contact_skype}}">
									<span class="help-block">Skype</span>
								</div>
								<div class="col-md-2">
									<input type="text" name="contact_mobile" class="form-control" value="{{ $customer_contact->contact_mobile}}">
									<span class="help-block">Mobile</span>
								</div>
								<div class="col-md-2">
									<input type="text" name="position" class="form-control" value="{{ $customer_contact->position}}">
									<span class="help-block">Job Title</span>
								</div>
								<div class="col-md-2">
                                    {{ Form::select('can_login', array('0' => 'No','1' => 'Yes'), $customer_contact->can_login, array("class"=>"form-control")) }}
                                    <span class="help-block">Can Login?</span>
								</div>
							</div>
						</div>
					</div>

					<div class="row">
<!--
						<div class="col-md-2">
							{{ Form::select('default_to', array('0' => 'No','1' => 'Yes'), $customer_contact->default_to, array("class"=>"form-control")) }}
							<span class="help-block">Default TO:</span>
						</div>
						<div class="col-md-2">
							{{ Form::select('default_cc', array('0' => 'No','1' => 'Yes'), $customer_contact->default_cc, array("class"=>"form-control")) }}
							<span class="help-block">Default CC:</span>
						</div>
-->
						<div class="col-md-2">
							{{ Form::select('system_emails', array('0' => 'No E-Mails','1' => 'Newsletter only','2' => 'Order Status only','3' => 'Newsletter+Order Status'), $customer_contact->system_emails, array("class"=>"form-control")) }}
							<span class="help-block">System E-Mails</span>
						</div>
						<div class="col-md-4">
						</div>
						<div class="col-md-2">
                            <input type="text" name="reset_password" class="form-control" value="" />
							<span class="help-block">Reset Password</span>
						</div>
					</div>
					<div class="form-actions">
						<input type="submit" value="SAVE" class="btn btn-success pull-right">
						<a href="/customer/getShow/{{$customer->id}}" class="btn btn-default pull-right">CANCEL</a>
					</div>
				</div>
			{!! Form::close() !!}
			</form>
		</div>
	</div>
</div>


@stop
