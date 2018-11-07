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
{{--                        {{ Form::open() }}--}}

                        @if($product->attributes->count() > 0)

                            @foreach($attributes as $attribute)
                                {!! Form::open(['method'=>'PATCH', 'action'=> ['AttributeController@update', $attribute->id], 'class'=>'form-vertical row-border form-validate','id'=>'main','enctype'=>'multipart/form-data']) !!}
                                {!!  Form::hidden('product_id', $product->id, array()) !!}
                                <tr>
                                    <td>
                                        {{ Form::text("group", $attribute->group, array("class"=>"form-control")) }}
                                    </td>
                                    <td>
                                        {{ Form::text("name", $attribute->name, array("class"=>"form-control")) }}
                                    </td>
                                    <td>
                                        {{ Form::text("value", $attribute->value, array("class"=>"form-control")) }}
                                    </td>
                                    <td class="align-right">
                                        {{ Form::button('RESET', ['type' => 'reset', 'class' => 'btn'] )  }}
                                        @if(has_role('products_edit'))
                                            {{--use ajax request to delete the row--}}
                                            <button class="btn deleteAttribute" data-id="{{ $attribute->id }}">REMOVE</button>

                                        @endif
                                        {{ Form::hidden('action', "mass_update", array()) }}
                                        {{--<input type="submit" value="UPDATE" class="btn">--}}
                                        {{ Form::submit('UPDATE',['class'=>'btn']) }}
                                    </td>
                                </tr>
                                {{ Form::close() }}
                            @endforeach
                        @else
                            <tr>
                                <td colspan="4">Nothing found</td>
                            </tr>
                        @endif

                        {!! Form::open(['method'=>'POST','action'=>'AttributeController@store']) !!}
{{--                        {!! Form::open(['method'=>'PUT', 'action'=> ['AttributeController@update', $product->id], 'class'=>'form-vertical row-border form-validate','id'=>'main','enctype'=>'multipart/form-data']) !!}--}}
                        {!!  Form::hidden('action', "attribute_add", array()) !!}
                        {!!  Form::hidden('id', $product->id, array()) !!}
                        <tr>
                            <td>
                                {!! Form::text('group', "", array("class"=>"form-control")) !!}
                            </td>
                            <td>
                                {!! Form::text('name', "", array("class"=>"form-control")) !!}
                            </td>
                            <td>
                                {!! Form::text('value', "", array("class"=>"form-control")) !!}
                            </td>
                            <td>
                                {{--<input type="submit" value="ADD/CHANGE" class="btn btn-xs pull-right">--}}
                                {!! Form::submit('ADD', ['class' => 'btn pull-right']) !!}

                            </td>
                        </tr>
                        {!! Form::close() !!}

                        </tbody>
                    </table>
                </div>
            </div>
            <!-- /Simple Table -->
        </div>
    </div>

@stop
@push('scripts')
    <script>
        $(".deleteAttribute").click(function(){
            var id = $(this).data("id");
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax(
                {
                    url:  "{!! url('products/attributes' ) !!}" + "/" + id,
                    type: 'POST',
                    dataType: "JSON",
                    data: {
                        "id": id,
                        "_method": 'DELETE',
                    },
                    // url: "products/attributes/"+id,
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