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
			<a href="/settings/product_categories/" title="">Product Categories</a>
		</li>
	</ul>

	<ul class="crumb-buttons">
		<li>
			<a href="javascript:void(0);" title=""><i class="icon-calendar"></i><span><?=date('F d, Y \(\K\W:W) H:i:s');?></span></a>
		</li>
	</ul>
@stop

{{--@section('page-header')--}}
{{--<div class="page-header">--}}
{{--<div class="page-title">--}}
{{--@php--}}
{{--$path = "<a href='/product_categories/'>Top</a>" . " &raquo; ";--}}
{{--if($ancestors){--}}
{{--foreach($ancestors as $ancestor){--}}
{{--$path .= "<a href='/product_categories/show/$ancestor->id'>$ancestor->name</a>" . " &raquo; ";--}}
{{--}--}}
{{--}--}}
{{--$path = substr_replace($path,"",-8);--}}
{{--@endphp--}}
{{--{{ $path }}--}}
{{--</div>--}}
{{--</div>--}}
{{--@stop--}}

@section('content')
	<div class="row">
		<div class="col-md-12">
			<div class="widget box">
				<div class="widget-header">
					@if($category)
						<h4><i class="icon-reorder"></i> Add subcategory to <em>{{$category->name}}</em></h4>
					@else
						<h4><i class="icon-reorder"></i> Add new top-level category</h4>
					@endif
					{{--<div class="toolbar no-padding">--}}
						{{--<div class="btn-group">--}}
							{{--<span class="btn btn-xs widget-collapse"><i class="icon-angle-down"></i></span>--}}
						{{--</div>--}}
					{{--</div>--}}
				</div>
				<div class="widget-content">
					{!! Form::open(['method'=>'GET','action'=>['ProductCategoryController@create']], array('enctype'=>'multipart/form-data','id'=>'main','class' => 'form-vertical row-border form-validate')) !!}
					{{--<form enctype="multipart/form-data" id="main" class="form-vertical row-border form-validate" action="/product_categories/create" method="POST">--}}
						<div class="form-group">
							<div class="row">
								<div class="col-md-3">
									<label class="control-label">Category Name</label>
									{{ Form::text('name', "", array("class"=>"form-control")) }}
									{{ Form::hidden('parent_id', $category_id, array("class"=>"form-control")) }}
								</div>
							</div>
							<div class="form-actions">
								<input type="submit" value="Add Category" class="btn btn-success pull-right">
							</div>
						</div>
					{{--</form>--}}
					{{ Form::close() }}
				</div>
			</div>
		</div>
	</div>

	<div class="row">
		<div class="col-md-12">
			<div class="widget box">
				<div class="widget-header">
					<h4><i class="icon-reorder"></i> Categories</h4>
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
							<th>Sort Code</th>
							<th>Category Name</th>
							<th>Banner</th>
							<th style="width: 125px;" class="no-break"></td>
						</tr>
						</thead>
						<tbody>
						@if($categories->count()>0)
							@foreach($categories as $category)
                                {!! Form::open(['method'=>'DELETE', 'action'=> ['ProductCategoryController@destroy', $category->id],'enctype'=>'multipart/form-data']) !!}


                                <tr>
									<td>{{$category->sort_by}}</td>
									<td>{{$category->name}}</td>
									<td>{{$category->banner }}</td>
									<td class="no-break">
										<ul class="table-controls">
											<li><a href="/settings/product_categories/{{ $category->id }}" class="bs-tooltip" title="View"><i class="icon-search"></i></a></li>
											<li><a href="/settings/product_categories/update/{{ $category->id }}" class="bs-tooltip" title="Update"><i class="icon-edit"></i></a></li>
											{{--<li><input type="submit" class="bs-tooltip" title="Delete"><i class="icon-remove"></i></input></li>--}}
                                            {{ Form::button('<i class="icon-remove"></i>', ['type' => 'submit', 'class' => 'btn btn-sm'] )  }}
                                        </ul>
									</td>
								</tr>
                                {!! Form::close() !!}
							@endforeach
						@else
							<tr>
								<td colspan="4">Nothing found</td>
							</tr>
						@endif
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
	<!-- /Normal -->
@stop
