@extends('layouts.default')



@section('content')

    <div class="row">

        <!--=== Vertical Forms ===-->

        <div class="col-md-12">

            <div class="widget box">

                <div class="widget-header">

                    <h4><i class="icon-reorder"></i> Customer Details</h4>

                </div>

                <div class="widget-content">

                    {!! Form::open(array('url' => "/customers/create", 'enctype' => 'multipart/form-data', 'id' => 'main', 'method' => 'POST', 'class' => 'form-vertical row-border form-validate', 'autocomplete' => 'off')) !!}
                    {{ Form::hidden('created_by','', array("class"=>"form-control")) }}
                    {{ Form::hidden('updated_by','', array("class"=>"form-control")) }}
                    {{ Form::hidden('company_id','', array("class"=>"form-control")) }}

                    <div class="form-group">

                        <div class="row">

                            <div class="col-md-2">

                                <label class="control-label">Company Code</label>

                                {!! Form::text('customer_code', '', array("class"=>"form-control")) !!}

                            </div>

                            <div class="col-md-4">

                                <label class="control-label">Company Name</label>

                                {!! Form::text('customer_name', '', array("class"=>"form-control")) !!}

                            </div>

                            <div class="col-md-2">

                                <label class="control-label">Customer Group</label>

                                {!! Form::select('group_id', $select_groups, '', array("class"=>"form-control")) !!}

                            </div>

                            <div class="col-md-2">

                                <label class="control-label">Status</label>

                                {!! Form::select('status', array('Active' => 'Active','Prospect' => 'Prospect', 'Inactive' => 'Inactive'), 'Prospect', array("class"=>"form-control")) !!}

                            </div>

                        </div>

                    </div>

                    <div class="form-group">

                        <div class="row">

                            <div class="col-md-3">

                                <label class="control-label">Street Line 1 &amp; 2</label>

                                {!! Form::text('inv_address1', '', array("class"=>"form-control")) !!}

                                &nbsp;

                                {!! Form::text('inv_address2', '', array("class"=>"form-control")) !!}

                            </div>

                            <div class="col-md-2">

                                <label class="control-label">City</label>

                                {!! Form::text('inv_city', '', array("class"=>"form-control")) !!}

                            </div>

                            <div class="col-md-3">

                                <label class="control-label">Postal Code</label>

                                {!!Form::text('inv_postal_code', '', array("class"=>"form-control")) !!}

                            </div>

                            <div class="col-md-2">

                                <label class="control-label">Province</label>

                                {!! Form::text('inv_province', '', array("class"=>"form-control")) !!}

                            </div>

                            <div class="col-md-2">

                                <label class="control-label">Country</label>

                                {!! Form::text('inv_country', '', array("class"=>"form-control")) !!}

                            </div>

                        </div>

                    </div>

                    <div class="form-group">

                        <div class="row">

                            <div class="col-md-2">

                                <label class="control-label">Telephone</label>

                                {!! Form::text('inv_phone', '', array("class"=>"form-control")) !!}

                            </div>

                            <div class="col-md-2">

                                <label class="control-label">Fax</label>

                                {!! Form::text('inv_fax', '', array("class"=>"form-control")) !!}

                            </div>

                            <div class="col-md-2">

                                <label class="control-label">Website URL</label>

                                {!! Form::text('url', '', array("class"=>"form-control")) !!}

                            </div>

                            <div class="col-md-2">

                            </div>

                            <div class="col-md-2">

                                <label class="control-label">Assigned User</label>

                                {!! Form::select('salesman_id', $select_users, '', array("class"=>"form-control")) !!}

                            </div>

                            <div class="col-md-2">

                                <label class="control-label">Commission %</label>

                                {!! Form::text('salesman_commission', '', array("class"=>"form-control")) !!}

                            </div>

                        </div>

                    </div>


                    <div class="form-group">

                        <div class="row">

                            <div class="col-md-2">

                                <label class="control-label">Currency</label>

                                {!! Form::select('currency_code', $select_currency_codes, 'USD', array("class"=>"form-control")) !!}

                            </div>

                            <div class="col-md-4">

                                <label class="control-label">Payment Terms</label>

                                {!! Form::select('payment_terms', $select_payment_terms , '', array("class"=>"form-control")) !!}

                            </div>

                            <div class="col-md-2">

                                <label class="control-label">Default Deposit</label>

                                {!! Form::text('default_deposit', '', array("class"=>"form-control")) !!}

                            </div>

                            <div class="col-md-2">

                                <label class="control-label">Tax ID</label>

                                {!! Form::text('tax_id', '', array("class"=>"form-control")) !!}

                            </div>

                            <div class="col-md-2">

                                <label class="control-label">Tax Code</label>

                                {!! Form::select('taxcode_id', $select_taxcodes, '', array("class"=>"form-control")) !!}

                            </div>

                        </div>

                    </div>

                    <div class="form-group">

                        <div class="row">

                            <div class="col-md-4">

                                <label class="control-label">Remarks</label>

                                {!! Form::textarea('remarks', '', array("rows"=>"3","cols"=>"5","class"=>"form-control")) !!}

                            </div>

                            <div class="col-md-2">

                                <label class="control-label">Credit</label>

                                {!! Form::text('credit', '', array("class"=>"form-control")) !!}

                            </div>

                        </div>

                    </div>

                    <div class="form-group">

                        <div class="row">

                            <div class="col-md-3">

                                <label class="control-label">Forwarder Name</label>

                                {!! Form::text('ff_name', '', array("class"=>"form-control"))!!}

                            </div>

                            <div class="col-md-3">

                                <label class="control-label">Forwarder Contact</label>

                                {!! Form::text('ff_contact', '', array("class"=>"form-control")) !!}

                            </div>

                            <div class="col-md-2">

                                <label class="control-label">Forwarder E-Mail</label>

                                {!! Form::text('ff_email', '', array("class"=>"form-control")) !!}

                            </div>

                            <div class="col-md-2">

                                <label class="control-label">Forwarder Phone</label>

                                {!! Form::text('ff_phone', '', array("class"=>"form-control")) !!}

                            </div>

                            <div class="col-md-2">

                                <label class="control-label">Forwarder Fax</label>

                                {!! Form::text('ff_fax', '', array("class"=>"form-control")) !!}

                            </div>

                        </div>


                        <div class="form-actions">

                            <input type="submit" value="CREATE" class="btn btn-success pull-right">
                            <a href="/customers" class="btn btn-default pull-right">Cancel</a>

                        </div>

                    </div>

                    {!! Form::close() !!}

                </div>

            </div>

        </div>

        <!-- /Vertical Forms -->

    </div>

@stop
