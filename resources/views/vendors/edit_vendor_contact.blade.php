@extends('layouts.default')

@section('page-module-menu')
    @include('vendors.top_menu') 
@stop


@section('page-crumbs')
    @include('vendors.bread_crumbs') 
@stop


@section('page-header')
    @include('vendors.page_header') 
@stop


@section('content')

<div class="row">
	<div class="col-md-12">
		<div class="widget box">
			<div class="widget-header">
				<h4><i class="icon-reorder"></i> Update Vendor Contact</h4>
			</div>
			<div class="widget-content">
				<form enctype="multipart/form-data" id="main" class="form-horizontal row-border form-validate" action="" method="POST">
					{{csrf_field()}}
					<div class="form-group">
						<div class="col-md-12">
							<div class="row">
								<div class="col-md-12">
									<input type="hidden" name="vendor_id" class="" value="{{ $vendor->id}}">
									<input type="hidden" name="id" class="" value="{{ $vendor_contact->id}}">
								</div>
							</div>
							<div class="row">
								<div class="col-md-2">
									<input type="text" name="name" class="form-control" value="{{ $vendor_contact->name}}">
									<span class="help-block">Contact Name</span>
								</div>
								<div class="col-md-2">
									<input type="text" name="email" class="form-control" value="{{ $vendor_contact->email}}">
									<span class="help-block">E-Mail</span>
								</div>
								<div class="col-md-2">
									<input type="text" name="skype" class="form-control" value="{{ $vendor_contact->skype}}">
									<span class="help-block">Skype</span>
								</div>
								<div class="col-md-2">
									<input type="text" name="mobile" class="form-control" value="{{ $vendor_contact->mobile}}">
									<span class="help-block">Mobile</span>
								</div>
								<div class="col-md-2">
									<input type="text" name="position" class="form-control" value="{{ $vendor_contact->position}}">
									<span class="help-block">Job Title</span>
								</div>
							</div>
						</div>
					</div>

					<div class="row">
						<div class="col-md-2">
							{{ Form::select('default_to', array('0' => 'No','1' => 'Yes'), $vendor_contact->default_to, array("class"=>"form-control")) }}
							<span class="help-block">Default TO:</span>
						</div>
						<div class="col-md-2">
							{{ Form::select('default_cc', array('0' => 'No','1' => 'Yes'), $vendor_contact->default_cc, array("class"=>"form-control")) }}
							<span class="help-block">Default CC:</span>
						</div>
					</div>
					<div class="form-actions">
						<input type="submit" value="Save" class="btn btn-success pull-right">
						<a href="/vendors/show/{{$vendor->id}}" class="btn btn-default pull-right">Cancel</a>
					</div>
				</div>
			</form>
		</div>
	</div>
</div>


@stop
