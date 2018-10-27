@extends('layouts.default')

@section('page-crumbs')
    <ul id="breadcrumbs" class="breadcrumb">
        <li>
            <i class="icon-home"></i>
            <a href="/">Dashboard</a>
        </li>
        <li class="current">
            <a href="/settings">Setting</a>
        </li>
    </ul>

    <ul class="crumb-buttons">
        <li>
            <a href="javascript:void(0);" title=""><i class="icon-calendar"></i><span><?=date('F d, Y \(\K\W:W) H:i:s');?></span></a>
        </li>
    </ul>
@stop


{{--@section('page-header')--}}
    {{--<div class="page-header">--}}
        {{--<div class="page-title">--}}
            {{--<form class="form-inline" id="create" action="/users/create" method="POST">--}}
            {{--</form>--}}
        {{--</div>--}}
    {{--</div>--}}
{{--@stop--}}



@section('content')

    <div class="row">
        <div class="col-md-12">
            <div class="widget box">
                <div class="widget-header">
                    <h4><i class="icon-reorder"></i> System Settings</h4>
                </div>
                <div class="widget-content">
                    {!! Form::open(['action' => ["SettingController@update", $user->id],'id'=>'main', 'enctype' => 'multipart/form-data', 'method' => 'PATCH', 'class' => 'form-vertical row-border form-validate', 'autocomplete' => 'off']) !!}
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-3">
                                </div>
                                <div class="col-md-3">
                                </div>
                                <div class="col-md-3">
                                </div>
                                <div class="col-md-3">
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-3">
                                    <label class="control-label">Default Sales Account</label>
                                    {{ Form::text('sales_account_id', $settings['sales_account_id'], array("class"=>"form-control")) }}
                                </div>
                                <div class="col-md-3">
                                    <label class="control-label">Default Purchase Account</label>
                                    {{ Form::text('purchase_account_id', $settings['purchase_account_id'], array("class"=>"form-control")) }}
                                </div>
                                <div class="col-md-3">
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-12">
                                    <h4>Upload / change company logo</h4>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-2">
                                </div>
                            </div>
                            <div class="form-actions">
                                <input type="submit" value="Save" class="btn btn-sm btn-success pull-right">
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
                    <h4><i class="icon-reorder"></i> Module Settings</h4>
                </div>
                <div class="widget-content">
                    @if(has_role('admin'))
                        <a class="btn btn-default btn-sm" href="settings/value_lists">Value Lists</a>
                        <a class="btn btn-default btn-sm" href="settings/product_categories">Product Categories</a>
                        <a class="btn btn-default btn-sm" href="companies">Companies</a>
                        {{--<a class="btn btn-default btn-sm" href="settings/exchange_rates">Exchange Rates</a>--}}
                        <a class="btn btn-default btn-sm" href="settings/taxcodes">Tax Codes</a>
                        <a class="btn btn-default btn-sm" href="settings/payment_terms">Payment Terms</a>
                        <a class="btn btn-default btn-sm" href="settings/shipping_terms">Shipping Terms</a>
                    @endif
                    <a class="btn btn-default btn-sm" href="settings/chart_of_accounts">Chart of Accounts</a>
                </div>
            </div>
        </div>
    </div>
@stop
