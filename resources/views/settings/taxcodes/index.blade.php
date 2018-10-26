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
			<a href="taxcodes/" title="">Tax Codes</a>
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
			<form class="form-inline" id="create" action="/taxcodes/create" method="POST">
				<a class="btn btn-success form-submit-conf" href="javascript:void(0);" data-target-form="create"><i class="icon-plus-sign"></i> New Taxcode</a>
			</form>
		</div>
	</div>
@stop


@section('content')
				<div class="row">
					<div class="col-md-12">
						<div class="widget box">
							<div class="widget-header">
								<h4><i class="icon-reorder"></i> Tax Codes</h4>
								<div class="toolbar no-padding">
									<div class="btn-group">
										<span class="btn btn-xs widget-collapse"><i class="icon-angle-down"></i></span>
									</div>
								</div>
							</div>
							<div class="widget-content no-padding">
								<table class="table table-striped table-bordered table-hover">
									<thead>
										<tr>
											<th>Description</th>
											<th>Tax Percent</th>
											<th></th>
										</tr>
									</thead>
									<tbody>
										@foreach($taxcodes as $taxcode)
                                            {!! Form::open(['method'=>'DELETE', 'action'=> ['TaxcodeController@destroy', $taxcode->id], 'enctype'=>'multipart/form-data']) !!}
                                            <tr>
												<td>{{$taxcode->name}}</td>
												<td>{{$taxcode->percent}}</td>
												<td class="align-right">
													<span class="btn-group">
														<a href="taxcodes/{{$taxcode->id}}" class="btn"><i class="icon-edit"></i></a>
														{{--<a href="/taxcodes/delete/{{$taxcode->id}}" class="btn"><i class="icon-trash"></i></a>--}}
                                                        {{ Form::button('<i class="icon-trash"></i>', ['type' => 'submit', 'class' => 'btn'] )  }}
													</span>
												</td>
											</tr>
                                            {!! Form::close() !!}
										@endforeach
									</tbody>
								</table>
							</div>
						</div>
					</div>
				</div>
				<!-- /Normal -->
@stop
