@extends('layouts.default')

@section('page-crumbs')
	<ul id="breadcrumbs" class="breadcrumb">
		<li>
			<i class="icon-home"></i>
			<a href="/">Dashboard</a>
		</li>
		<li class="current">
			<a href="/companies/{{$company->id}}" title="">Company Details</a>
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
			<h3>{{ $company->name }}</h3>
		</div>

		<ul class="page-stats">
			<li>
			</li>
		</ul>
	</div>
@stop



@section('content')

<!--    --><?php
//
//    $user = User::find(1);
//
//    ?>

	<div class="row">
		<div class="col-md-12">
			<div class="widget box">
				<div class="widget-header">
					<h4><i class="icon-reorder"></i> Company Information</h4>
				</div>
				<div class="widget-content">
					{!! Form::open(['method'=>'PUT', 'action'=> ['CompanyController@update', $company->id], 'class'=>'form-vertical row-border form-validate','id'=>'main','enctype'=>'multipart/form-data']) !!}
					<form autocomplete="off" enctype="multipart/form-data" id="main" class="form-vertical row-border form-validate" action="" method="POST">
						<div class="form-group">
							<div class="row">
								<div class="col-md-3">
									<label class="control-label">Company Name</label>
									{{ Form::text('name', $company->name, array("class"=>"form-control")) }}
								</div>
								<div class="col-md-2">
									<label class="control-label">Company Contact</label>
									{{ Form::text('contact_person', $company->contact_person, array("class"=>"form-control")) }}
								</div>
								<div class="col-md-2">
									<label class="control-label">Company Phone</label>
									{{ Form::text('contact_phone', $company->contact_phone, array("class"=>"form-control")) }}
								</div>
								<div class="col-md-3">
									<label class="control-label">Company E-Mail</label>
									{{ Form::text('contact_email', $company->contact_email, array("class"=>"form-control")) }}
								</div>
								<div class="col-md-2">
									<label class="control-label">Base Currency</label>
									{{ Form::select('currency_code', selectbox_array('currency_codes'), $company->currency_code, array("class"=>"form-control")) }}
								</div>
							</div>
						</div>
						<div class="form-group">
							<div class="row">
								<div class="col-md-3">
									<label class="control-label">Local Company Name</label>
									{{ Form::text('name_local', $company->name_local, array("class"=>"form-control")) }}
								</div>
								<div class="col-md-2">
									<label class="control-label">Company Code</label>
									{{ Form::text('letter', $company->letter, array("class"=>"form-control")) }}
								</div>
								<div class="col-md-3">
									<label class="control-label">Allow Company Login?</label><br />
									<div class="make-switch" data-on-label="Yes" data-off-label="No">
										<input type="checkbox" name="can_login" class="toggle" {{ $company->can_login == 1 ? 'checked' : '' }} />
									</div>
								</div>
								<div class="col-md-2">
									<label class="control-label">CI Title</label>
									{{ Form::text('ci_title', $company->ci_title, array("class"=>"form-control")) }}
								</div>
								<div class="col-md-2">
									<label class="control-label">Customer ID</label>
									{{ Form::text('customer_id', $company->customer_id, array("class"=>"form-control")) }}
								</div>
							</div>
						</div>
						<div class="form-group">
							<div class="row">
								<div class="col-md-12">
									<h4>Addresses</h4>
								</div>
							</div>
						</div>
						<div class="form-group">
							<div class="row">
								<div class="col-md-4">
									<textarea rows="3" cols="5" name="bill_to" class="form-control">{{ $company->bill_to }}</textarea>
									<span class="help-block">Bill To</span>
								</div>

								<div class="col-md-4">
									<textarea rows="3" cols="5" name="ship_to" class="form-control">{{ $company->ship_to }}</textarea>
									<span class="help-block">Ship To</span>
								</div>

								<div class="col-md-4">
									<textarea rows="3" cols="5" name="bank_info" class="form-control">{{ $company->bank_info }}</textarea>
									<span class="help-block">Bank Info</span>
								</div>
							</div>
						</div>
						<div class="form-group">
							<div class="row">
								<div class="col-md-12">
									<h4>Upload / change company logo</h4>
								</div>
							</div>
						</div>
						<div class="form-group">
							<div class="row">
								<div class="col-md-4">
									<input type="file" name="company_logo" data-style="fileinput">
									<span class="help-block">Upload 640x200px max</span>
								</div>
								<div class="col-md-8">
									@if($company->company_logo != "")
										<img class="pull-right" style="max-height: 100px;" src="/public/global/companies/{{ $company->company_logo }}" />
									@endif
								</div>
							</div>
						</div>

						<div class="form-group">
							<div class="row">
								<div class="col-md-12">
									<h4>Document Footers</h4>
								</div>
							</div>
						</div>

						<div class="form-group">
							<div class="row">
								<div class="col-md-6">
									<textarea rows="3" cols="5" name="df_quote" class="form-control">{{ $company->df_quote }}</textarea>
									<span class="help-block">Quotations</span>
								</div>
								<div class="col-md-6">
									<textarea rows="3" cols="5" name="df_con" class="form-control">{{ $company->df_con }}</textarea>
									<span class="help-block">Order Confirmation</span>
								</div>
							</div>
						</div>

						<div class="form-group">
							<div class="row">
								<div class="col-md-6">
									<textarea rows="3" cols="5" name="df_sc" class="form-control">{{ $company->df_sc }}</textarea>
									<span class="help-block">Sales Contract</span>
								</div>
								<div class="col-md-6">
									<textarea rows="3" cols="5" name="df_pi" class="form-control">{{ $company->df_pi }}</textarea>
									<span class="help-block">Proforma Invoice</span>
								</div>
							</div>
						</div>

						<div class="form-group">
							<div class="row">
								<div class="col-md-6">
									<textarea rows="3" cols="5" name="df_ci" class="form-control">{{ $company->df_ci }}</textarea>
									<span class="help-block">Commercial Invoice</span>
								</div>
								<div class="col-md-6">
									<textarea rows="3" cols="5" name="df_pod" class="form-control">{{ $company->df_pod }}</textarea>
									<span class="help-block">Production order</span>
								</div>
							</div>
						</div>

						<div class="form-group">
							<div class="row">
								<div class="col-md-6">
									<textarea rows="3" cols="5" name="df_pl" class="form-control">{{ $company->df_pl }}</textarea>
									<span class="help-block">Packing List</span>
								</div>
							</div>
						</div>


						<div class="form-group">
							<div class="form-actions">
								@if(has_role('admin'))
									<input type="submit" value="Save" class="btn btn-sm btn-success pull-right">
									<a class="btn btn-danger btn-sm pull-right conf" href="/companies/destroy/{{$company->id}}"><i class="icon-trash"></i> Delete</a>
								@endif
								<a href="/companies" class="btn btn-sm btn-default pull-right">Cancel</a>
							</div>
						</div>
					</form>
				</div>
				{{ Form::close() }}
				{{--</form>--}}
			</div>
		</div>
	</div>

	<div class="row">
		<div class="col-md-12">
			<div class="widget box">
				<div class="widget-header">
					<h4><i class="icon-reorder"></i> Users Index</h4>
					<div class="toolbar no-padding">
						<div class="btn-group">
							<span class="btn btn-xs widget-collapse"><i class="icon-angle-down"></i></span>
						</div>
					</div>
				</div>
				<div class="widget-content no-padding">
					<table class="table table-striped table-bordered table-hover table-chooser">
						<thead>
						<tr>
							<th>User ID</th>
							<th>First Name</th>
							<th>Last Name</th>
							<th>Username</th>
							<th>Last Login</th>
							<th></th>
						</tr>
						</thead>
						<tbody>
						@foreach($company->users as $user)
							<tr>
								<td>{{ $user->id }}</td>
								<td>{{ $user->first_name  }}</td>
								<td>{{ $user->last_name  }}</td>
								<td>{{ $user->username }}</td>
								<td>{{ $user->last_login }}</td>
								<td>
									<a href="/usersList/{{ $user->id }}">Show</a>
								</td>
							</tr>
						@endforeach
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>


@stop
