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
				<h4><i class="icon-reorder"></i> Update Customer Address</h4>
			</div>
			<div class="widget-content">

				{!! Form::open(array('url' => "/customer/address-edit/$customer_address->id", 'enctype' => 'multipart/form-data', 'id' => 'main', 'method' => 'post', 'class' => 'orm-horizontal row-border form-validate', 'autocomplete' => 'off')) !!}

					<div class="form-group">
						<div class="col-md-12">
							<div class="row">
								<div class="col-md-12">
									<input type="hidden" name="customer_id" class="" value="{{ $customer->id}}">
									<input type="hidden" name="id" class="" value="{{ $customer_address->id}}">
								</div>
							</div>
							<div class="row">
								<div class="col-md-2">
									<input type="text" name="description" class="form-control" value="{{ $customer_address->description}}">
									<span class="help-block">Description</span>
								</div>
								<div class="col-md-2">
									<input type="text" name="address1" class="form-control" value="{{ $customer_address->address1}}">
									<span class="help-block">Street 1</span>
								</div>
								<div class="col-md-2">
									<input type="text" name="city" class="form-control" value="{{ $customer_address->city}}">
									<span class="help-block">City</span>
								</div>
								<div class="col-md-2">
									<input type="text" name="postal_code" class="form-control" value="{{ $customer_address->postal_code}}">
									<span class="help-block">Postal Code</span>
								</div>
								<div class="col-md-2">
									<input type="text" name="province" class="form-control" value="{{ $customer_address->province}}">
									<span class="help-block">Province</span>
								</div>
								<div class="col-md-2">
									<input type="text" name="country" class="form-control" value="{{ $customer_address->country}}">
									<span class="help-block">Country</span>
								</div>
							</div>
						</div>
					</div>

					<div class="row">
						<div class="col-md-2">
						</div>
						<div class="col-md-2">
							<input type="text" name="address2" class="form-control" value="{{ $customer_address->address2}}">
							<span class="help-block">Street 2</span>
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
