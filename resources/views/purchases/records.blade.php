@extends('layouts.default')


@section('page-module-menu')
    @include('purchases.top_menu')
@stop

@section('page-crumbs')
    @include('purchases.bread_crumbs')
@stop

@section('page-header')
    @include('purchases.page_header')
@stop


@section('content')
    <div class="row">
        {{ Form::open(array("url"=>"/purchases/destroy/$purchase->id","method"=>"post","class"=>"form-inline","id"=>"delete")) }}
        </form>

        <form class="form-inline" id="create" action="/purchases/create" method="POST">
        </form>

        <form class="form-inline" id="print_pdf" action="/purchases/{{$purchase->id}}/print_pdf" method="POST">
        </form>

        <form class="form-inline" id="post" action="/purchases/{{$purchase->id}}/change_status" method="POST">
            {{ Form::hidden('id', $purchase->id, array("class"=>"", 'readonly')) }}
            {{ Form::hidden('status', "UNPAID", array("class"=>"", 'readonly')) }}
        </form>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="widget box">
                <div class="widget-header">
                    <h4><i class="icon-reorder"></i> Record P.O History</h4>
                </div>
                <div class="widget-content">
                    <form enctype="multipart/form-data" id="main" class="form-vertical row-border form-validate" action="/purchases/email/{{$purchase->id}}" method="POST">
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
                                                {{ Form::text('id', $purchase->id, array("class"=>"form-control", 'readonly')) }}
                                            </div>
                                            <div class="col-md-2">
                                                <label class="control-label">Vendor</label>
                                                {{ Form::text('', $vendor->company_name, array("class"=>"form-control", 'readonly')) }}
                                            </div>
                                            <div class="col-md-2">
                                                <label class="control-label">Vendor Contact</label>
                                                {{ Form::text('', $purchase->vendor_contact, array("class"=>"form-control", 'readonly')) }}
                                            </div>
                                            <div class="col-md-2">
                                                <label class="control-label">Currency</label>
                                                {{ Form::text('', $purchase->currency_code, array("class"=>"form-control", 'readonly')) }}
                                            </div>
                                            <div class="col-md-2">
                                                {{ Form::label('user_id', "Assigned User") }}
                                                {{ Form::text('', $purchase->user->username, array("class"=>"form-control", 'readonly')) }}
                                            </div>
                                            <div class="col-md-2">
                                                <label class="control-label">Payment Terms</label>
                                                {{ Form::text('', $purchase->payment_terms, array("class"=>"form-control", 'readonly')) }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <p>To:</p>
                                                @foreach($vendor->contacts as $vendor_contact)
                                                    @if($vendor_contact->default_to)
                                                        {{ Form::checkbox('mail_to[]', $vendor_contact->email, true) }}
                                                    @else
                                                        {{ Form::checkbox('mail_to[]', $vendor_contact->email ) }}
                                                    @endif
                                                    <label class="control-label">{{$vendor_contact->name}}</label>
                                                    <br />
                                                @endforeach
                                            </div>
                                            <div class="col-md-4">
                                                <p>Cc:</p>
                                                @foreach($vendor->contacts as $vendor_contact)
                                                    @if($vendor_contact->default_cc)
                                                        {{ Form::checkbox('mail_cc[]', $vendor_contact->email, true) }}
                                                    @else
                                                        {{ Form::checkbox('mail_cc[]', $vendor_contact->email) }}
                                                    @endif
                                                    <label class="control-label">{{$vendor_contact->name}}</label>
                                                    <br />
                                                @endforeach
                                            </div>
                                            <div class="col-md-2">
                                                <label class="control-label">Mail Bcc:</label>
                                                {{ Form::text('mail_bcc', $mail_bcc, array("class"=>"form-control" )) }}
                                            </div>
                                            <div class="col-md-2">
                                                <label class="control-label">Attach Invoice as PDF</label>
                                                {{ Form::select('attach_pdf', array("1"=>"Yes","0"=>"No"), "Yes", array("class"=>"form-control")) }}
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
                                                {{ Form::textarea('mail_body', $mail_body, array("rows"=>"3","cols"=>"5","class"=>"form-control")) }}
                                            </div>
                                        </div>
                                        <div class="form-actions">
                                            <input type="submit" value="Save" class="btn btn-sm btn-success pull-right">
                                            <a href="/purchases/{{$purchase->id}}" class="btn btn-sm btn-default pull-right">Cancel</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                </div>
            </div>
            </form>
        </div>
    </div>

    <!--=== Box Tabs ===-->
    <div class="row">
        <div class="col-md-12">
            <div class="widget box">
                <div class="widget-header">
                    <h4><i class="icon-reorder"></i> Order History</h4>
                </div>
                <div class="widget-content">
                    <div class="tabbable box-tabs">
                        <ul class="nav nav-tabs">
                            <li class="active"><a href="#box_tab1" data-toggle="tab">General</a></li>
                        </ul>
                        <div class="tab-content">
                            <div class="tab-pane active" id="box_tab1">
                                <table class="table table-hover">
                                    <thead>
                                    <tr>
                                        <th>Timestamp</th>
                                        <th>Created by</th>
                                        <th>Message</th>
                                        <th>File attached</th>
                                        <th>Vendor informed</th>
                                        <th class="align-right">
                                            -
                                        </th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                                </form>
                            </div>
                            <div class="tab-pane" id="box_tab4">
                            </div>
                        </div>
                    </div> <!-- /.tabbable portlet-tabs -->
                </div> <!-- /.widget-content -->
            </div> <!-- /.widget .box -->
        </div> <!-- /.col-md-12 -->
    </div> <!-- /.row -->
    <!-- /Box Tabs -->

    <div class="row">
        <div class="col-md-12 no-padding">
            {{--<p class="record_status">Created: {{$purchase->created_at}} | Created by: {{User::find($purchase->created_by)->username}} | Updated: {{$purchase->updated_at}} | Updated by: {{User::find($purchase->updated_by)->username}}</p>--}}
        </div>
    </div>

@stop
