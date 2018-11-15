@extends('layouts.default')

@section('page-module-menu')
    @include('orders.top_menu')
@stop

@section('page-crumbs')
    @include('orders.bread_crumbs')
@stop

@section('page-header')
    @include('orders.page_header')
@stop

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="widget box">
                <div class="widget-header">
                    <h4><i class="icon-reorder"></i> Record order History</h4>
                </div>
                <div class="widget-content">
                    {{--<form enctype="multipart/form-data" id="main" class="form-vertical row-border form-validate" method="POST">--}}
                        {!! Form::open(['method'=>'POST','action'=>['EmailController@sendOrderEmail', $order->id],'files' =>false], array('enctype'=>'multipart/form-data','id'=>'main','class' => 'form-vertical row-border form-validate')) !!}
                        <div class="tabbable box-tabs">
                            <ul class="nav nav-tabs">
                                <li class="active"><a href="#box_tab1" data-toggle="tab">General</a></li>
                            </ul>
                            <div class="tab-content">
                                <div class="tab-pane active" id="box_tab1">
                                    <div class="form-group">
                                        <div class="row">
                                            <div class="col-md-2">
                                                <label class="control-label">ID</label>
                                                {{ Form::text('', $order->order_no, array("class"=>"form-control", 'readonly')) }}
                                            </div>
                                            <div class="col-md-2">
                                                <label class="control-label">Customer</label>
                                                {{ Form::text('', $customer->customer_name, array("class"=>"form-control", 'readonly')) }}
                                            </div>
                                            <div class="col-md-2">
                                                <label class="control-label">Customer Contact</label>
                                                {{ Form::text('', $order->customerContact->contact_name, array("class"=>"form-control", 'readonly')) }}
                                            </div>
                                            <div class="col-md-2">
                                                <label class="control-label">Currency</label>
                                                {{ Form::text('', $order->currency_code, array("class"=>"form-control", 'readonly')) }}
                                            </div>
                                            <div class="col-md-4">
                                                <label class="control-label">Status</label>
                                                {{ Form::select('status_id', $select_status, $order->status_id, array("class"=>"form-control", "id" => "select_order_status")) }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <p>To:</p>
                                                @foreach($customer->contacts as $customer_contact)
                                                    @if($customer_contact->system_emails >= 2)
                                                        {{ Form::checkbox('mail_to[]', $customer_contact->username, true) }}
                                                    @else
                                                        {{ Form::checkbox('mail_to[]', $customer_contact->username) }}
                                                    @endif
                                                    <label class="control-label">{{$customer_contact->contact_name }}</label>
                                                    <br />
                                                @endforeach
                                            </div>
                                            <div class="col-md-3">
                                                <p>Cc:</p>
                                                @foreach($customer->contacts as $customer_contact)
                                                    @if($customer_contact->default_cc)
                                                        {{ Form::checkbox('mail_cc[]', $customer_contact->username, true) }}
                                                    @else
                                                        {{ Form::checkbox('mail_cc[]', $customer_contact->username) }}
                                                    @endif
                                                    <label class="control-label">{{$customer_contact->contact_name }}</label>
                                                    <br />
                                                @endforeach
                                            </div>
                                            <div class="col-md-3">
                                                <label class="control-label">Mail Bcc:</label>
                                                {{ Form::text('mail_bcc', $mail_bcc, array("class"=>"form-control" )) }}
                                            </div>
                                            <div class="col-md-3">
                                                <label class="control-label">Settings</label><br />

                                                {{ Form::checkbox('inform_customer', "1", true) }}
                                                <label class="control-label"> Inform Customer</label>

                                                <br />
                                                {{ Form::checkbox('record_file', "1", true) }}
                                                <label class="control-label"> Record File</label>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <label class="control-label">Mail Subject:</label>
                                                {{ Form::text('mail_subject', $mail_subject, array("class"=>"form-control" )) }}
                                            </div>
                                            <div class="col-md-6">
                                                <label class="control-label">Message Body:</label>
                                                {{ Form::textarea('mail_body', $mail_body, array("rows"=>"12","cols"=>"5","class"=>"form-control", "id" => "history_comment")) }}
                                            </div>
                                        </div>
                                        <div class="form-actions">
                                            {{--<a href="/testmail" class="btn btn-info" role="button">Send Mail</a>--}}

                                            <input type="submit" value="Send Email and Record Message" class="btn btn-sm btn-success pull-right">
                                            {{ Form::button('RESET', ['type' => 'reset', 'class' => 'btn btn-default pull-right'] )  }}
                                            {{--<a href="/orders/{{$order->id}}" class="btn btn-sm btn-default pull-right">Cancel</a>--}}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        {{ Form::close() }}
                    {{--</form>--}}
                </div>
            </div>
        </div>
    </div>

    <!--=== Box Tabs ===-->
    <div class="row">
        <div class="col-md-12">
            <div class="widget box">
                <div class="widget-header">
                    <h4><i class="icon-reorder"></i> order History</h4>
                </div>
                <div class="widget-content">
                    <div class="tabbable">
                        {{--<ul class="nav nav-tabs">--}}
                            {{--<li class="active"><a href="#box_tab1" data-toggle="tab">General</a></li>--}}
                        {{--</ul>--}}
                        <div class="tab-content">
                            {{--<div class="tab-pane active" id="box_tab1">--}}
                                <table class="table table-hover" id="email-record-table" style="width:100%;">
                                    <thead>
                                    <tr>
                                        <th>Timestamp</th>
                                        <th>Username</th>
                                        <th>Status</th>
                                        <th>Message</th>
                                        <th>File</th>
                                        <th>Customer Notified?</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                        @if(count($order->history)>0)
                                            @foreach($order_history as $history)
                                                <tr class="order-form-row">
                                                    <td>{{$history->created_at}}</td>
                                                    <td>{{$history->username }}</td>
                                                    <td>{{$history->status()->first()->name }}</td>
                                                    <td>{!! ($history->comment) !!}</td>
                                                    <td>
                                                        @if($history->file_name != "")
                                                            <a href="/orders/download/{{$history->id}}" class="" rel="{{ $history->id }}">Download</a>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if($history->notify_customer == 1)
                                                            Yes
                                                        @else
                                                            No
                                                        @endif
                                                    </td>

                                                </tr>
                                            @endforeach
                                        @endif
                                    </tbody>
                                </table>
                        </div>
                    </div> <!-- /.tabbable portlet-tabs -->
                </div> <!-- /.widget-content -->
            </div> <!-- /.widget .box -->
        </div> <!-- /.col-md-12 -->
    </div> <!-- /.row -->
    <!-- /Box Tabs -->

    <div class="row">
        <div class="col-md-12 no-padding">
            <p class="record_status">Created: {{$order->created_at}} | Created by: {{$the_user_created_this_order}} | Updated: {{$order->updated_at}} | Updated by: {{ $the_user_updated_this_order }}</p>
        </div>
    </div>

@stop
