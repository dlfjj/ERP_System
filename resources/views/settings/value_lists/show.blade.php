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
		<li>
			<a href="/settings/value_lists/" title="">Value Lists</a>
		</li>
		<li class="current">
			<a href="/settings/value_lists/{{$value_list->id}}" title="">{{ucfirst($value_list->uid)}}</a>
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
			<a class="btn btn-default" href="/settings/value_lists"><i class="icon-check"></i> Done</a>
		</div>
	</div>
@stop

@section('content')

{{--<script type="text/javascript">--}}
	{{--$(document).ready(function() {--}}
		{{--$("body").on("click", "a.delete_item", function(e){--}}
			{{--$(this).closest("tr").remove();--}}
		{{--});--}}

		{{--$("body").on("click", "a.add_item", function(e){--}}
			{{--$("table tr:last").clone().appendTo("table");--}}
		{{--});--}}
	{{--});--}}
{{--</script>--}}

<div class="row">
	<div class="col-md-12">
		<div class="widget box">
			<div class="widget-header">
				<h4><i class="icon-reorder"></i> List Entries for Valuelist <em>{{ucfirst($value_list->module)}} -> {{ucfirst($value_list->uid)}}</em></h4>
			</div>
			<div class="widget-content">
                {!! Form::open(['method'=>'PUT', 'action'=> ['ValueListController@update', $value_list->id], 'class'=>'form-vertical row-border','id'=>'main']) !!}
				{{--<form enctype="multipart/form-data" id="main" class="form-vertical row-border" action="/value_lists/update/{{$value_list->id}}" method="POST">--}}
					<div class="form-group">
						<div class="row">
							<div class="col-md-12">
								<table class="table table-striped table-hover valuelisttable">
									<tbody>
										@foreach($list_entries as $entry)
											<tr>
												<td>
													<input type="text" name="name[]" class="form-control" value="{{$entry->name}}">
												</td>
												<td class="align-right">
													<span class="btn-group">
														<a class="delete_item btn btn-lg"><i class="icon-trash"></i></a>
													</span>
												</td>
											</tr>
										@endforeach
									</tbody>
								</table>
							</div>
						</div>
						<div class="form-actions">
                            <a href="javascript:void(0);" class="btn btn-sm btn-info pull-right add_item">Add New</a>
                            <input type="submit" value="Save" class="btn btn-sm btn-success pull-right">
                            {{ Form::button('RESET', ['type' => 'reset', 'class' => 'btn btn-default pull-right'] )  }}
							<a href="/settings/value_lists/{{$value_list->id}}" class="btn btn-sm btn-default pull-right">Undo</a>
						</div>
					</div>
                {{ Form::close() }}

			</div>
		</div>
	</div>
</div>
@stop

@push('scripts')
    <script>
        $(document).ready(function() {
            $("body").on("click", "a.delete_item", function(){
                $(this).closest("tr").remove();
            });

            $("body").on("click", "a.add_item", function(){
                $(".valuelisttable tr:last").clone().appendTo("table").val('');

            });
        });
    </script>
@endpush
