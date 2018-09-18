@extends('layouts.default')

@section('page-module-menu')

    @include('vendors.top_menu')

@stop

@section('page-crumbs')

    @include('vendors.bread_crumbs')

@stop


@section('page-header')

    @include('vendors.page_header')

@stop

@section('content')

    <div class="row">


        {!! Form::open(array("url"=>"/vendors/destroy/$vendor->id","method"=>"post","class"=>"form-inline","id"=>"delete")) !!}

        </form>


        <form class="form-inline" id="create" action="/vendors/create" method="POST">

        </form>


        <form class="form-inline" id="duplicate" action="/vendors/duplicate/{{$vendor->id}}" method="POST">

        </form>


        <!--=== Vertical Forms ===-->

        <div class="col-md-12">

            <div class="widget box">

                <div class="widget-header">

                    <h4><i class="icon-reorder"></i> Vendor Details</h4>

                </div>

                <div class="widget-content">

                    <form autocomplete="off" enctype="multipart/form-data" id="main"
                          class="form-vertical row-border form-validate" action="/vendors/{{$vendor->id}}"
                          method="POST">
                        {{csrf_field()}}

                        <div class="form-group">

                            <div class="row">

                                <div class="col-md-5">

                                    <label class="control-label">Company Name</label>

                                    {!! Form::text('company_name', $vendor->company_name, array("class"=>"form-control required")) !!}

                                </div>

                                <div class="col-md-3">

                                    <label class="control-label">Localized Name</label>

                                    {!!Form::text('company_name_localized', $vendor->company_name_localized, array("class"=>"form-control")) !!}

                                </div>

                                <div class="col-md-2">

                                </div>

                                <div class="col-md-2">

                                    <label class="control-label">Status</label>

                                    {!! Form::select('status', array('ACTIVE' => 'ACTIVE','Prospect' => 'Prospect', 'INACTIVE' => 'INACTIVE'), $vendor->status, array("class"=>"form-control")) !!}

                                </div>

                            </div>

                        </div>

                        <div class="form-group">

                            <div class="row">

                                <div class="col-md-2">

                                    <label class="control-label">Telephone 1</label>

                                    {!!Form::text('telephone_1', $vendor->telephone_1, array("class"=>"form-control")) !!}

                                </div>

                                <div class="col-md-3">

                                    <label class="control-label">Telephone 2</label>

                                    {!! Form::text('telephone_2', $vendor->telephone_2, array("class"=>"form-control")) !!}

                                </div>

                                <div class="col-md-3">

                                    <label class="control-label">Fax</label>
                                    {!! Form::text('fax', $vendor->fax, array("class"=>"form-control")) !!}

                                </div>

                                <div class="col-md-2">

                                    <label class="control-label">E-Mail</label>

                                    {!! Form::text('email', $vendor->email, array("class"=>"form-control")) !!}

                                </div>

                                <div class="col-md-2">

                                    <label class="control-label">Website URL</label>

                                    {!! Form::text('url', $vendor->url, array("class"=>"form-control")) !!}

                                </div>

                            </div>

                        </div>

                        <div class="form-group">

                            <div class="row">

                                <div class="col-md-3">

                                    <label class="control-label">Street Line 1 &amp; 2 &amp; Local</label>

                                    {!! Form::text('street_1', $vendor->street_1, array("class"=>"form-control")) !!}

                                    &nbsp;

                                    {!! Form::text('street_2', $vendor->street_2, array("class"=>"form-control")) !!}

                                    &nbsp;

                                    {!! Form::text('local_address', $vendor->local_address, array("class"=>"form-control")) !!}

                                </div>

                                <div class="col-md-2">

                                    <label class="control-label">City</label>

                                    {!! Form::text('city', $vendor->city, array("class"=>"form-control")) !!}

                                </div>

                                <div class="col-md-3">

                                    <label class="control-label">Postal Code</label>

                                    {!! Form::text('postal_code', $vendor->postal_code, array("class"=>"form-control")) !!}

                                </div>

                                <div class="col-md-2">

                                    <label class="control-label">Province</label>

                                    {!! Form::text('province', $vendor->province, array("class"=>"form-control")) !!}

                                </div>

                                <div class="col-md-2">

                                    <label class="control-label">Country</label>

                                    {!! Form::text('country', $vendor->country, array("class"=>"form-control")) !!}

                                </div>

                            </div>

                        </div>

                        <div class="form-group">

                            <div class="row">

                                <div class="col-md-2">

                                    <label class="control-label">Currency</label>

                                    {!! Form::select('currency_code', $select_currency_codes, (isset($vendor->currency_code) ? $vendor->currency_code: ""), array("class"=>"form-control")) !!}

                                </div>

                                <div class="col-md-3">

                                    <label class="control-label">Payment Terms</label>

                                    {!! Form::select('payment_terms', $select_payment_terms , $vendor->payment_terms, array("class"=>"form-control")) !!}

                                </div>

                                <div class="col-md-2">

                                    <label class="control-label">Credit (d)</label>

                                    {!! Form::text('credit_days', $vendor->credit_days, array("class"=>"form-control")) !!}

                                </div>

                                <div class="col-md-3">

                                    <label class="control-label">Bank Details</label>

                                    {!! Form::textarea('bank_details', $vendor->bank_details, array("rows"=>"3","cols"=>"5","class"=>"form-control")) !!}

                                </div>

                                <div class="col-md-2">

                                    <label class="control-label">Tax Code</label>

                                    {!! Form::select('taxcode_id', $select_taxcodes, $vendor->taxcode_id, array("class"=>"form-control")) !!}

                                </div>

                            </div>

                        </div>

                        <div class="form-group">

                            <div class="row">

                                <div class="col-md-4">

                                    <label class="control-label">Remarks</label>

                                    {!! Form::textarea('remarks', $vendor->remarks, array("rows"=>"3","cols"=>"5","class"=>"form-control")) !!}

                                </div>

                                <div class="col-md-2">

                                    <label class="control-label">Introduced via</label>

                                    {!! Form::text('introduced_via', $vendor->introduced_via, array("class"=>"form-control")) !!}

                                </div>

                            </div>

                            <div class="form-actions">

                                @if(has_role('vendors_edit'))

                                    <input type="submit" value="Save" class="btn btn-sm btn-success pull-right">

                                @endif

                            </div>

                        </div>


                    </form>

                </div>

            </div>

        </div>

        <!-- /Vertical Forms -->

    </div>



    <div class="row">

        <div class="col-md-12">

            <div class="widget box">

                <div class="widget-header">

                    <h4><i class="icon-reorder"></i> Vendor Contacts</h4>

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

                            <th>Job Title</th>

                            <th class="align-right"></th>

                        </tr>

                        </thead>

                        <tbody>

                        @if(count($vendor->contacts)>0)

                            @foreach($vendor->contacts as $contact)

                                <tr>

                                    <td>{{$contact->name}}</td>

                                    <td>{{$contact->email}}</td>

                                    <td>{{$contact->mobile }}</td>

                                    <td>{{$contact->skype}}</td>

                                    <td>{{$contact->position}}</td>

                                    <td class="align-right">

								<span class="btn-group">

									{!! Form::open(array("url"=>"/vendors/".$vendor->id."/contacts/$contact->id","method"=>"DELETE","class"=>"form-inline", "id"=>"delete-contact-$contact->id")) !!}

                                    <a href="javascript:void(0);" class="btn btn-xs form-submit-conf"><i
                                                class="icon-trash"></i></a>

										<a href="/vendors/{{$vendor->id}}/contacts/{{$contact->id}}" class="btn btn-xs"><i
                                                    class="icon-edit"></i></a>

                                    </form>

								</span>

                                    </td>

                                </tr>

                            @endforeach

                        @else

                            <tr>

                                <td colspan="6">Nothing found</td>

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

                <form enctype="multipart/form-data" id="vendor_contact" class="form-validate1"
                      action="/vendors/{{$vendor->id}}/contacts" method="POST" autocomplete="off">
                    {{csrf_field()}}
                    {!! Form::hidden('vendor_id', $vendor->id, array("class"=>"form-control")) !!}

                    <div class="modal-body">

                        <div class="form-group">

                            <div class="row">

                                <div class="col-md-3">

                                    <label class="control-label">Contact Name</label>

                                    {!! Form::text('name', "", array("class"=>"form-control required")) !!}

                                </div>

                                <div class="col-md-3">

                                    <label class="control-label">E-Mail</label>

                                    {!! Form::text('email', "", array("class"=>"form-control required")) !!}

                                </div>

                                <div class="col-md-3">

                                    <label class="control-label">Mobile</label>

                                    {!! Form::text('mobile', "", array("class"=>"form-control")) !!}

                                </div>

                                <div class="col-md-3">

                                    <label class="control-label">Skype</label>

                                    {!! Form::text('skype', "", array("class"=>"form-control")) !!}

                                </div>

                            </div>

                            &nbsp;

                            <div class="row">

                                <div class="col-md-3">

                                    <label class="control-label">Job Title</label>

                                    {!! Form::text('position', "", array("class"=>"form-control")) !!}

                                </div>

                            </div>

                        </div>

                    </div>

                    <div class="modal-footer">

                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>

                        <input type="submit" class="btn btn-primary" value="Submit">

                    </div>

                </form>

            </div><!-- /.modal-content -->

        </div><!-- /.modal-dialog -->

    </div><!-- /.modal -->



    <div class="row">

        <div class="col-md-12 no-padding">

            <p class="record_status">Created: {{$vendor->created_at}} | Created by: {{$created_by_user}} |
                Updated: {{$vendor->updated_at}} | Updated by: {{$updated_by_user}} | <a
                        href="/vendors/changelog/{{ $vendor->id }}">Changelog</a></p>

        </div>

    </div>





@stop

