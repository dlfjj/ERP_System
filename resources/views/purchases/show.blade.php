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

        <form class="form-inline" id="create" action="/purchases/create" method="POST">
        </form>

        <form class="form-inline" id="duplicate" action="/purchases/duplicate/{{$purchase->id}}" method="POST">
            {{ Form::hidden('id', $purchase->id, array("class"=>"", 'readonly')) }}
        </form>

        <form class="form-inline" id="post" action="/purchases/post/{{$purchase->id}}" method="POST">
            {{ Form::hidden('id', $purchase->id, array("class"=>"", 'readonly')) }}
        </form>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="tab-content" id="myTabContent">
                <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">...</div>
                <div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">...</div>
                <div class="tab-pane fade" id="contact" role="tabpanel" aria-labelledby="contact-tab">...</div>
            </div>

            <div class="widget box">
                <div class="widget-header">
                    <h4><i class="icon-reorder"></i> Purchase Details</h4>
                </div>
                {{--<div id="app">--}}
                    {{--<test></test>--}}
                {{--</div>--}}

                <div class="widget-content">
                    {{--<form enctype="multipart/form-data" id="main" class="form-vertical row-border form-validate" action="" method="POST">--}}
                    {!! Form::open(['method'=>'PUT', 'action'=> ['PurchaseController@update', $purchase->id], 'class'=>'form-vertical row-border form-validate','id'=>'main','enctype'=>'multipart/form-data']) !!}
                    {{--<div class="tabbable box-tabs">--}}
                    <div class="tabbable">
                            <ul class="nav nav-tabs">
                                <li class="active"><a href="#box_tab1" data-toggle="tab">General</a></li>
                                <li><a href="#box_tab2" data-toggle="tab">Handling Fees</a></li>
                                <li><a href="#box_tab3" data-toggle="tab">Remarks / Deliver To</a></li>
                            </ul>
                            <div class="tab-content">
                                <div class="tab-pane active" id="box_tab1">
                                    <div class="form-group">
                                        <div class="row">
                                            <div class="col-md-2">
                                                <label class="control-label">P.O ID</label>
                                                {{ Form::text('id', $purchase->id, array("class"=>"form-control", 'readonly')) }}
                                            </div>
                                            <div class="col-md-2">
                                                <label class="control-label"><a href="/vendors/{{$vendor->id}}">Vendor</a></label>
                                                {{ Form::text('', $vendor->company_name, array("class"=>"form-control", 'readonly')) }}
                                            </div>
                                            <div class="col-md-2">
                                                <label class="control-label">Vendor Contact</label>
                                                {{ Form::select('vendor_contact', $select_vendor_contacts, $purchase->vendor_contact, array("class"=>"form-control")) }}
                                            </div>
                                            <div class="col-md-2">
                                                <label class="control-label">Currency</label>
                                                {{ Form::select('currency_code', $select_currency_codes, $purchase->currency_code, array("class"=>"form-control")) }}
                                            </div>
                                            <div class="col-md-2">
                                                {{ Form::label('user_id', "Assigned User") }}
                                                {{ Form::select('user_id', $select_users, $purchase->user_id, array("class"=>"form-control")) }}
                                            </div>
                                            <div class="col-md-2">
                                                <label class="control-label">Payment Terms</label>
                                                {{ Form::select('payment_terms', $select_payment_terms, $purchase->payment_terms, array("class"=>"form-control")) }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="row">
                                            <div class="col-md-2">
                                                <label class="control-label">Date placed</label>
                                                {{ Form::text('date_placed', $purchase->date_placed, array("class"=>"form-control datepicker")) }}
                                            </div>
                                            <div class="col-md-2">
                                                <label class="control-label">Date required</label>
                                                {{ Form::text('date_required', $purchase->date_required, array("class"=>"form-control datepicker")) }}
                                            </div>
                                            <div class="col-md-2">
                                                <label class="control-label">Date confirmed</label>
                                                {{ Form::text('date_confirmed', $purchase->date_confirmed, array("class"=>"form-control datepicker")) }}
                                            </div>
                                            <div class="col-md-2">
                                                <label class="control-label">Tax Code</label>
                                                {{ Form::select('taxcode_id', $select_taxcodes, $purchase->taxcode_id, array("class"=>"form-control")) }}
                                            </div>
                                        </div>
                                        <div class="form-actions">
                                            @if(has_role('purchases_edit'))
                                                <input type="submit" value="Save" class="btn btn-sm btn-success pull-right">
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane" id="box_tab2">
                                    <div class="form-group">
                                        <div class="row">
                                            <div class="col-md-2">
                                                <label class="control-label">Extra Label 1</label>
                                                {{ Form::text('shipping_amount_label', $purchase->shipping_amount_label, array("class"=>"form-control")) }}
                                            </div>
                                            <div class="col-md-2">
                                                <label class="control-label">Amount</label>
                                                {{ Form::text('gross_shipping_amount', $purchase->gross_shipping_amount, array("class"=>"form-control")) }}
                                            </div>
                                            <div class="col-md-2">
                                                <label class="control-label">Extra Label 2</label>
                                                {{ Form::text('handling_amount_label', $purchase->handling_amount_label, array("class"=>"form-control")) }}
                                            </div>
                                            <div class="col-md-2">
                                                <label class="control-label">Amount</label>
                                                {{ Form::text('gross_handling_amount', $purchase->gross_handling_amount, array("class"=>"form-control")) }}
                                            </div>
                                            <div class="col-md-2">
                                                <label class="control-label">% Discount</label>
                                                {{ Form::text('discount', $purchase->discount, array("class"=>"form-control")) }}
                                            </div>
                                        </div>
                                        <div class="form-actions">
                                            @if(has_role('purchases_edit'))
                                                <input type="submit" value="Save" class="btn btn-sm btn-success pull-right">
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane" id="box_tab3">
                                    <div class="form-group">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <label class="control-label">Remarks Public</label>
                                                {{ Form::textarea('remarks_public', $purchase->remarks_public, array("rows"=>"2","cols"=>"5","class"=>"form-control")) }}
                                                <span class="help-block">Will appear on Printouts</span>
                                            </div>
                                            <div class="col-md-4">
                                                <label class="control-label">Remarks Private</label>
                                                {{ Form::textarea('remarks_private', $purchase->remarks_private, array("rows"=>"2","cols"=>"5","class"=>"form-control")) }}
                                                <span class="help-block">Will not be visible to the vendor</span>
                                            </div>
                                            <div class="col-md-4">
                                                <label class="control-label">Deliver To</label>
                                                {{ Form::textarea('ship_to', $purchase->ship_to, array("rows"=>"2","cols"=>"5","class"=>"form-control")) }}
                                                <span class="help-block">Leave empty to use Default</span>
                                            </div>
                                        </div>
                                        <div class="form-actions">
                                            @if(has_role('purchases_edit'))
                                                <input type="submit" value="Save" class="btn btn-sm btn-success pull-right">
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    {{--</form>--}}
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>

    <!--=== Box Tabs ===-->
    <div class="row">
        <div class="col-md-12">
            <div class="widget box">
                <div class="widget-header">
                    <h4><i class="icon-reorder"></i> Purchase Items</h4>
                </div>
                <div class="widget-content">
                    <div class="tabbable box-tabs">
                        <ul class="nav nav-tabs">
                            <li class="active"><a href="#box_tab1" data-toggle="tab">Purchase Items</a></li>
                        </ul>
                        <div class="tab-content">

                            <div class="tab-pane active" id="box_tab1">
                                <table class="table table-hover">
                                    <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>PART #</th>
                                        <th class="cell-tight align-right">STOCK</th>
                                        <th class="cell-tight align-right">RECEIVED</th>
                                        <th class="cell-tight align-right">OPEN</th>
                                        <th class="cell-tight align-right" style="background: #EEE;">ORDERED</th>
                                        <th class="cell-tight align-right">GROSS</th>
                                        <th class="cell-tight align-right">AMOUNT</th>
                                        <th class="align-right">
                                            @if(has_role('purchases_edit'))
                                                <a href="/purchases/line-item-add/{{$purchase->id}}" class="btn btn-xs"><i class="icon-plus-sign"></i></a>
                                            @endif
                                        </th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @if(count($purchase->items)>0)
                                        <?php
                                        $purchase_items = $purchase->items->sortBy('sort_no');
                                        ?>
                                        @foreach($purchase_items as $oi)
                                            <tr class="order-form-row">
                                                <td>
                                                    {{$oi->sort_no}}
                                                </td>
                                                <td>
                                                    <a href="/products/show/{{ $oi->product->id }}">{{ $oi->product->product_code }}</a>
                                                </td>
                                                <td class="align-right">{{ $oi->product->getStockOnHand() }}</td>
                                                <td class="align-right">{{ $oi->getQuantityDelivered() }}</td>
                                                <td class="align-right">
                                                    {{ $oi->quantity_open }}
                                                </td>
                                                <td class="align-right" style="background: #EEE;">
                                                    @if($oi->quantity % $oi->product->pack_unit == 0)
                                                        {{$oi->quantity}}
                                                    @else
                                                        {{$oi->quantity}} (PU!)
                                                    @endif
                                                </td>
                                                <td class="align-right">{{ number_format($oi->gross_price,3) }}</td>
                                                <td class="align-right">{{ number_format($oi->gross_total,3) }}</td>
                                                <td class="align-right" style="width: 100px;">
												<span class="btn-group">
													@if(has_role('purchases_edit'))
                                                        <a href="/purchases/line-item-delete/{{$oi->id}}" class="btn btn-xs"><i class="icon-trash"></i></a>
                                                        <a href="/purchases/line-item-update/{{$oi->id}}" class="btn btn-xs"><i class="icon-edit"></i></a>
                                                    @else
                                                        -
                                                    @endif
												</span>
                                                </td>
                                            </tr>
                                            <tr class="order-form-row">
                                                <td></td>
                                                <td colspan="8">
                                                    {{ nl2br(wordwrap($oi->product->product_name,80)) }}
                                                    @if($oi->remarks != "")
                                                        <br />
                                                        {{ nl2br($oi->remarks) }}
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                        <tr>
                                            <td colspan="9">{{ count($purchase->items) }} Line Items Total</td>
                                        </tr>
                                        <tr>
                                            <td colspan="7" class="align-right">Sub Total Net</td>
                                            <td class="align-right">{{ $purchase->net_sub_total }}</td>
                                            <td></td>
                                        </tr>
                                        @if($purchase->net_shipping_amount > 0)
                                            <tr>
                                                <td colspan="7" class="align-right">+ {{ $purchase->shipping_amount_label }}</td>
                                                <td class="align-right">{{ number_format($purchase->net_shipping_amount,2) }}</td>
                                                <td></td>
                                            </tr>
                                        @endif
                                        @if($purchase->net_handling_amount > 0)
                                            <tr>
                                                <td colspan="7" class="align-right">+ {{ $purchase->handling_amount_label }}</td>
                                                <td class="align-right">{{ number_format($purchase->net_handling_amount,2) }}</td>
                                                <td></td>
                                            </tr>
                                        @endif
                                        @if($purchase->taxcode_percent > 0)
                                            <tr>
                                                <td colspan="7" class="align-right">{{ $purchase->taxcode_name }}</td>
                                                <td class="align-right">{{ $purchase->tax_total }}</td>
                                                <td></td>
                                            </tr>
                                        @endif
                                        <tr>
                                            <td colspan="7" class="align-right">Total {{ $purchase->currency_code }}</td>
                                            <td class="align-right">{{ $purchase->gross_total }}</td>
                                            <td></td>
                                        </tr>
                                    @else
                                        <tr>
                                            <td colspan="9">Nothing found</td>
                                        </tr>
                                    @endif
                                    </tbody>
                                </table>
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
            {{--<p class="record_status">Created: {{$purchase->created_at}} | Created by: {{User::find($purchase->created_by)->username}} | Updated: {{$purchase->updated_at}} | Updated by: {{User::find($purchase->updated_by)->username}} | <a href="/purchases/changelog/{{ $purchase->id }}">Changelog</a></p>--}}
        </div>
    </div>
@stop
