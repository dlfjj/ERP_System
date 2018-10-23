@extends('layouts.default')

@section('page-crumbs')
    <ul id="breadcrumbs" class="breadcrumb">
        <li>
            <i class="icon-home"></i>
            <a href="/">Dashboard</a>
        </li>
        <li class="current">
            <a href="/users/" title="">Users</a>
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
            @if(has_role('users_edit'))
                <form class="form-inline" id="create" action="/users/create" method="POST">
                    <a class="btn btn-success form-submit-conf" href="javascript:void(0);" data-target-form="create"><i class="icon-plus-sign"></i> New User</a>
                </form>
            @endif
        </div>
    </div>
@stop

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading"><i class="icon-reorder"></i> User Index</div>
                <div class="panel-body">
                    <table class="table table-striped table-bordered table-hover datatable">
                        <thead>
                        <tr>
                            <th>First Name</th>
                            <th>Last Name</th>
                            <th class="hidden-xs">Username</th>
                            <th class="hidden-xs">Company</th>
                            <th>Last Login</th>
                            <th></th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($users as $user)
                            <tr>
                                <td>{{ $user->first_name}}</td>
                                <td>{{ $user->last_name}}</td>
                                <td class="hidden-xs">{{ $user->username }}</td>
                                <td class="hidden-xs">{{ $user->company->name }}</td>
                                <td>{{ $user->last_login }}</td>
                                <td class="align-center">
                                    <ul class="table-controls">
                                        <li><a href="/usersList/{{ $user->id }}" class="bs-tooltip" title="View"><i class="icon-search"></i></a> </li>
                                    </ul>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <!-- /Normal -->




@stop
