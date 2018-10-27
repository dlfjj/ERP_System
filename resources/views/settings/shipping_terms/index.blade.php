@extends('layouts.default')

@section('page-crumbs')
    <ul id="breadcrumbs" class="breadcrumb">
        <li>
            <i class="icon-home"></i>
            <a href="/">Dashboard</a>
        </li>
        <li>
            <a href="/settings">Settings</a>
        </li>
        <li class="current">
            <a href="/settings/shipping_terms/" title="">Shipping Terms</a>
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
            {{ Form::open(['method'=>'GET','action'=>['ShippingTermController@create'],'class'=>'form-inline','id'=>'create']) }}
            <a class="btn btn-success form-submit-conf" href="javascript:void(0);" data-target-form="create"><i class="icon-plus-sign"></i> New Shipping Term</a>
            {{ Form::close() }}
        </div>
    </div>
@stop


@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="widget box">
                <div class="widget-header">
                    <h4><i class="icon-reorder"></i> Shipping Terms</h4>
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
                            <th>Description</th>
                            <th>Sort order</th>
                            <th></th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($shipping_terms as $shipping_term)
                            {!! Form::open(['method'=>'DELETE', 'action'=> ['ShippingTermController@destroy', $shipping_term->id], 'enctype'=>'multipart/form-data']) !!}
                            <tr>
                                <td>{{$shipping_term->name}}</td>
                                <td>{{$shipping_term->sort_no }}</td>
                                <td class="align-right">
                                    <span class="btn-group">
                                        <a href="/settings/shipping_terms/{{$shipping_term->id}}" class="btn"><i class="icon-edit"></i></a>
                                        {{--<a href="/shipping_terms/delete/{{$shipping_term->id}}" class="btn btn-xs"><i class="icon-trash"></i></a>--}}
                                        {{ Form::button('<i class="icon-trash"></i>', ['type' => 'submit', 'class' => 'btn'] )  }}
                                    </span>
                                </td>
                            </tr>
                            {!! Form::close() !!}
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <!-- /Normal -->
@stop
