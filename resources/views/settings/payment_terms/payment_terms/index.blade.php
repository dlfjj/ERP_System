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
			<form class="form-inline" id="create" action="/payment_terms/create" method="POST">
				<a class="btn btn-success btn-lg form-submit-conf" href="javascript:void(0);" data-target-form="create"><i class="icon-plus-sign"></i> New Payment Term</a>
			</form>
		</div>
	</div>
@stop


@section('content')
				<div class="row">
					<div class="col-md-12">
						<div class="widget box">
							<div class="widget-header">
								<h4><i class="icon-reorder"></i> Payment Terms</h4>
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
											<th>Credit (d)</th>
											<th>Sort order</th>
											<th></th>
										</tr>
									</thead>
									<tbody>
										@foreach($payment_terms as $payment_term)
											<tr>
												<td>{{$payment_term->name}}</td>
												<td>{{$payment_term->credit }}</td>
												<td>{{$payment_term->sort_no }}</td>
												<td class="align-right">
													<span class="btn-group">
														<a href="/payment_terms/show/{{$payment_term->id}}" class="btn btn-xs"><i class="icon-edit"></i></a>
														<a href="/payment_terms/delete/{{$payment_term->id}}" class="btn btn-xs"><i class="icon-trash"></i></a>
													</span>
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
