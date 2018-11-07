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
            <a href="/settings/chart_of_accounts/" title="">Chart of Accounts Top</a>
        </li>
        {{--<li class="current">--}}
            {{--<a href="/settings/chart_of_accounts/lower-level/{{ $account->id }}" title="">{{ $account->name }}</a>--}}
        {{--</li>--}}

        {!! $path !!}
    </ul>

    <ul class="crumb-buttons">
        <li>
            <a href="javascript:void(0);" title=""><i class="icon-calendar"></i><span><?=date('F d, Y \(\K\W:W) H:i:s');?></span></a>
        </li>
    </ul>
@stop

@section('page-header')
    <div class="page-header">
        {{--<div class="page-title">--}}

        {{--</div>--}}
    </div>
@stop

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="widget box">
                <div class="widget-header">
                    @if($account)
                        <h4><i class="icon-reorder"></i> Add subaccount to <em>{{$account->name}}</em></h4>
                    @else
                        <h4><i class="icon-reorder"></i> Add new sub-level account</h4>
                    @endif
                </div>
                <div class="widget-content">
                    {!! Form::open(['method'=>'GET','action'=>['ChartOfAccountController@create']], array('enctype'=>'multipart/form-data','id'=>'main','class' => 'form-vertical row-border form-validate')) !!}
                    <div class="form-group">
                        <div class="row">
                            <div class="col-md-2">
                                <label class="control-label">Code</label>
                                {{ Form::input('number','code', "", array("class"=>"form-control","step"=>"1")) }}
                                {{ Form::hidden('parent_id', $account_id, array("class"=>"form-control")) }}
                            </div>
                            <div class="col-md-2">
                                <label class="control-label">Type</label>
                                {{ Form::select('type', $select_account_type, "", array("class"=>"form-control")) }}
                            </div>
                            <div class="col-md-2">
                                <label class="control-label">Account Name</label>
                                {{ Form::text('name', "", array("class"=>"form-control")) }}
                            </div>
                        </div>
                        <div class="form-actions">
                            <input type="submit" value="Add account" class="btn btn-sm btn-success pull-right">
                        </div>
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
                    <h4><i class="icon-reorder"></i> Accounts</h4>
                    {{--<div class="toolbar no-padding">--}}
                    {{--<div class="btn-group">--}}
                    {{--<span class="btn btn-xs widget-collapse"><i class="icon-angle-down"></i></span>--}}
                    {{--</div>--}}
                    {{--</div>--}}
                </div>
                <div class="widget-content no-padding">
                    <table class="table table-striped table-bordered table-hover">
                        <thead>
                        <tr>
                            <th>Code</th>
                            <th>Type</th>
                            <th>Name</th>
                            <th style="width: 125px;" class="no-break"></td>
                        </tr>
                        </thead>
                        <tbody>
                        @if($accounts->count()>0)
                            @foreach($accounts as $account)
                                {!! Form::open(['method'=>'DELETE', 'action'=> ['ChartOfAccountController@destroy', $account->id], 'enctype'=>'multipart/form-data']) !!}

                                <tr>
                                    <td>{{$account->code}}</td>
                                    <td>{{$account->type}}</td>
                                    <td>{{$account->name}}</td>
                                    <td class="no-break">
                                        <ul class="table-controls">
                                            <li><a href="/settings/chart_of_accounts/lower-level/{{ $account->id }}" class="bs-tooltip" title="View"><i class="icon-search"></i></a></li>
                                            <li><a href="/settings/chart_of_accounts/{{ $account->id }}" class="btn" title="Update"><i class="icon-edit"></i></a></li>
                                            {{ Form::button('<i class="icon-trash"></i>', ['type' => 'submit', 'class' => 'btn'] )  }}
                                            {{--<li><a href="/settings/chart_of_accounts/delete/{{ $account->id }}" class="bs-tooltip" title="Delete"><i class="icon-remove"></i></a></li>--}}
                                        </ul>
                                    </td>
                                </tr>

                                {!! Form::close() !!}
                            @endforeach
                        @else
                            <tr>
                                <td colspan="3">Nothing found</td>
                            </tr>
                        @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <!-- /Normal -->
@stop
