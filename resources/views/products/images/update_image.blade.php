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
				<h4><i class="icon-reorder"></i> Set Product Prices</h4>
			</div>
			<div class="widget-content">
                {!! Form::open(['method'=>'PATCH', 'action'=> ['ImageController@update', $attachment->id], 'class'=>'form-vertical row-border form-validate','id'=>'main','enctype'=>'multipart/form-data']) !!}

					<div class="form-group">
						<div class="row">
							<div class="col-md-4">
								<label class="control-label">SEO Keywords</label>
								{{ Form::text('seo_keyword', $attachment->seo_keyword, array("class"=>"form-control")) }}
								<span class="help-block">Keep brief. Separate multiple words using comma</span>
                            </div>
							<div class="col-md-4">
								<label class="control-label">Upload / Replace</label>
                                {!! Form::file('image_id', ['class'=>'form-controll']) !!}
                                <span class="help-block">Max size 2M</span>
                            </div>
							<div class="col-md-4 text-right">
								<img src="{{ URL::asset( return_company_id().'/products/'.$product->id.'/'.$attachment->picture) }}" style="max-width: 100px;" /><br />
							</div>
						</div>
                    </div>
					<div class="form-group">
						@if(has_role('products_edit'))
							<div class="form-actions">
								<input type="submit" value="Update" class="btn btn-success pull-right">
							</div>
						@endif
					</div>
				</div>
            {{ Form::close()  }}
		</div>
	</div>
</div>

@stop
