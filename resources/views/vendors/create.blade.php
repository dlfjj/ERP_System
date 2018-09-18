@extends('layouts.default')

@section('content')

    <div class="row">

        <!--=== Vertical Forms ===-->

        <div class="col-md-12">

            <div class="widget box">

                <div class="widget-header">

                    <h4><i class="icon-reorder"></i> Vendor Details</h4>

                </div>

                <div class="widget-content">

                    <form autocomplete="off" enctype="multipart/form-data" id="main"
                          class="form-vertical row-border form-validate" action="/vendors/create"
                          method="POST">
                        {{csrf_field()}}

                        <div class="form-group">

                            <div class="row">

                                <div class="col-md-5">

                                    <label class="control-label">Company Name</label>

                                    {!! Form::text('company_name', '', array("class"=>"form-control required")) !!}

                                </div>

                                <div class="col-md-3">

                                    <label class="control-label">Localized Name</label>

                                    {!!Form::text('company_name_localized', '', array("class"=>"form-control")) !!}

                                </div>

                                <div class="col-md-2">

                                </div>

                                <div class="col-md-2">

                                    <label class="control-label">Status</label>

                                    {!! Form::select('status', array('ACTIVE' => 'ACTIVE','Prospect' => 'Prospect', 'INACTIVE' => 'INACTIVE'), '', array("class"=>"form-control")) !!}

                                </div>

                            </div>

                        </div>

                        <div class="form-group">

                            <div class="row">

                                <div class="col-md-2">

                                    <label class="control-label">Telephone 1</label>

                                    {!!Form::text('telephone_1', '', array("class"=>"form-control")) !!}

                                </div>

                                <div class="col-md-3">

                                    <label class="control-label">Telephone 2</label>

                                    {!! Form::text('telephone_2', '', array("class"=>"form-control")) !!}

                                </div>

                                <div class="col-md-3">

                                    <label class="control-label">Fax</label>
                                    {!! Form::text('fax', '', array("class"=>"form-control")) !!}

                                </div>

                                <div class="col-md-2">

                                    <label class="control-label">E-Mail</label>

                                    {!! Form::text('email', '', array("class"=>"form-control")) !!}

                                </div>

                                <div class="col-md-2">

                                    <label class="control-label">Website URL</label>

                                    {!! Form::text('url', '', array("class"=>"form-control")) !!}

                                </div>

                            </div>

                        </div>

                        <div class="form-group">

                            <div class="row">

                                <div class="col-md-3">

                                    <label class="control-label">Street Line 1 &amp; 2 &amp; Local</label>

                                    {!! Form::text('street_1', '', array("class"=>"form-control")) !!}

                                    &nbsp;

                                    {!! Form::text('street_2', '', array("class"=>"form-control")) !!}

                                    &nbsp;

                                    {!! Form::text('local_address', '', array("class"=>"form-control")) !!}

                                </div>

                                <div class="col-md-2">

                                    <label class="control-label">City</label>

                                    {!! Form::text('city', '', array("class"=>"form-control")) !!}

                                </div>

                                <div class="col-md-3">

                                    <label class="control-label">Postal Code</label>

                                    {!! Form::text('postal_code', '', array("class"=>"form-control")) !!}

                                </div>

                                <div class="col-md-2">

                                    <label class="control-label">Province</label>

                                    {!! Form::text('province', '', array("class"=>"form-control")) !!}

                                </div>

                                <div class="col-md-2">

                                    <label class="control-label">Country</label>

                                    {!! Form::text('country', '', array("class"=>"form-control")) !!}

                                </div>

                            </div>

                        </div>

                        <div class="form-group">

                            <div class="row">

                                <div class="col-md-2">

                                    <label class="control-label">Currency</label>

                                    {!! Form::select('currency_code', $select_currency_codes, '', array("class"=>"form-control")) !!}

                                </div>

                                <div class="col-md-3">

                                    <label class="control-label">Payment Terms</label>

                                    {!! Form::select('payment_terms', $select_payment_terms , '', array("class"=>"form-control")) !!}

                                </div>

                                <div class="col-md-2">

                                    <label class="control-label">Credit (d)</label>

                                    {!! Form::text('credit_days', '', array("class"=>"form-control")) !!}

                                </div>

                                <div class="col-md-3">

                                    <label class="control-label">Bank Details</label>

                                    {!! Form::textarea('bank_details', '', array("rows"=>"3","cols"=>"5","class"=>"form-control")) !!}

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

                                    <label class="control-label">Introduced via</label>

                                    {!! Form::text('introduced_via', '', array("class"=>"form-control")) !!}

                                </div>

                            </div>

                            <div class="form-actions">
                                <input type="submit" value="Create" class="btn btn-sm btn-success pull-right">

                            </div>

                        </div>


                    </form>

                </div>

            </div>

        </div>

        <!-- /Vertical Forms -->

    </div>

@stop

