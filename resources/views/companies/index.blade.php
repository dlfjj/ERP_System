@extends('layouts.default')

@section('page-crumbs')
	<ul id="breadcrumbs" class="breadcrumb">
		<li>
			<i class="icon-home"></i>
			<a href="/">Dashboard</a>
		</li>
		<li class="current">
			<a href="/companies/" title="">Companies</a>
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
			@if(has_role('admin'))
			{{--<form class="form-inline" id="create" action="/companies/create" method="POST">--}}
                {!! Form::open(['method'=>'GET','action'=>'CompanyController@create'], array('enctype'=>'multipart/form-data','class' => 'form-inline')) !!}
                    <input type="submit" value="ADD NEW COMPANY" class="btn btn-success form-submit-conf">
                {{--<a class="btn btn-success form-submit-conf" href="javascript:void(0);" data-target-form="create"><i class="icon-plus-sign"></i> New Company</a>--}}
                {{ Form::close() }}
			{{--</form>--}}
			@endif
		</div>
	</div>
@stop

@section('content')
				<div class="row">
					<div class="col-md-12">
						<div class="panel panel-default">
							<div class="panel-heading"><i class="icon-reorder"></i> Company Index</div>
							<div class="panel-body">
								<table class="table table-striped table-bordered table-hover datatable">
									<thead>
										<tr>
											<th>Status</th>
											<th>Company Name</th>
											<th>Contact Person</th>
											<th>Contact Phone</th>
											<th>Contact E-Mail</th>
											<th>Country</th>
											<th>Login</th>
											<th></th>
										</tr>
									</thead>
									<tbody>
										@foreach($companies as $company)
											<tr>
												<td>{{ $company->status }}</td>
												<td>{{ $company->name}}</td>
												<td>{{ $company->contact_person }}</td>
												<td>{{ $company->contact_email }}</td>
												<td>{{ $company->contact_phone }}</td>
												<td>{{ $company->country }}</td>
												<td>{{ ($company->can_login == 1) ? "Yes" : "No" }}</td>
												<td class="align-center">
													<ul class="table-controls">
														<li><a href="/companies/{{ $company->id }}" class="bs-tooltip" title="View"><i class="icon-search"></i></a> </li>
													</ul>
												</td>
											</tr>
										@endforeach
									</tbody>
								</table>
							</div>
						</div>
					</div>
				</div>
				<!-- /Normal -->
@stop
