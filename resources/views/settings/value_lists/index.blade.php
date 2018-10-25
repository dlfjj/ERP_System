@extends('layouts.default')

@section('page-crumbs')
	<ul id="breadcrumbs" class="breadcrumb">
		<li>
			<i class="icon-home"></i>
			<a href="/">Dashboard</a>
		</li>
        <li>
            <a href="/settings">Setting</a>
        </li>
		<li class="current">
			<a href="value_lists/" title="">Value Lists</a>
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
	</div>
@stop


@section('content')
	<div class="row">
		<div class="col-md-12">
			<div class="widget box">
				<div class="widget-header">
					<h4><i class="icon-reorder"></i> Value Lists</h4>
				</div>
				<div class="widget-content no-padding">
					<table class="table table-striped table-bordered table-hover">
						<thead>
						<tr>
							<th>Module</th>
							<th>UID</th>
							<th></th>
						</tr>
						</thead>
						<tbody>
						@foreach($value_lists as $value_list)
							<tr>
								<td>{{$value_list->module}}</td>
								<td>{{$value_list->uid}}</td>
								<td class="align-right">
									<span class="btn-group">
										<a href="/settings/value_lists/{{$value_list->id}}" class="btn"><i class="icon-edit"></i></a>
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
