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
        <li>
            <a href="/settings/product_categories">Product Categories</a>
        </li>
		<li class="current">
			<a href="/settings/product_categories/update/{{$category->id}}" title="">Product Categories Update</a>
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
		<!-- Page Stats -->
		<ul class="page-stats">
		</ul>
		<!-- /Page Stats -->
	</div>
@stop

@section('content')

	<div class="row">
		<div class="col-md-12">
			<div class="widget box">
				<div class="widget-header">
					<h4><i class="icon-reorder"></i> Update Category</h4>
				</div>
				<div class="widget-content">
                    {!! Form::open(['method'=>'PUT', 'action' => ['ProductCategoryController@update', $category->id], 'class'=>'form-horizontal row-border form-validate','id'=>'main','enctype'=>'multipart/form-data']) !!}
						<div class="form-group">
							<div class="col-md-4">
								<input type="hidden" name="action" class="" value="update_info" />
								<input type="text" name="name" class="form-control" value="{{ $category->name }}">
								<input type="hidden" name="category_id" class="" value="{{ $category->id}}">
								<span class="help-block">Category Name</span>
							</div>
							<div class="col-md-2">
								<input type="text" name="sort_by" class="form-control" value="{{ $category->sort_by}}">
								<span class="help-block">Sort Code</span>
							</div>
							<div class="col-md-2">
								{{ Form::select('visible', array("1" => "Yes", "0" => "No"), $category->visible, array("class"=>"form-control")) }}
								<span class="help-block">Is Visible</span>
							</div>
							<div class="col-md-4">
								{{ Form::textarea('description', $category->description, array("class"=>"form-control froala-editor")) }}
								<span class="help-block">Description</span>
							</div>

						</div>
						<div class="form-group">
							<div class="col-md-4">
								<input type="text" name="name_localized" class="form-control" value="{{ $category->name_localized }}">
								<span class="help-block">Category Name localized</span>
							</div>
							<div class="col-md-2">
							</div>
							<div class="col-md-2">
							</div>
							<div class="col-md-4">
								{{ Form::textarea('description_localized', $category->description_localized, array("class"=>"form-control froala-editor")) }}
								<span class="help-block">Description localized</span>
							</div>

						</div>
						<div class="form-group">
							<div class="col-md-12">
								<div class="row">
									<div class="col-md-4">
										<input type="file" name="banner" data-style="fileinput">
										<span class="help-block">Banner, PNG or JPG, 500KB MAX</span>
									</div>
									@if($category->banner != "")
										<div class="col-md-8 align-right">
											<img style="max-height: 125px;" src="/public/categories/{{$category->banner}}" /><br />
											Remove {{ Form::checkbox('delete_banner', '1', false) }}
										</div>
									@endif
								</div>
							</div>
						</div>

						<div class="form-group">
							<div class="col-md-12">
								<div class="row">
									<div class="col-md-4">
										<input type="file" name="picture" data-style="fileinput">
										<span class="help-block">Group Picture, PNG or JPG, 500KB MAX</span>
									</div>
									@if($category->picture != "")
										<div class="col-md-8 align-right">
											<img style="max-height: 125px;" src="/public/categories/{{$category->picture}}" /><br />
											Remove {{ Form::checkbox('delete_picture', '1', false) }}
										</div>
									@endif
								</div>
							</div>
						</div>

						<div class="form-actions">
							<input type="submit" value="SAVE" class="btn btn-success pull-right">
							<a href="/settings/product_categories" class="btn btn-default pull-right">Cancel</a>
						</div>
                    {{ Form::close() }}
				</div>
			</div>
		</div>
	</div>

	<div class="row">
		<div class="col-md-12">
			<div class="widget box">
				<div class="widget-header">
					<h4><i class="icon-reorder"></i> Category Images</h4>
					<div class="toolbar no-padding">
						<div class="btn-group">
						</div>
					</div>
				</div>
				<div class="widget-content">
                    {!! Form::open(['method'=>'PATCH', 'action'=> ['ProductCategoryController@updateAddThumbnail', $category->id],'enctype'=>'multipart/form-data']) !!}
                    {{--<form enctype="multipart/form-data" action="/product_categories/add-thumbnail/{{ $category->id }}" method="POST">--}}
						<table class="table table-hover">
							<thead>
							<tr>
								<th>Thumbnail</th>
								<th>Date Added</th>
								<th class="align-right"></th>
							</tr>
							</thead>
							<tbody>
							@if($category->images->count() > 0)
                                @php
                                	$images = App\Models\CategoryImage::where('category_id',$category->id)->get();
                                @endphp
								@foreach($images as $image)
									<tr>
										<td>
											<img style="max-width: 125px;" src="/categories/{{$image->picture}}" /><br />
										</td>
										<td>
											{{ $image->date_added }}
										</td>
										<td class="align-right">
											<a class="btn deleteThumbnail" data-id="{{ $image->id }}">Remove</a>
										</td>
									</tr>
								@endforeach
							@else
								<tr>
									<td colspan="3">Nothing found</td>
								</tr>
							@endif
							{{ Form::hidden('action', "images", array()) }}
							<tr>
								<td>
									<input type="file" name="picture" data-style="fileinput">
								</td>
								<td>
								</td>
								<td>
									<input type="submit" value="ADD" class="btn pull-right">
								</td>
							</tr>
							</tbody>
						</table>
					{{ Form::close() }}
				</div>
			</div>
			<!-- /Simple Table -->
		</div>
	</div>


	<div class="row">
		<div class="col-md-12">
			<div class="widget box">
				<div class="widget-header">
					<h4><i class="icon-reorder"></i> Category Downloads</h4>
					<div class="toolbar no-padding">
						<div class="btn-group">
						</div>
					</div>
				</div>
				<div class="widget-content">
					{!! Form::open(['method'=>'PATCH', 'action' => ['ProductCategoryController@updateDownloadableFile', $category->id], 'class'=>'form-horizontal row-border form-validate','enctype'=>'multipart/form-data']) !!}
					{{--<form enctype="multipart/form-data" class="form-horizontal row-border form-validate" action="/product_categories/update-downloads/{{$category->id}}" method="POST">--}}
						<table class="table table-hover">
							<thead>
							<tr>
								<th>Sort No</th>
								<th>Date Added</th>
								<th>File Name</th>
								<th>File Description</th>
								<th>File Size</th>
								<th>Login required</th>
								<th class="align-right"></th>
							</tr>
							</thead>
							<tbody>
							@if($category->downloads->count() > 0)
                                @php
                                    $downloads = App\Models\CategoryDownload::where('category_id',$category->id)->orderBy('sort_no','ASC')->get();
                                @endphp
								@foreach($downloads as $download)
									<tr>
										<td>
											{{ Form::input('number',"downloads[$download->id][sort_no]", $download->sort_no, array("class"=>"form-control")) }}
										</td>
										<td>
											{{ $download->date_added }}
										</td>
										<td>
											{{ $download->original_file_name }}
										</td>
										<td>
											{{ Form::text("downloads[$download->id][description]", $download->description, array("class"=>"form-control")) }}
										</td>
										<td>
											{{ $download->file_size }}
										</td>
										<td>
											{{ Form::select("downloads[$download->id][login_required]", array("Yes" => "Yes", "No" => "No"), $download->login_required, array("class"=>"form-control")) }}
										</td>
										<td class="align-right">
											<a href="/product_categories/download-delete/{{ $download->id }}" class="btn">Remove</a>
										</td>
									</tr>
								@endforeach
							@else
								<tr>
									<td colspan="7">Nothing found</td>
								</tr>
							@endif
							{{ Form::hidden('action', "downloads", array()) }}
							<tr>
								<td>
									{{ Form::input('number', 'downloads[0][sort_no]', "", array("class"=>"form-control","step"=>"1")) }}
								</td>
								<td colspan="2">
									<input type="file" name="file" data-style="fileinput" />
								</td>
								<td>
									{{ Form::text('downloads[0][description]', "", array("class"=>"form-control")) }}
								</td>
								<td>
								</td>
								<td>
									{{ Form::select('downloads[0][login_required]', array("Yes" => "Yes", "No" => "No"), "No", array("class"=>"form-control")) }}
								</td>
								<td>
									<input type="submit" value="ADD/CHANGE" class="btn pull-right">
								</td>
							</tr>
							</tbody>
						</table>
					{{ Form::close() }}
				</div>
			</div>
			<!-- /Simple Table -->
		</div>
	</div>

	<div class="row">
		<div class="col-md-12">
			<div class="widget box">
				<div class="widget-header">
					<h4><i class="icon-reorder"></i> Category Attributes</h4>
					<div class="toolbar no-padding">
						<div class="btn-group">
						</div>
					</div>
				</div>
				<div class="widget-content">
                    {!! Form::open(['method'=>'PATCH', 'action'=> ['ProductCategoryController@updateAttributes', $category->id], 'class'=>'form-horizontal row-border form-validate','enctype'=>'multipart/form-data']) !!}
					{{--<form enctype="multipart/form-data" class="form-horizontal row-border form-validate" action="/product_categories/update-attributes/{{$category->id}}" method="POST">--}}
						<table class="table table-hover">
							<thead>
							<tr>
								<th>Sort No</th>
								<th>Group</th>
								<th>Name</th>
								<th>Value</th>
								<th class="align-right"></th>
							</tr>
							</thead>
							<tbody>
							@if($category->attributes->count() > 0)
                                @php
                                $attributes = App\Models\CategoryAttribute::where('category_id',$category->id)->orderBy('sort_no','ASC')->orderBy('group','ASC')->get();
                                @endphp
								@foreach($attributes as $attribute)
									<tr>
										<td>
											{{ Form::text("attributes[$attribute->id][sort_no]", $attribute->sort_no, array("class"=>"form-control")) }}
										</td>
										<td>
											{{ Form::text("attributes[$attribute->id][group]", $attribute->group, array("class"=>"form-control")) }}
										</td>
										<td>
											{{ Form::text("attributes[$attribute->id][name]", $attribute->name, array("class"=>"form-control")) }}
										</td>
										<td>
											{{ Form::text("attributes[$attribute->id][value]", $attribute->value, array("class"=>"form-control")) }}
										</td>
										<td class="align-right">
											<a href="/product_categories/attribute-delete/{{ $attribute->id }}" class="btn btn-xs">Remove</a>
										</td>
									</tr>
								@endforeach
							@else
								<tr>
									<td colspan="5">Nothing found</td>
								</tr>
							@endif
							{{ Form::hidden('action', "attributes", array()) }}
							<tr>
								<td>
									{{ Form::text("attributes[0][sort_no]", "", array("class"=>"form-control")) }}
								</td>
								<td>
									{{ Form::text("attributes[0][group]", "", array("class"=>"form-control")) }}
								</td>
								<td>
									{{ Form::text("attributes[0][name]", "", array("class"=>"form-control")) }}
								</td>
								<td>
									{{ Form::text("attributes[0][value]", "", array("class"=>"form-control")) }}
								</td>
								<td>
									<input type="submit" value="ADD/CHANGE" class="btn pull-right">
								</td>
							</tr>
							</tbody>
						</table>
                    {{ Form::close() }}
                </div>
			</div>
			<!-- /Simple Table -->
		</div>
	</div>
@stop


@push('scripts')
	<script>
        $(".deleteThumbnail").click(function(){
            var id = $(this).data("id");
            console.log(id);
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax(
                {
                    url:  "{!! url('settings/product_categories/update/category_image_delete' ) !!}" + "/" + id,
                    type: 'POST',
                    dataType: "JSON",
                    data: {
                        "id": id,
                        "_method": 'DELETE',
                    },
                    success: function ()
                    {

                    }
                });
        });

        $(document).ajaxStop(function(){
            window.location.reload();
        });
	</script>
@endpush