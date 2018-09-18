@extends('layouts.default')



@section('page-module-menu')

    <li><a href="/customers/{{$customer->id}}">General</a></li>

    <li><a href="#">History</a></li>
    <!--customer/getHistory/$customer->id-->
    <li><a href="#">Products</a></li>
    <!--customer/getProducts/$customer->id-->

@stop





@section('page-crumbs')

    <ul id="breadcrumbs" class="breadcrumb">

        <li>

            <i class="icon-home"></i>

            <a href="/dashboard">Dashboard</a>

        </li>

        <li>

            <a href="/customers" title="">Customers</a>

        </li>

        <li class="current">

            <a href="/customers/{{$customer->id}}" title="">Details</a>

        </li>

    </ul>



    <ul class="crumb-buttons">

    <!-- @if(has_role('customers_pricelist')) -->
    <!-- /customer/getPricelist/{{ $customer->id }} -->
        <li><a href="#" class="" title=""><i class="icon-table"></i><span>Pricelist</span></a></li>

    <!-- @endif -->

    <!-- @if(has_role('customers_opos')) -->
    <!-- /customer/getOpos/{{ $customer->id }} -->
        <li><a href="#" class="" title=""><i class="icon-table"></i><span>OPOS</span></a></li>

    <!-- @endif -->

        <li>

            <a href="javascript:void(0);" title=""><i
                        class="icon-calendar"></i><span><?=date('F d, Y \(\K\W:W) H:i:s');?></span></a>

        </li>

    </ul>

@stop





@section('page-header')

    <?php

    ?>

    <div class="page-header">

        <div class="page-title">

        </div>

        <ul class="page-stats">

            @if(has_role('invoices'))

                <li>

                    <div class="summary">

                        <span>Outstandings</span>

                        <h3>{{ $outstanding_currency }} {{ number_format($outstandings,2) }}</h3>

                    </div>

                </li>

                <li>

                    <div class="summary">

                        <span>Overdue</span>

                        <h3>{{ $overdue_currency }} {{ number_format($overdue,2) }}</h3>

                    </div>

                </li>

            @endif

        </ul>

    </div>

@stop



