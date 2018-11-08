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

    {{--customer order detail form--}}
    <div class="row">
        {{--{{ Form::open(array("url"=>"/orders/destroy/$order->id","method"=>"post","class"=>"form-inline","id"=>"delete")) }}--}}
        {{--</form>--}}

        {{--<form class="form-inline" id="create" action="/orders/create" method="POST">--}}
        {{--</form>--}}

        {{--<form class="form-inline" id="create_invoice" action="/invoices/create" method="POST">--}}
        {{--{{ Form::hidden('order_id', $order->id, array("class"=>"", 'readonly')) }}--}}
        {{--{{ Form::hidden('from_order', 1, array("class"=>"", 'readonly')) }}--}}
        {{--</form>--}}

        {{--<form class="form-inline" id="print_pdf" action="/orders/{{$order->id}}/print_pdf" method="POST">--}}
        {{--</form>--}}

        {{--<form class="form-inline" id="post" action="/orders/post/{{$order->id}}" method="POST">--}}
        {{--{{ Form::hidden('id', $order->id, array("class"=>"", 'readonly')) }}--}}
        {{--</form>--}}


        {{--customer order details section--}}
        <div class="col-md-12">
            <div class="widget box">
                <div class="widget-header">
                    <h4><i class="icon-reorder"></i> Customer Order Details</h4>
                </div>
                <div class="widget-content">
                    {!! Form::open(['method'=>'PATCH', 'action'=> ['OrderController@update', $order->id], 'class'=>'form-vertical row-border form-validate','id'=>'main','enctype'=>'multipart/form-data','autocomplete'=>'off']) !!}
                    <div class="tabbable">
                        <ul class="nav nav-tabs">
                            <li class="active"><a href="#box_tab1" data-toggle="tab">General</a></li>
                            <li><a href="#box_tab2" data-toggle="tab">Terms</a></li>
                            <li><a href="#box_tab3" data-toggle="tab">Shipping</a></li>
                            <li><a href="#box_tab4" data-toggle="tab">Remarks</a></li>
                        </ul>
                        <div class="tab-content">
                            <div class="tab-pane" id="box_tab2">
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-2">
                                            <label class="control-label">Currency Code</label>
                                            {{ Form::select('currency_code', $select_currency_codes, $order->currency_code, array("class"=>"form-control")) }}
                                        </div>
                                        <div class="col-md-2">
                                            <label class="control-label">Payment Terms</label>
                                            {{ Form::select('payment_term_id', $select_payment_terms, $order->payment_term_id, array("class"=>"form-control")) }}
                                        </div>
                                        <div class="col-md-2">
                                            <label class="control-label">Tax Code</label>
                                            {{ Form::select('taxcode_id', $select_taxcodes, $order->taxcode_id, array("class"=>"form-control")) }}
                                        </div>
                                        <div class="col-md-2">
                                            <label class="control-label">Discount %</label>
                                            {{ Form::text('discount', $order->discount, array("class"=>"form-control")) }}
                                        </div>
                                        <div class="col-md-2">
                                            <label class="control-label">Freight Charge</label>
                                            {{ Form::text('shipping_cost', $order->shipping_cost, array("class"=>"form-control")) }}
                                        </div>
                                        <div class="col-md-2">
                                            <label class="control-label">Freight Cost</label>
                                            {{ Form::text('shipping_cost_actual', $order->shipping_cost_actual, array("class"=>"form-control")) }}
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    @php
                                        $commission_ro = "readonly";
                                        if(has_role('admin') || has_role('company_admin')){
                                            $commission_ro = "";
                                        }
                                    @endphp
                                    <div class="row">
                                        <div class="col-md-2">
                                            <label class="control-label">Commission</label>
                                            {{ Form::text('commission', $order->commission, array("class"=>"form-control",$commission_ro)) }}
                                        </div>
                                        <div class="col-md-2">
                                            <label class="control-label">Commission Remarks</label>
                                            {{ Form::text('commission_remarks', $order->commission_remarks, array("class"=>"form-control",$commission_ro)) }}
                                        </div>
                                        <div class="col-md-2">
                                            <label class="control-label">Due Date</label>
                                            {{ Form::text('', $order->getDueDate(), array("class"=>"form-control","readonly")) }}
                                        </div>
                                        <div class="col-md-2">
                                            <label class="control-label">Due Date override</label>
                                            {{ Form::text('due_date_override', $order->due_date_override, array("class"=>"form-control datepicker")) }}
                                        </div>
                                        <div class="col-md-2">
                                            <label class="control-label">Override Remark</label>
                                            {{ Form::text('due_date_override_remark', $order->due_date_override_remark, array("class"=>"form-control")) }}
                                        </div>
                                        <div class="col-md-2">
                                        </div>
                                    </div>
                                    <div class="form-actions">
                                        <input type="submit" value="SAVE" class="btn btn-sm btn-success pull-right">
                                        {{--<a href="/orders/{{$order->id}}" class="btn btn-sm btn-default pull-right">Cancel</a>--}}
                                        {{ Form::button('RESET', ['type' => 'reset', 'class' => 'btn btn-default pull-right'] )  }}

                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane" id="box_tab4">
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <label class="control-label">Remarks Public</label>
                                            {{ Form::textarea('order_remarks_public', $order->order_remarks_public, array("rows"=>"2","cols"=>"5","class"=>"form-control")) }}
                                            <span class="help-block">Will appear on Printouts</span>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="control-label">Remarks Private</label>
                                            {{ Form::textarea('order_remarks', $order->order_remarks, array("rows"=>"2","cols"=>"5","class"=>"form-control")) }}
                                            <span class="help-block">Will not be visible to the Customer</span>
                                        </div>
                                    </div>
                                    <div class="form-actions">
                                        <input type="submit" value="SAVE" class="btn btn-sm btn-success pull-right">
                                        {{--<a href="/orders/{{$order->id}}" class="btn btn-sm btn-default pull-right">Cancel</a>--}}
                                        {{ Form::button('RESET', ['type' => 'reset', 'class' => 'btn btn-default pull-right'] )  }}

                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane" id="box_tab3">
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <label class="control-label">Delivery Address (<a href="/orders/change-address/{{ $order->id }}">Change</a>)</label>
                                            {{ Form::textarea('delivery_address', $order->delivery_address, array("rows"=>"6","cols"=>"5","class"=>"form-control")) }}
                                        </div>
                                        <div class="col-md-2">
                                        </div>
                                        <div class="col-md-2">
                                        </div>
                                        <div class="col-md-3">
                                            <label class="control-label">Shipping Terms</label>
                                            {{ Form::select('shipping_term_id', $select_shipping_terms, $order->shipping_term_id, array("class"=>"form-control")) }}
                                        </div>
                                        <div class="col-md-2">
                                            <label class="control-label">Container</label>
                                            {{ Form::select('container_type', $select_containers, $order->container->id, array("class"=>"form-control")) }}
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-2">
                                            <label class="control-label">Container Number</label>
                                            {{ Form::text('container_number', $order->container_number, array("class"=>"form-control")) }}
                                        </div>
                                        <div class="col-md-2">
                                            <label class="control-label">Vessel ETD</label>
                                            {{ Form::text('vessel_etd', $order->vessel_etd, array("class"=>"form-control datepicker")) }}
                                        </div>
                                        <div class="col-md-2">
                                            <label class="control-label">Vessel ETA</label>
                                            {{ Form::text('vessel_eta', $order->vessel_eta, array("class"=>"form-control datepicker")) }}
                                        </div>
                                        <div class="col-md-2">
                                            <label class="control-label">Shipping Method</label>
                                            {{ Form::select('shipping_method', $select_shipping_methods, $order->shipping_method, array("class"=>"form-control")) }}
                                        </div>
                                        <div class="col-md-2">
                                            <label class="control-label">Tracking No.</label>
                                            {{ Form::text('tracking_number', $order->tracking_number, array("class"=>"form-control ")) }}
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-2">
                                            <label class="control-label">Real Nt Weight</label>
                                            {{ Form::text('net_weight', $order->net_weight, array("class"=>"form-control")) }}
                                        </div>
                                        <div class="col-md-2">
                                            <label class="control-label">Real Gr Weight</label>
                                            {{ Form::text('gross_weight', $order->gross_weight, array("class"=>"form-control")) }}
                                        </div>
                                        <div class="col-md-2">
                                            <label class="control-label">Real CBM</label>
                                            {{ Form::text('real_cbm', $order->real_cbm, array("class"=>"form-control")) }}
                                        </div>
                                        <div class="col-md-2">
                                            <label class="control-label">No. of Pallets</label>
                                            {{ Form::text('number_of_pallettes', $order->number_of_pallettes, array("class"=>"form-control")) }}
                                        </div>
                                        <div class="col-md-2">
                                            <label class="control-label">Weight of Pallets</label>
                                            {{ Form::text('weight_of_pallets', $order->weight_of_pallets, array("class"=>"form-control")) }}
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <label class="control-label">Forwarder Name</label>
                                            {{ Form::text('ff_name', $order->ff_name, array("class"=>"form-control")) }}
                                        </div>
                                        <div class="col-md-3">
                                            <label class="control-label">Forwarder Contact</label>
                                            {{ Form::text('ff_contact', $order->ff_contact, array("class"=>"form-control")) }}
                                        </div>
                                        <div class="col-md-2">
                                            <label class="control-label">Forwarder E-Mail</label>
                                            {{ Form::text('ff_email', $order->ff_email, array("class"=>"form-control")) }}
                                        </div>
                                        <div class="col-md-2">
                                            <label class="control-label">Forwarder Phone</label>
                                            {{ Form::text('ff_phone', $order->ff_phone, array("class"=>"form-control")) }}
                                        </div>
                                        <div class="col-md-2">
                                            <label class="control-label">Forwarder Fax</label>
                                            {{ Form::text('ff_fax', $order->ff_fax, array("class"=>"form-control")) }}
                                        </div>
                                    </div>
                                    <div class="form-actions">
                                        <input type="submit" value="SAVE" class="btn btn-sm btn-success pull-right">
                                        {{--<a href="/orders/{{$order->id}}" class="btn btn-sm btn-default pull-right">Cancel</a>--}}
                                        {{ Form::button('RESET', ['type' => 'reset', 'class' => 'btn btn-default pull-right'] )  }}

                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane active" id="box_tab1">
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-2">
                                            <label class="control-label">Order ID</label>
                                            {{ Form::hidden('id', $order->id, array("class"=>"form-control")) }}
                                            {{ Form::text('', $order->order_no, array("class"=>"form-control", 'readonly')) }}
                                        </div>
                                        <div class="col-md-2">
                                            <label class="control-label">Order Rev.</label>
                                            {{ Form::text('order_rev', $order->order_rev, array("class"=>"form-control")) }}
                                        </div>
                                        <div class="col-md-2">
                                            <label class="control-label">Customer (<a href="/customers/{{ $customer->id }}">View</a>)</label>
                                            {{ Form::text('', $customer->customer_name, array("class"=>"form-control", 'readonly')) }}
                                        </div>
                                        <div class="col-md-2">
                                            <label class="control-label">Customer Contact</label>
                                            {{ Form::select('customer_contact_id', $select_customer_contacts, $order->customer_contact_id, array("class"=>"form-control")) }}
                                        </div>
                                        <div class="col-md-2">
                                        </div>
                                        <div class="col-md-2">
                                            <label class="control-label">C.O.N</label>
                                            {{ Form::text('customer_order_number', $order->customer_order_number, array("class"=>"form-control")) }}
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-2">
                                            <label class="control-label">Order Date</label>
                                            {{ Form::text('order_date', $order->order_date, array("class"=>"form-control datepicker")) }}
                                        </div>
                                        <div class="col-md-2">
                                            <label class="control-label">Est. Finish Date</label>
                                            {{ Form::text('estimated_finish_date', $order->estimated_finish_date, array("class"=>"form-control datepicker")) }}
                                        </div>
                                    </div>
                                    <div class="form-actions">
                                        {!! Form::submit('Save',['class'=>'btn btn-sm btn-success pull-right']) !!}
                                        {{--<a href="/orders/{{$order->id}}" class="btn btn-sm btn-default pull-right">Cancel</a>--}}
                                        {{ Form::button('RESET', ['type' => 'reset', 'class' => 'btn btn-default pull-right'] )  }}

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>

    {{--order items--}}
    <div class="row">
        <div class="col-md-12">
            <div class="widget box">
                <div class="widget-header">
                    <h4><i class="icon-reorder"></i> Order Items</h4>
                </div>
                <div class="widget-content">
                    <div class="tabbable">
                        <ul class="nav nav-tabs">
                            @if(has_role('orders_see_profits'))
                                <li class="active"><a href="#box_tab11" data-toggle="tab">Line Items</a></li>
                            @endif
                            <li class=""><a href="#box_tab12" data-toggle="tab">Profits</a></li>
                        </ul>


                        <div class="tab-content">
                            {{--Profits tab--}}
                            @if(has_role('orders_see_profits'))
                                <div class="tab-pane" id="box_tab12">
                                    <table cellpadding="0" cellspacing="0" border="0" class="table table-hover order-profits">
                                        <tr>
                                            <td>Total Costs</td>
                                            <td>$ <?=number_format($total_cost,2);?></td>
                                        </tr>
                                        <tr>
                                            <td>Total Sales</td>
                                            <td>$ <?=number_format($total_sales,2);?></td>
                                        </tr>
                                        <tr>
                                            <td>Commissions (<?=$commission_percent;?>%)</td>
                                            <td>$ <?=number_format($commission,2);?></td>
                                        </tr>
                                        <tr>
                                            <td>Profit</td>
                                            <td style="font-size: 20px;">$ <?=number_format($profit,2);?></td>
                                        </tr>
                                        <tr>
                                            <td>Profit in %</td>
                                            <td style="font-size: 20px;">% <?=number_format($profit_percent,2);?></td>
                                        </tr>
                                    </table>
                                </div>
                            @endif

                            {{--line item tab--}}
                            <div class="tab-pane active" id="box_tab11">
                                {!! Form::open(['method' => 'PATCH','action' => ['OrderController@update',$order->id], 'class'=>'table_form']) !!}
                                <table class="table table-hover table-striped">
                                    <thead>
                                    <tr>
                                        <th class="cell-tight">#</th>
                                        <th>Product #</th>
                                        <th class="cell-tight">P/U</th>
                                        <th class="cell-tight">CBM</th>
                                        <th class="cell-tight">Per Pallet</th>
                                        <th class="cell-tight">Stock</th>
                                        <th class="cell-tight">Qty</th>
                                        <th class="cell-tight">Nt. Price</th>
                                        <th class="cell-tight">Nt. Amount</th>
                                        <th class="align-right" style="width: 90px;">
                                            <a href="/orders/line_item_add/{{$order->id}}" class="btn"><i class="icon-plus-sign"></i></a>
                                        </th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @if(count($order->items)>0)
                                        @php
                                            $order_items = $order->items;
                                        @endphp
                                        @foreach($order_items as $oi)
                                            <tr class="order-form-row">
                                                <td>
                                                    {{ $oi->line_no }}
                                                </td>
                                                <td>
                                                    <a href="/products/{{$oi->product->pluck('id')->implode(',')}}">{{$oi->product->pluck('product_code')->implode(',') }}</a>
                                                    <table class="table_in_table">
                                                        <tr>
                                                            <td>Product</td>
                                                            <td>{{ nl2br($oi->product->pluck('product_name')->implode(',') ) }}</td>
                                                        </tr>
                                                        <tr>
                                                            <td>Remarks</td>
                                                            <td><textarea name="oi[{{$oi->id}}][remark]" style="height: 40px; background: none; width: 100%; padding: 3px !important; border: 1px solid #CCC;">{{ $oi->remark }}</textarea>
                                                        </tr>
                                                        <tr>
                                                            <td>CSC Code</td>
                                                            <td><input name="oi[{{$oi->id}}][commodity_code]" style="background: none; width: 100%; padding: 3px !important; border: 1px solid #CCC;" type="text" value="{{ $oi->commodity_code }}" /></td>
                                                        </tr>
                                                    </table>
                                                </td>
                                                <td>
                                                    {{$oi->product->pluck('pack_unit')->implode(',') }}
                                                </td>
                                                <td>
                                                    {{$oi->cbm }}
                                                </td>
                                                <td>
                                                    @if($order->container->pluck('code')->implode(',') == '40hq')
                                                        {{$oi->product->pluck('units_per_pallette_hq')->implode(',') }}
                                                    @else
                                                        {{$oi->product->pluck('units_per_pallette')->implode(',') }}
                                                    @endif
                                                </td>
                                                <td>
                                                    @php
                                                        if ($oi->product->pluck('stock')->implode(',') == ""){
                                                            $stock = "0";
                                                        }else{
                                                            $stock = $oi->product->pluck('stock')->implode(',');
                                                        }
                                                    @endphp
                                                    {{ $stock }}
                                                </td>
                                                <td class="">
                                                    <input name="oi[{{$oi->id}}][quantity]" style="width: 50px; padding: 3px !important; border: 1px solid #CCC;" type="text" value="{{ $oi->quantity }}" />
                                                </td>
                                                <td>
                                                    <input name="oi[{{$oi->id}}][price]" style="width: 50px; padding: 3px !important; border: 1px solid #CCC;" type="text" value="{{ $oi->unit_price_net }}" />
                                                    @if(has_role('admin'))
                                                        <p style='font-size: 9px;'>{{ $oi->base_price }}</p>
                                                    @endif
                                                </td>
                                                <td>
                                                    {{number_format($oi->amount_net,2)}}
                                                </td>
                                                <td>
														<span class="btn-group">
            												<a class="btn btn-block remove-record" data-toggle="modal" data-target="#deleteLineItemModal" data-item="{{ $oi->id }}" data-item2="{{ nl2br($oi->product->pluck('product_name')->implode(',') ) }}"><i class="icon-trash"></i></a>

            												<a href="/orders/update_line_item/{{$oi->id}}" class="btn btn-block"><i class="icon-edit"></i></a>
        												</span>
                                                </td>

                                            </tr>
                                        @endforeach
                                        <tr>
                                            <td colspan="9" style="font-size: 17px">{{ count($order->items) }} Line Items Total</td>
                                            <td>
                                                <input type="hidden" name="action" value="update_ois" />
                                                <input type="submit" value="SAVE" class="btn btn-success pull-right">
                                            </td>
                                        </tr>
                                        @if($order->discount > 0)
                                            <tr>
                                                <td colspan="8" class="align-right">Discount</td>
                                                <td class="align-right">{{ $order->discount }}%</td>
                                                <td></td>
                                            </tr>
                                        @endif

                                        @if($order->discount > 0)
                                            <tr>
                                                <td colspan="8" class="align-right">Sub Total Net</td>
                                                <td class="align-right">{{ $order->sub_total_net }}</td>
                                                <td></td>
                                            </tr>
                                        @else
                                            <tr>
                                                <td colspan="8" class="align-right">Sub Total Net</td>
                                                <td class="align-right">{{ $order->sub_total_net }}</td>
                                                <td></td>
                                            </tr>
                                        @endif


                                        @if($order->shipping_cost > 0)
                                            <tr>
                                                <td colspan="8" class="align-right">+ Freight Charge</td>
                                                <td class="align-right">{{ number_format($order->shipping_cost,2) }}</td>
                                                <td></td>
                                            </tr>
                                        @endif
                                        @if($order->taxcode->percent > 0)
                                            <tr>
                                                <td colspan="8" class="align-right">{{ $order->taxcode->name }}</td>
                                                <td class="align-right">{{ $order->tax_total }}</td>
                                                <td></td>
                                            </tr>
                                        @endif
                                        <tr>
                                            <td colspan="8" class="align-right">Total {{ $order->currency_code }}</td>
                                            <td class="align-right">{{ $order->total_gross }}</td>
                                            <td></td>
                                        </tr>
                                        <tr>
                                            <td colspan="8" class="align-right">Paid til now</td>
                                            <td class="align-right">{{ $order->getPaidTillNow() }}</td>
                                            <td></td>
                                        </tr>
                                        <tr>
                                            <td colspan="8" class="align-right">Open Balance</td>
                                            <td class="align-right">{{ $order->getOpenBalance() }}</td>
                                            <td></td>
                                        </tr>
                                    @else
                                        <tr>
                                            <td colspan="10">Nothing found</td>
                                        </tr>
                                    @endif
                                    </tbody>
                                </table>
                                {{--</form>--}}

                                {!! Form::close() !!}
                            </div>
                        </div>
                    </div>
                </div> <!-- /.widget-content -->
            </div> <!-- /.widget .box -->
        </div> <!-- /.col-md-12 -->
    </div> <!-- /.row -->

    {{--bootstrap modal--}}
    @if(count($order->items)>0)
        {{ Form::open(['method' => 'DELETE', 'action' => ['OrderController@lineItemDelete', $oi->id], 'id'=>'lineitem' ]) }}

        <div class="modal fade" id="deleteLineItemModal" tabindex="-1" role="dialog" aria-labelledby="deleteLineItemModal" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel"></h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body edit-content">
                        <div class="text-center" id="lineitemtext">Are you sure you want to delete this line item? </div>
                        {{--                    <input id="email" name="email" type="text" value="{{ request.form.email }}" />--}}
                    </div>
                    <div class="modal-footer">
                        {{--<a href="/orders/line-item-delete/{{$oi->id}}" id="lineitem"><button type="button" class="btn btn-danger pull-left">Yes, I am sure</button></a>--}}
                        {{ Form::submit('Delete', ['class' => 'btn btn-danger']) }}
                        {{--                    <a href="{{ URL::route('orders/line-item-delete/'.$oi->id) }}" id="lineitem"><button type="button" class="btn btn-danger pull-left">Yes, I am sure</button></a>--}}
                        {{--<a href="{!! route('lineItem.delete', ['item' => $oi->id]) !!}" id="lineitem"><button type="button" class="btn btn-danger pull-left">Yes, I am sure</button></a>--}}
                        <button type="button" class="btn btn-primary" data-dismiss="modal">No, not today</button>
                    </div>
                </div>
            </div>
        </div>
        {{ Form::close() }}
    @endif




    <div class="row">
        <div class="col-md-12 no-padding">
            <p class="record_status">Created: {{$order->created_at}} | Created by: {{$created_by_user}} | Updated: {{$order->updated_at}} | Updated by: {{$updated_by_user}} | <a href="/orders/changelog/{{ $order->id }}">Changelog</a></p>
        </div>
    </div>
@stop

@push('scripts')
    <script>
        $(document).on("click", ".remove-record", function () {
            var itemid= $(this).attr('data-item');
            var itemname= $(this).attr('data-item2');
            $("#lineitem").attr("action","http://americand.test/orders/line_item_delete/"+itemid)
            // $('#text').val(itemid);
            // var shit = document.getElementById('lineitemtext');
            // shit.innerHTML += itemname;
            // $("#lineitemtext").attr("href","/orders/line-item-delete/"+itemid)

        });
    </script>
@endpush
