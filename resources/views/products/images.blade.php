@extends('layouts.default')

@section('page-module-menu')
    @include('products.top_menu')
@stop

@section('page-crumbs')
    @include('products.bread_crumbs')
@stop

@section('page-header')
    @include('products.page_header')
@stop

@section('content')
<div class="row">
	<div class="col-md-12">
		<div class="widget box">
			<div class="widget-header">
				<h4><i class="icon-reorder"></i> Images for Product "{{ $product->product_name }}"</h4>
				<div class="toolbar no-padding">
					<div class="btn-group">
					</div>
				</div>
			</div>
			<div class="widget-content">
				<table class="table table-hover">
					<thead>
						<tr>
							<th>Added</th>
							<th>Filename</th>
							<th>Filesize</th>
							<th>SEO Keyword(s)</th>
							<th class="align-right">-</th>
						</tr>
					</thead>
					<tbody>
					@if(count($product->images )>0)
					@foreach($product->images as $attachment)
						<tr>
							<td>
<img src="/products/view-image/{{ $product->id }}/{{ $attachment->id }}" style="max-width: 100px;" /><br />
{{$attachment->updated_at }}
</td>
							<td>{{$attachment->picture }}</td>
							<td>{{$attachment->file_size}}</td>
							<td>{{$attachment->seo_keyword}}</td>
							<td class="align-right">
								@if(has_role('products_edit'))
								<span class="btn-group">
									{{ Form::open(array("url"=>"/products/image-delete/$attachment->id","method"=>"post","class"=>"form-inline","id"=>"v_$attachment->id")) }}
										<a href="javascript:void(0);" class="btn btn-xs form-submit-conf"><i class="icon-trash"></i></a>
									{{ Form::close() }}
										<a href="/products/update-image/{{ $attachment->id }}" class="btn btn-xs"><i class="icon-edit"></i></a>
								</span>
                                    @if($product->picture == $attachment->picture)
                                        <a href="/products/unmark-as-main-image/{{ $attachment->id }}" class="btn btn-xs">Unset main image</a>
                                    @else
                                        <a href="/products/mark-as-main-image/{{ $attachment->id }}" class="btn btn-xs">Set main image</a>
                                    @endif
								@endif
								<a href="/products/image-download/{{ $attachment->id }}" class="btn btn-xs"><i class="icon-arrow-down"> Download</i></a>
							</td>
						</tr>
					@endforeach
					@else
						<tr>
							<td colspan="6">Nothing found</td>
						</tr>
					@endif
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>

@if(has_role('products_edit'))
<div class="row">
    <div class="col-md-12">
       <a class="btn btn-success btn-sm" data-toggle="modal" href="#modal_add_file" class="">Add File</a>
    </div>
</div>
@endif

<div class="modal fade" id="modal_add_file">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title">Add new File</h4>
			</div>
			<form autocomplete="off" enctype="multipart/form-data" id="customer_contact" class="form-validate1" action="" method="POST">
			<div class="modal-body">
                <div class="form-group">
                    <div class="row">
                        <div class="col-md-6">
							<input type="file" name="file" data-style="fileinput">
							<span class="help-block">File</span>
						</div>
                        <div class="col-md-6">
							{{ Form::text('seo_keyword',"", array("class"=>"form-control")) }}
							<span class="help-block">Keyword(s)</span>
                        </div>
                    </div>
                </div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
				<input type="submit" class="btn btn-primary" value="Submit">
			</div>
			</form>
        </div>
    </div>
</div>



@stop
