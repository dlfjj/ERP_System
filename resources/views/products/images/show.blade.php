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
    @if(has_role('products_edit'))
        <div class="row">
            <div class="col-md-12">
                <a class="btn btn-success" data-toggle="modal" href="#modal_add_file" style="margin-bottom: 30px;">Add File</a>
            </div>
        </div>
    @endif
    <div class="row">
        <div class="col-md-12">
            <div class="widget box">
                <div class="widget-header">
                    <h4><i class="icon-reorder"></i> Images for Product "{{ $product->product_name }}"</h4>
                    <div class="toolbar">
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
                                        <img src="{{ URL::asset( return_company_id().'/products/'.$product->id.'/'.$attachment->picture) }}" style="max-width: 100px;" /> <br>
                                        {{$attachment->updated_at }}
                                    </td>
                                    <td>{{$attachment->picture }}</td>
                                    <td>{{$attachment->file_size}}</td>
                                    <td>{{$attachment->seo_keyword}}</td>
                                    <td class="align-right">
                                        @if(has_role('products_edit'))

                                            <button class="btn deleteImage" data-id="{{ $attachment->id }}"><i class="icon-trash"></i></button>

                                            <a href="{{ $attachment->id }}/edit" class="btn"><i class="icon-edit"></i></a>
                                            @if($product->picture == $attachment->picture)
                                                <button class="btn unset-main" data-id="{{ $attachment->id }}" >UNSET MAIN IMAGE</button>
                                            @else
                                                <button class="btn set-main" data-id="{{ $attachment->id }}">SET MAIN IMAGE</button>
                                            @endif
                                        @endif
                                        <a href="/products/images/image-download/{{ $attachment->id }}" class="btn"><i class="icon-arrow-down"> Download</i></a>
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

    <div class="modal fade" id="modal_add_file">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title">Add new File</h4>
                </div>
                {!! Form::open(['method'=>'POST','action'=>'ImageController@store','files' =>true, 'class'=>'form-validate1','id'=>'customer_contact','enctype'=>'multipart/form-data','autocomplete'=>'off']) !!}
                {{ Form::hidden('product_id',$product->id) }}
                    <div class="modal-body">
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-6">
                                    <span class="help-block">File:</span>
                                    {!! Form::file('image_id', ['class'=>'form-controll']) !!}
                                </div>
                                <div class="col-md-6">
                                    <span class="help-block">Keyword(s):</span>
                                    {{ Form::text('seo_keyword',"", array("class"=>"form-control")) }}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn" data-dismiss="modal">Close</button>
                        {!! Form::submit('Submit',['class'=>'btn btn-primary col-sm-2 pull-right']) !!}
                    </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>
@stop
@push('scripts')
    <script>
        // delete image using ajax request
        $(".deleteImage").click(function(){
            var id = $(this).data("id");
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax(
                {
                    url:  "{!! url('products/images' ) !!}" + "/" + id,
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

        // set main image for product using ajax request
        $(".set-main").click(function(){
            var id = $(this).data("id");
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax(
                {
                    url:  "{!! url('products/images/mark-as-main-image' ) !!}" + "/" + id,
                    type: 'POST',
                    dataType: "JSON",
                    data: {
                        "id": id,
                        "_method": 'PATCH',
                    },
                    success: function ()
                    {
                    }
                });
        });
        $(".unset-main").click(function(){
            var id = $(this).data("id");
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax(
                {
                    url:  "{!! url('products/images/unmark-as-main-image' ) !!}" + "/" + id,
                    type: 'POST',
                    dataType: "JSON",
                    data: {
                        "id": id,
                        "_method": 'PATCH',
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