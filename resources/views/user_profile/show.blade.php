@extends('layouts.default')
<!--change layout to extends to get the layout page-->
@section('page-crumbs')
    <ul id="breadcrumbs" class="breadcrumb">
        <li>
            <i class="icon-home"></i>
            <a href="/">Dashboard</a>
        </li>
        <li class="current">
            <a href="/user_profile/" title="">My Profile</a>
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
        </div>
    </div>
@stop


@section('content')

    <div class="row">
        <div class="col-md-12">
            <div class="widget box">
                <div class="widget-header">
                    <h4><i class="icon-reorder"></i> My Profile</h4>
                </div>
                <div class="widget-content">
                    {{--<form enctype="multipart/form-data" id="main" class="form-vertical row-border form-validate" action="/user_profile" method="POST">--}}
                    {!! Form::open(['action' => ["UserProfileController@update",$user->id],'id'=>'main', 'enctype' => 'multipart/form-data', 'method' => 'PATCH', 'class' => 'form-vertical row-border form-validate', 'autocomplete' => 'off']) !!}
                    {{ Form::hidden('action', 'change_avatar', array()) }}


                    <div class="form-group">
                            <div class="row">
                                <div class="col-md-2">
                                    <label class="control-label">First Name</label>
                                    {!! Form::text('first_name', $user->first_name, array("class"=>"form-control","readonly")) !!}
                                </div>
                                <div class="col-md-2">
                                    <label class="control-label">Last Name</label>
                                    {!! Form::text('last_name', $user->last_name, array("class"=>"form-control","readonly")) !!}
                                </div>
                                <div class="col-md-2">
                                    <label class="control-label">E-Mail</label>
                                    {!! Form::text('email', $user->email, array("class"=>"form-control","readonly")) !!}
                                </div>
                                <div class="col-md-6">
                                    @if($user->picture != "")
{{--                                        <img style="margin-bottom: 10px;" src="/timthumb.php?src=/public/users/{{ $user->picture }}&w=100" /><br />--}}
                                        <img style="margin-bottom: 0px;display: block;max-width: 100%; height: 150px" src="/users/{{ $user_avatar }}" class="pull-right img-responsive" />
{{--                                        <img style="margin-bottom: 0px;display: block;max-width: 100%; height: 150px" src="/users/{{ $user_avatar }}" class="pull-right img-responsive" />--}}
                                        {{--<img style="margin-bottom: 0px;display: block;max-width: 100%; height: 150px" src="/users/placeholder_200x200.jpg" class="pull-right img-responsive" />--}}
                                        {{--<img src="{{ asset('/users/.'.$user->picture.') }}" /><br />--}}
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-6">
                                    <input type="file" name="picture" data-style="fileinput">
                                    <span class="help-block">Update Picture or Change your Avatar</span>
                                </div>
                            </div>
                            <div class="form-actions">
                                <input type="submit" value="SAVE" class="btn btn-primary pull-right">
                                <a href="/" class="btn btn-default pull-right">Back</a>
                            </div>
                        </div>
                    {{ Form::close() }}
                </div>
            </div>


            <div class="widget box">
                <div class="widget-header">
                    <h4><i class="icon-reorder"></i> Password</h4>
                </div>
                <div class="widget-content">
                    {!! Form::open(['action' => ["UserProfileController@update",$user->id],'id'=>'main', 'enctype' => 'multipart/form-data', 'method' => 'PATCH', 'class' => 'form-vertical row-border form-validate', 'autocomplete' => 'off']) !!}
                    {{ Form::hidden('action', 'password_change', array()) }}
                    <div class="form-group">
                        <div class="row">
                            <div class="col-md-2">
                                <label class="control-label">Username</label>
                                {!! Form::text('username', $user->username, array("class"=>"form-control")) !!}
                                <span class="help-block">Leave as is for no change</span>
                            </div>
                            <div class="col-md-4">
                                <label class="control-label">New Password</label>
                                {!! Form::text('password', "", array("class"=>"form-control")) !!}
                                <span class="help-block">At least 10 characters</span>
                            </div>
                            <div class="col-md-4">
                                <label class="control-label">Confirm Password</label>
                                {!! Form::text('password_conf', "", array("class"=>"form-control")) !!}
                                {{--<span class="help-block">Confirm new Password</span>--}}
                            </div>
                        </div>
                    </div>
                    <div class="form-actions">
                        <input type="submit" value="SAVE" class="btn btn-primary pull-right">
                        {{--<a href="/" class="btn btn-default pull-right">Back</a>--}}
                        {{ Form::button('RESET', ['type' => 'reset', 'class' => 'btn btn-default pull-right'] )  }}
                    </div>
                    {{ Form::close() }}

                </div>
            </div>
        </div>
    </div>

@stop