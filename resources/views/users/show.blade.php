@extends('layouts.default')

@section('page-crumbs')
    <ul id="breadcrumbs" class="breadcrumb">
        <li>
            <i class="icon-home"></i>
            <a href="/">Dashboard</a>
        </li>
        <li>
            <a href="/usersList">Users List</a>
        </li>
        <li class="current">
            <a href="/users/{{$user->id}}" title="">User Details</a>
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
            <h3>{{ $user->first_name }} {{ $user->last_name }}</h3>
        </div>

        <ul class="page-stats">
            <li>
                @if(has_role('admin'))
                    {{--<form enctype="multipart/form-data" class="" action="/usersList/loginAs/{{ $user->id}}" method="POST">--}}
                    {!! Form::open(['method'=>'GET','action'=>['UserController@postLoginAs', $user->id]], array('enctype'=>'multipart/form-data')) !!}
                        <input type="submit" name='submit' class="btn btn-sm btn-primary" value="Login as {{$user->username}}" />
                    {!! Form::close() !!}
                @endif
            </li>
        </ul>
    </div>
@stop



@section('content')

    <div class="row">
        <div class="col-md-12">
            <div class="widget box">
                <div class="widget-header">
                    <h4><i class="icon-reorder"></i> User Information</h4>
                </div>
                <div class="widget-content">
                    {!! Form::open(['method'=>'PUT', 'action'=> ['UserController@update', $user->id], 'class'=>'form-vertical row-border form-validate','id'=>'main','enctype'=>'multipart/form-data']) !!}
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-2">
                                    <label class="control-label">First Name</label>
                                    {{ Form::text('first_name', $user->first_name, array("class"=>"form-control")) }}
                                </div>
                                <div class="col-md-2">
                                    <label class="control-label">Last Name</label>
                                    {{ Form::text('last_name', $user->last_name, array("class"=>"form-control")) }}
                                </div>
                                <div class="col-md-2">
                                    <label class="control-label">E-Mail</label>
                                    {{ Form::text('email', $user->email, array("class"=>"form-control")) }}
                                </div>
                                <div class="col-md-6 text-right">
                                    @if($user->picture != "")
{{--                                        <img height="50" src="{{$user_avatar}}" /><br />--}}
                                        <img style="margin-bottom: 10px;" src="/timthumb.php?src=/public/users/{{ $user->picture }}&w=100" /><br />
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-12">
                                    <h4>Change Username / Password here</h4>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-2">
                                    <label class="control-label">Allow User Login?</label><br />
                                    <div class="make-switch" data-on-label="Yes" data-off-label="No">
                                        <input type="checkbox" name="can_login" class="toggle" {{ $user->can_login == 1 ? 'checked' : '' }} />
                                    </div>
                                    <span class="help-block">Last: {{ $user->last_login }}</span>
                                </div>
                                <div class="col-md-2">
                                    <label class="control-label">Username</label>
                                    {{ Form::text('username', $user->username, array("class"=>"form-control")) }}
                                    <span class="help-block">Leave as is for no change</span>
                                </div>
                                <div class="col-md-2">
                                    <label class="control-label">Password</label>
                                    {{ Form::text('password', "", array("class"=>"form-control")) }}
                                    <span class="help-block">At least 10 characters</span>
                                </div>
                                <div class="col-md-2">
                                    <label class="control-label">Password conf</label>
                                    {{ Form::text('password_conf', "", array("class"=>"form-control")) }}
                                    <span class="help-block">Confirm new Password</span>
                                </div>
                                <div class="col-md-2">
                                    <label class="control-label">Redirect</label>
                                    {{ Form::text('redirect_to', $user->redirect_to, array("class"=>"form-control")) }}
                                    <span class="help-block">Redirect after Login</span>
                                </div>
                                <div class="col-md-2">
                                    <label class="control-label">Company</label>
                                    {{ Form::select('company_id', $select_companies, $user->company_id, array("class"=>"form-control")) }}
                                    <span class="help-block">Company ID</span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-12">
                                    <h4>E-Mail Signature</h4>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-12">
                                    <textarea rows="3" cols="5" name="signature" class="form-control">{{ $user->signature }}</textarea>
                                    <span class="help-block">E-Mail Signature</span>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-12">
                                    <h4>Roles / Permissions</h4>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-12">
                                    <table class="table table-hover table-checkable">
                                        <thead>
                                        <tr>
                                            <th></th>
                                            <th>Role Name</th>
                                            <th>Role Description</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($roles as $role)
                                            <tr>
                                                <td>
                                                    @if($role_user->contains($role->id))
                                                        {{ Form::checkbox('roles[]', $role->id, true, array("class"=>"uniform")) }}
                                                    @else
                                                        {{ Form::checkbox('roles[]', $role->id, false, array("class"=>"uniform")) }}
                                                    @endif
                                                </td>
                                                <td>{{ $role->name }}</td>
                                                <td>{{ $role->description }}</td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-12">
                                    <h4>Purchase Module Settings</h4>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-4">
                                    <label class="control-label">P.O Amount Limit</label>
                                    {{ Form::text('purchase_limit_amount', $user->purchase_limit_amount, array("class"=>"form-control")) }}
                                    <span class="help-block">Can approve P.O's < USD {{$user->purchase_limit_amount}}</span>
                                </div>
                                {{--<div class="col-md-3">--}}
                                {{--</div>--}}
                                {{--<div class="col-md-3">--}}
                                {{--</div>--}}
                                {{--<div class="col-md-3">--}}
                                {{--</div>--}}
                            </div>
                        </div>

                        {{--<div class="form-group">--}}
                            {{--<div class="row">--}}
                                {{--<div class="col-md-12">--}}
                                    {{--<h4>Upload / Change Avatar</h4>--}}
                                {{--</div>--}}
                            {{--</div>--}}
                        {{--</div>--}}

                        <div class="form-group">
                            {{--<div class="row">--}}
                                {{--<div class="col-md-2">--}}
                                    {{--<input type="file" name="picture" data-style="fileinput">--}}
                                    {{--<span class="help-block">Update Picture</span>--}}
                                {{--</div>--}}
                            {{--</div>--}}
                            <div class="form-actions">
                                @if(has_role('users_edit'))
                                    <input type="submit" value="SAVE" class="btn btn-success pull-right">
                                    <a class="btn btn-danger pull-right conf" href="/users/destroy/{{$user->id}}"><i class="icon-trash"></i> Delete</a>
                                @endif
                                <a href="/users" class="btn btn-default pull-right">Cancel</a>
                            </div>
                        </div>
                    {{ Form::close() }}
                </div>
            </div>
        </div>
    </div>

@stop
