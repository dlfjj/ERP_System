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
				<h4><i class="icon-reorder"></i> Manage Product Attributes</h4>
				<div class="toolbar no-padding">
					<div class="btn-group">
					</div>
				</div>
			</div>
			<div class="widget-content">
				<table class="table table-hover">
					<thead>
						<tr>
							<th>Group</th>
							<th>Name</th>
							<th>Value</th>
							<th class="align-right"></th>
						</tr>
					</thead>
					<tbody>
                    {{ Form::open() }}
					@if($product->attributes->count() > 0)
                      
					@foreach($attributes as $attribute)
						<tr>
<td>
    {{ Form::text("group[$attribute->id]", $attribute->group, array("class"=>"form-control")) }}
</td>
<td>
    {{ Form::text("name[$attribute->id]", $attribute->name, array("class"=>"form-control")) }}
</td>
<td>
    {{ Form::text("value[$attribute->id]", $attribute->value, array("class"=>"form-control")) }}
</td>
							<td class="align-right">
								@if(has_role('products_edit'))
                                        <a href="/products/attribute-delete/{{ $attribute->id }}" class="btn btn-xs">Remove</a>
									</span>
								@endif
							</td>
						</tr>
					@endforeach
						<tr>
							<td></td>
							<td></td>
							<td></td>
<td>
{{ Form::hidden('action', "mass_update", array()) }}
    <input type="submit" value="UPDATE" class="btn btn-xs pull-right">
{{ Form::close() }}
</td>
						</tr>
                    @else
						<tr>
							<td colspan="4">Nothing found</td>
						</tr>
					@endif
                    {{ Form::open() }}
                    {{ Form::hidden('action', "attribute_add", array()) }}
                    <tr>
                        <td>
                            {{ Form::text('group', "", array("class"=>"form-control")) }}
                        </td>
                        <td>
                            {{ Form::text('name', "", array("class"=>"form-control")) }}
                        </td>
                        <td>
                            {{ Form::text('value', "", array("class"=>"form-control")) }}
                        </td>
                        <td>
                            <input type="submit" value="ADD/CHANGE" class="btn btn-xs pull-right">
                        </td>
                    </tr>
                    {{ Form::close() }}

					</tbody>
				</table>
			</div>
		</div>
		<!-- /Simple Table -->
	</div>
</div>

@stop