@section('content')

    <div class="row">


        {!! Form::open(array("url"=>"/customers/$customer->id","method"=>"DELETE","class"=>"form-inline","id"=>"delete")) !!}

        </form>


        <!--=== Vertical Forms ===-->

        <div class="col-md-12">

            <div class="widget box">

                <div class="widget-header">

                    <h4><i class="icon-reorder"></i> Customer Details</h4>

                </div>

                <div class="widget-content">

                    {!! Form::open(array('url' => "/customers/$customer->id", 'enctype' => 'multipart/form-data', 'id' => 'main', 'method' => 'post', 'class' => 'form-vertical row-border form-validate', 'autocomplete' => 'off')) !!}

                    <div class="form-group">

                        <div class="row">

                            <div class="col-md-2">

                                <label class="control-label">Company Code</label>

                                {!! Form::text('customer_code', $customer->customer_code, array("class"=>"form-control")) !!}

                            </div>

                            <div class="col-md-4">

                                <label class="control-label">Company Name</label>

                                {!! Form::text('customer_name', $customer->customer_name, array("class"=>"form-control")) !!}

                            </div>

                            <div class="col-md-2">

                                <label class="control-label">Customer Group</label>

                                {!! Form::select('group_id', $select_groups, $customer->group_id, array("class"=>"form-control")) !!}

                            </div>

                            <div class="col-md-2">

                                <label class="control-label">Main Contact</label>

                                {!! Form::select('customer_contact_id', $select_contacts, $customer->customer_contact_id, array("class"=>"form-control")) !!}

                            </div>

                            <div class="col-md-2">

                                <label class="control-label">Status</label>

                                {!! Form::select('status', array('Active' => 'Active','Prospect' => 'Prospect', 'Inactive' => 'Inactive'), $customer->status, array("class"=>"form-control")) !!}

                            </div>

                        </div>

                    </div>

                    <div class="form-group">

                        <div class="row">

                            <div class="col-md-3">

                                <label class="control-label">Street Line 1 &amp; 2</label>

                                {!! Form::text('inv_address1', $customer->inv_address1, array("class"=>"form-control")) !!}

                                &nbsp;

                                {!! Form::text('inv_address2', $customer->inv_address2, array("class"=>"form-control")) !!}

                            </div>

                            <div class="col-md-2">

                                <label class="control-label">City</label>

                                {!! Form::text('inv_city', $customer->inv_city, array("class"=>"form-control")) !!}

                            </div>

                            <div class="col-md-3">

                                <label class="control-label">Postal Code</label>

                                {!!Form::text('inv_postal_code', $customer->inv_postal_code, array("class"=>"form-control")) !!}

                            </div>

                            <div class="col-md-2">

                                <label class="control-label">Province</label>

                                {!! Form::text('inv_province', $customer->inv_province, array("class"=>"form-control")) !!}

                            </div>

                            <div class="col-md-2">

                                <label class="control-label">Country</label>

                                {!! Form::text('inv_country', $customer->inv_country, array("class"=>"form-control")) !!}

                            </div>

                        </div>

                    </div>

                    <div class="form-group">

                        <div class="row">

                            <div class="col-md-2">

                                <label class="control-label">Telephone</label>

                                {!! Form::text('inv_phone', $customer->inv_phone, array("class"=>"form-control")) !!}

                            </div>

                            <div class="col-md-2">

                                <label class="control-label">Fax</label>

                                {!! Form::text('inv_fax', $customer->inv_fax, array("class"=>"form-control")) !!}

                            </div>

                            <div class="col-md-2">

                                <label class="control-label">Website URL</label>

                                {!! Form::text('url', $customer->url, array("class"=>"form-control")) !!}

                            </div>

                            <div class="col-md-2">

                            </div>

                            <div class="col-md-2">

                                <label class="control-label">Assigned User</label>

                                {!! Form::select('salesman_id', $select_users, $customer->salesman_id, array("class"=>"form-control")) !!}

                            </div>

                            <div class="col-md-2">

                                <label class="control-label">Commission %</label>

                                {!! Form::text('salesman_commission', $customer->salesman_commission, array("class"=>"form-control")) !!}

                            </div>

                        </div>

                    </div>


                    <div class="form-group">

                        <div class="row">

                            <div class="col-md-2">

                                <label class="control-label">Currency</label>

                                {!! Form::select('currency_code', $select_currency_codes, (isset($customer->currency_code) ? $customer->currency_code: ""), array("class"=>"form-control")) !!}

                            </div>

                            <div class="col-md-4">

                                <label class="control-label">Payment Terms</label>

                                {!! Form::select('payment_terms', $select_payment_terms , $customer->payment_terms, array("class"=>"form-control")) !!}

                            </div>

                            <div class="col-md-2">

                                <label class="control-label">Default Deposit</label>

                                {!! Form::text('default_deposit', $customer->default_deposit, array("class"=>"form-control")) !!}

                            </div>

                            <div class="col-md-2">

                                <label class="control-label">Tax ID</label>

                                {!! Form::text('tax_id', $customer->tax_id, array("class"=>"form-control")) !!}

                            </div>

                            <div class="col-md-2">

                                <label class="control-label">Tax Code</label>

                                {!! Form::select('taxcode_id', $select_taxcodes, $customer->taxcode_id, array("class"=>"form-control")) !!}

                            </div>

                        </div>

                    </div>

                    <div class="form-group">

                        <div class="row">

                            <div class="col-md-4">

                                <label class="control-label">Remarks</label>

                                {!! Form::textarea('remarks', $customer->remarks, array("rows"=>"3","cols"=>"5","class"=>"form-control")) !!}

                            </div>

                            <div class="col-md-2">

                                <label class="control-label">Credit</label>

                                {!! Form::text('credit', $customer->credit, array("class"=>"form-control")) !!}

                            </div>

                        </div>

                    </div>

                    <div class="form-group">

                        <div class="row">

                            <div class="col-md-3">

                                <label class="control-label">Forwarder Name</label>

                                {!! Form::text('ff_name', $customer->ff_name, array("class"=>"form-control"))!!}

                            </div>

                            <div class="col-md-3">

                                <label class="control-label">Forwarder Contact</label>

                                {!! Form::text('ff_contact', $customer->ff_contact, array("class"=>"form-control")) !!}

                            </div>

                            <div class="col-md-2">

                                <label class="control-label">Forwarder E-Mail</label>

                                {!! Form::text('ff_email', $customer->ff_email, array("class"=>"form-control")) !!}

                            </div>

                            <div class="col-md-2">

                                <label class="control-label">Forwarder Phone</label>

                                {!! Form::text('ff_phone', $customer->ff_phone, array("class"=>"form-control")) !!}

                            </div>

                            <div class="col-md-2">

                                <label class="control-label">Forwarder Fax</label>

                                {!! Form::text('ff_fax', $customer->ff_fax, array("class"=>"form-control")) !!}

                            </div>

                        </div>


                        <div class="form-actions">

                            @if(has_role('customers_edit'))

                                <input type="submit" value="SAVE" class="btn btn-sm btn-success pull-right">

                            @endif

                        </div>

                    </div>

                    {!! Form::close() !!}

                </div>

            </div>

        </div>

        <!-- /Vertical Forms -->

    </div>



    <div class="row">

        <div class="col-md-12">

            <div class="widget box">

                <div class="widget-header">

                    <h4><i class="icon-reorder"></i> Customer Contacts</h4>

                    <div class="toolbar no-padding">

                        <div class="btn-group">

                            <span class="btn btn-xs"><a data-toggle="modal" href="#modal_add_contact" class="">Add Contact</a></span>

                        </div>

                    </div>

                </div>

                <div class="widget-content">

                    <table class="table table-hover">

                        <thead>

                        <tr>

                            <th>Name</th>

                            <th>E-Mail</th>

                            <th>Mobile</th>

                            <th>Skype</th>

                            <th>Position</th>

                            <th>System E-Mails</th>

                            <th>Can Login?</th>

                            <th class="align-right"></th>

                        </tr>

                        </thead>

                        <tbody>

                        @if(count($customer->contacts)>0)

                            @foreach($customer->contacts as $contact)

                                <tr>

                                    <td>{{$contact->contact_name}}</td>

                                    <td>{{$contact->username}}</td>

                                    <td>{{$contact->contact_mobile}}</td>

                                    <td>{{$contact->contact_skype}}</td>

                                    <td>{{$contact->position}}</td>

                                    <td>

                                        @if($contact->system_emails == 0)

                                            No E-Mails

                                        @elseif($contact->system_emails == 1)

                                            Newsletter only

                                        @elseif($contact->system_emails == 2)

                                            Order Status only

                                        @elseif($contact->system_emails == 3)

                                            Newsletter+Order Status

                                        @endif

                                    </td>

                                    <td>@if($contact->can_login == 1) Yes @else No @endif</td>

                                    <td class="align-right">

								<span class="btn-group">

									{!! Form::open(array('url' => "/customers/$customer->id/contacts/$contact->id", 'enctype' => 'multipart/form-data', 'id' => "contact-delete-$contact->id", 'method' => 'DELETE', 'class' => 'form-inline', 'autocomplete' => 'off')) !!}

                                    <a href="javascript:void(0);" class="btn btn-xs form-submit-conf"><i
                                                class="icon-trash"></i></a>

										<a href="/customers/{{$customer->id}}/contacts/{{$contact->id}}"
                                           class="btn btn-xs"><i class="icon-edit"></i></a>

                                    {!! Form::close() !!}

								</span>

                                    </td>

                                </tr>

                            @endforeach

                        @else

                            <tr>

                                <td colspan="7">Nothing found</td>

                            </tr>

                        @endif

                        </tbody>

                    </table>

                </div>

            </div>

            <!-- /Simple Table -->

        </div>

    </div>



    <div class="row">

        <div class="col-md-12">

            <div class="widget box">

                <div class="widget-header">

                    <h4><i class="icon-reorder"></i> Delivery Addresses (if different)</h4>

                    <div class="toolbar no-padding">

                        <div class="btn-group">

                            <span class="btn btn-xs"><a data-toggle="modal" href="#modal_add_address" class="">Add Address</a></span>

                        </div>

                    </div>

                </div>

                <div class="widget-content">

                    <table class="table table-hover">

                        <thead>

                        <tr>

                            <th>Description</th>

                            <th>Street</th>

                            <th>City</th>

                            <th>Zip</th>

                            <th>Provice</th>

                            <th>Country</th>

                            <th class="align-right"></th>

                        </tr>

                        </thead>

                        <tbody>

                        @if(count($customer->addresses)>0)

                            @foreach($customer->addresses as $address)

                                <tr>

                                    <td>{{$address->description}}</td>

                                    <td>

                                        {{$address->address1}}

                                        @if($address->address2!= "")

                                            <br/>{{$address->address2}}

                                        @endif

                                    </td>

                                    <td>{{$address->city}}</td>

                                    <td>{{$address->postal_code}}</td>

                                    <td>{{$address->province}}</td>

                                    <td>{{$address->country}}</td>

                                    <td class="align-right">

								<span class="btn-group">

									{!! Form::open(array('url' => "/customers/$customer->id/addresses/$address->id", 'enctype' => 'multipart/form-data', 'id' => "address-delete-$address->id", 'method' => 'DELETE', 'class' => 'form-inline', 'autocomplete' => 'off')) !!}

                                    <a href="javascript:void(0);" class="btn btn-xs form-submit-conf"><i
                                                class="icon-trash"></i></a>

										<a href="/customers/{{$customer->id}}/addresses/{{$address->id}}"
                                           class="btn btn-xs"><i class="icon-edit"></i></a>

                                    {!! Form::close() !!}

								</span>

                                    </td>

                                </tr>

                            @endforeach

                        @else

                            <tr>

                                <td colspan="7">Nothing found</td>

                            </tr>

                        @endif

                        </tbody>

                    </table>

                </div>

            </div>

            <!-- /Simple Table -->

        </div>

    </div>



    <!-- Modal dialog -->

    <div class="modal fade" id="modal_add_contact">

        <div class="modal-dialog">

            <div class="modal-content">

                <div class="modal-header">

                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>

                    <h4 class="modal-title">Add new Contact</h4>

                </div>

                {!! Form::open(array('url' => "/customers/$customer->id/contacts/add", 'enctype' => 'multipart/form-data', 'id' => "addresscustomer_contact", 'method' => 'POST', 'class' => 'form-validate1', 'autocomplete' => 'off')) !!}

                {{ Form::hidden('customer_id', $customer->id, array("class"=>"form-control")) }}

                <div class="modal-body">

                    <div class="form-group">

                        <div class="row">

                            <div class="col-md-3">

                                <label class="control-label">Contact Name</label>

                                {{ Form::text('contact_name', "", array("class"=>"form-control required")) }}

                            </div>

                            <div class="col-md-3">

                                <label class="control-label">E-Mail</label>

                                {{ Form::text('username', "", array("class"=>"form-control required")) }}

                            </div>

                            <div class="col-md-3">

                                <label class="control-label">Mobile</label>

                                {{ Form::text('contact_mobile', "", array("class"=>"form-control")) }}

                            </div>

                            <div class="col-md-3">

                                <label class="control-label">Skype</label>

                                {{ Form::text('contact_skype', "", array("class"=>"form-control")) }}

                            </div>

                        </div>

                        &nbsp;

                        <div class="row">

                            <div class="col-md-3">

                                <label class="control-label">Job Title</label>

                                {{ Form::text('position', "", array("class"=>"form-control")) }}

                            </div>

                            <div class="col-md-3">

                                <label class="control-label">Can Login</label>

                                {{ Form::select('can_login', array('0' => 'No','1' => 'Yes'), 1, array("class"=>"form-control")) }}

                            </div>

                            <div class="col-md-6">

                                <label class="control-label">System E-Mails</label>

                                {{ Form::select('system_emails', array('0' => 'No E-Mails','1' => 'Newsletter only','2' => 'Order Status only','3' => 'Newsletter+Order Status'), 3, array("class"=>"form-control")) }}

                            </div>

                        </div>

                    </div>

                </div>

                <div class="modal-footer">

                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>

                    <input type="submit" class="btn btn-primary" value="Submit">

                </div>

                {!! Form::close() !!}

            </div><!-- /.modal-content -->

        </div><!-- /.modal-dialog -->

    </div><!-- /.modal -->



    <!-- Modal dialog -->

    <div class="modal fade" id="modal_add_address">

        <div class="modal-dialog">

            <div class="modal-content">

                <div class="modal-header">

                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>

                    <h4 class="modal-title">Add new Delivery Address</h4>

                </div>

                {!! Form::open(array('url' => '/customers/'.$customer->id.'/addresses/add', 'enctype' => 'multipart/form-data', 'id' => 'customer_address', 'method' => 'post', 'class' => 'form-validate2')) !!}

                {{ Form::hidden('customer_id', $customer->id, array("class"=>"form-control")) }}

                <div class="modal-body">

                    <div class="form-group">

                        <div class="row">

                            <div class="col-md-4">

                                <label class="control-label">Description</label>

                                {{ Form::text('description', "", array("class"=>"form-control required")) }}

                            </div>

                            <div class="col-md-4">

                                <label class="control-label">Street 1</label>

                                {{ Form::text('address1', "", array("class"=>"form-control required")) }}

                            </div>

                            <div class="col-md-4">

                                <label class="control-label">City</label>

                                {{ Form::text('city', "", array("class"=>"form-control required")) }}

                            </div>

                        </div>

                        &nbsp;

                        <div class="row">

                            <div class="col-md-4">

                                <label class="control-label">Postal Code</label>

                                {{ Form::text('postal_code', "", array("class"=>"form-control")) }}

                            </div>

                            <div class="col-md-4">

                                <label class="control-label">Province</label>

                                {{ Form::text('province', "", array("class"=>"form-control")) }}

                            </div>

                            <div class="col-md-4">

                                <label class="control-label">Country</label>

                                {{ Form::text('country', "" , array("class"=>"form-control")) }}

                            </div>

                        </div>

                    </div>

                </div>

                <div class="modal-footer">

                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>

                    <input type="submit" class="btn btn-primary" value="Submit">

                </div>

                {!! Form::close() !!}

            </div><!-- /.modal-content -->

        </div><!-- /.modal-dialog -->

    </div><!-- /.modal -->



    <div class="row">

        <div class="col-md-12 no-padding">

            <p class="record_status">Created: {{$customer->created_at}} | Created by: {{$created_by_user}} |
                Updated: {{$customer->updated_at}} | Updated by: {{$updated_by_user}} | <a
                        href="/customers/changelog/{{ $customer->id }}">Changelog</a></p>

        </div>

    </div>





@stop
