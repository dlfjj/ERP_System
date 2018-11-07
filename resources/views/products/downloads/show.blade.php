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
        <div class="row" style="margin-bottom: 30px;">
            <div class="col-md-12">
                <a class="btn btn-success btn-sm" data-toggle="modal" href="#modal_add_file" class="">Add File</a>
            </div>
        </div>
    @endif
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
									            {{ Form::open(array("action"=>["DownloadController@destroy",$attachment->id],"method"=>"DELETE","class"=>"form-inline","id"=>"v_$attachment->id")) }}
                                                    {{--<button  class="btn btn-xs form-submit-conf"><i class="icon-trash"></i></button>--}}
                                                    {{ Form::button('<i class="icon-trash"></i>', ['type' => 'submit', 'class' => 'btn'] )  }}

                                                {{ Form::close() }}
								</span>
                                        @endif
                                        <a href="/products/downloads/file-download/{{ $attachment->id }}" class="btn"><i class="icon-arrow-down"> Download</i></a>
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

    <div class="modal fade" id="modal_add_file">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title">Add new File</h4>
                </div>
                {{--<form autocomplete="off" enctype="multipart/form-data" id="customer_contact" class="form-validate1" action="" method="POST">--}}
                {!! Form::open(['method'=>'POST','action'=>'DownloadController@store','files' =>true, 'class'=>'form-validate1','id'=>'customer_contact','enctype'=>'multipart/form-data','autocomplete'=>'off']) !!}
                    {{ Form::hidden('product_id',$product->id) }}
                    <div class="modal-body">
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-12">
                                    <p>Choose File</p>
                                    {{--<input type="file" name="file" data-style="fileinput">--}}
                                    {!! Form::file('file_id', ['class'=>'form-controll']) !!}
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
                                    {{--<input name="description" class="form-control input-width-xlarge" type="text" >--}}
                                    {{ Form::text('description',"",['class'=>'form-control input-width-xlarge']) }}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn" data-dismiss="modal">Close</button>
                        {{--<input type="submit" class="btn btn-primary" value="Submit">--}}
                        {!! Form::submit('Submit',['class'=>'btn btn-primary col-sm-2 pull-right']) !!}

                    </div>
                {{--</form>--}}
                {{ Form::close() }}
            </div>
        </div>
    </div>
@stop
