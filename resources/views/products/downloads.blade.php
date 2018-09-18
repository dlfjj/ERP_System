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
				<h4><i class="icon-reorder"></i> Downloads for Product "{{ $product->product_name }}"</h4>
				<div class="toolbar no-padding">
					<div class="btn-group">
					</div>
				</div>
			</div>
			<div class="widget-content">
				<table class="table table-hover">
					<thead>
						<tr>
							<th>Sort</th>
							<th>Added</th>
							<th>Filename</th>
							<th>Description</th>
							<th>Filesize</th>
							<th>Login req.?</th>
							<th class="align-right">-</th>
						</tr>
					</thead>
					<tbody>
					@if(count($product->downloads)>0)
					@foreach($product->downloads as $attachment)
						<tr>
							<td>{{$attachment->sort_no }}</td>
							<td>{{$attachment->updated_at }}</td>
							<td>{{substr($attachment->original_file_name,0,30)}}</td>
							<td>{{$attachment->description}}</td>
							<td>{{$attachment->file_size}}</td>
							<td>{{$attachment->login_required }}</td>
							<td class="align-right">
								@if(has_role('products_edit'))
								<span class="btn-group">
									{{ Form::open(array("url"=>"/products/download-delete/$attachment->id","method"=>"post","class"=>"form-inline","id"=>"v_$attachment->id")) }}
										<a href="javascript:void(0);" class="btn btn-xs form-submit-conf"><i class="icon-trash"></i></a>
									{{ Form::close() }}
								</span>
								@endif
								<a href="/products/download-download/{{ $attachment->id }}" class="btn btn-xs"><i class="icon-arrow-down"> Download</i></a>
							</td>
						</tr>
					@endforeach
					@else
						<tr>
							<td colspan="7">Nothing found</td>
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
                        <div class="col-md-12">
                            <p>Choose File</p>
                            <input type="file" name="file" data-style="fileinput">
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <div class="row">
                        <div class="col-md-12">
                            <p>Login required?</p>
                            {{ Form::select('login_required', $select_yesno , "No", array("class"=>"form-control")) }}
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <div class="row">
                        <div class="col-md-12">
                            <p>Description</p>
                            <input name="description" class="form-control input-width-xlarge" type="text" >
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
