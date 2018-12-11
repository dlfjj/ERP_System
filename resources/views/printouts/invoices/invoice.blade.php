@extends('layouts.print')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-xs-4">
                <p style="text-decoration: underline;">Customer Info:</p>
                <p>
                    <strong>{{ $customer->customer_name }}</strong><br />
                    <strong>Contact Name</strong>: {{ nl2br($order->customerContact->contact_name) }}<br />
                    <strong>Billing Address</strong>: {{$order->billing_address}}<br/>
                    <strong>Ship by</strong>: {{ $order->container->name }}<br/>
                    <strong>Tax ID</strong>: {{ $order->customer->tax_id }}
                </p>
            </div>
            @if($order->delivery_address != "")
                <div class="col-xs-3">
                    <p style="text-decoration: underline;">Ship To:</p>
                    <p>
                        {!! nl2br($order->delivery_address) !!}
                    </p>
                </div>
                {{--@else--}}
                {{--<div class="col-xs-4">--}}
                {{--<p></p>--}}
                {{--</div>    --}}
            @endif
            <div class="col-xs-5 pull-right">
                <table class="table table-bordered table-invoice table-invoice-header">
                    <tr>
                        <td>Quotation Number</td>
                        <td>{{$order->order_no}}</td>
                    </tr>
                    <tr>
                        <td>Quotation Date</td>
                        <td>{{ $order->order_date }}</td>
                    </tr>
                    <tr>
                        <td>Shipping</td>
                        <td>{{ $order->from_port }} / {{$order->shipping_method}}</td>
                    </tr>
                    <tr>
                        <td>Payment Term</td>
                        <td>{{$payment_terms[0]['name'] }}</td>
                    </tr>
                    @if($order->estimated_finish_date != null)
                        <tr>
                            <td>Shipping Date:</td>
                            <td>{{ $order->estimated_finish_date }}</td>
                        </tr>
                    @else
                        <tr>
                            <td>Shipping Date:</td>
                            <td>TBD</td>
                        </tr>
                    @endif
                    @if($order->vessel_etd != "0000-00-00")
                        <tr>
                            <td>Vessel ETD</td>
                            <td>{{ $order->vessel_etd }}</td>
                        </tr>
                    @endif
                    @if($order->vessel_eta != "0000-00-00")
                        <tr>
                            <td>Vessel ETA</td>
                            <td>{{ $order->vessel_eta }}</td>
                        </tr>
                    @endif
                    <tr>
                        <td>Packages</td>
                        <td>{{ $package_count }}</td>
                    </tr>
                    <tr>
                        <td>Weight&nbsp;(NT/GR)</td>
                        <td>{{ number_format($net_weight,2) }}KG / {{ number_format($gross_weight,2) }}KG  </td>
                    </tr>
                    <tr>
                        <td>Volume</td>
                        <td>{{ number_format($volumn,3) }} m&sup3;</td>
                    </tr>
                </table>
            </div>
        </div>

        <div class="row">
            <div class="col-xs-12">
                <p>&nbsp;</p>
            </div>
        </div>


        <div class="row">
            <!--=== Table ===-->
            <div class="col-xs-12">
                @if($order->order_remarks_public != "")
                <table class="table table-bordered table-invoice">
                    <thead>
                    <tr>
                        <th>Remarks</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td>{{ $order->order_remarks_public }}</td>
                    </tr>
                    </tbody>
                </table>
                @endif
                <table class="table table-bordered table-highlight-head table-invoice">
                    <thead>
                    <tr>
                        <th align="left">Item</th>
                        <th class="cell-tight" align="left">Qty</th>
                        <th  class="cell-tight" align="left">in ctn/total ctn</th>
                        @if($order->taxcode->percent > 0)
                            <td  align="left">Nt Price</td>
                            <td  align="left">Tax</td>
                            <td  align="left">Gr Price</td>
                        @else
                            <td  align="left">Price</td>
                        @endif
                        <th  class="cell-tight" align="left">Price</th>
                        <th  class="cell-tight" align="left">Line total</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($order->items as $okey=>$order_item)
                        <tr>
                            <td>{{ $order_item->product_name }}</td>
                            <td>{{ $order_item->quantity }}</td>
                            <td>{{ $order_item->cbm }}</td>
                            @if($order->taxcode->percent > 0)
                                <td>
                                    {{ number_format($orderitem->unit_price_net,2) }}
                                </td>
                                <td>
                                    {{ $order->taxcode->perc }} %
                                </td>
                                <td>
                                    {{ number_format($order_item->unit_price_gross,2) }}
                                </td>
                            @else
                                <td>
                                    {{ number_format($order_item->unit_price_net,2) }}
                                </td>
                            @endif
                            <td>{{ $order_item->unit_price_net }}</td>
                            <td align="right">{{ $order_item->amount_net }} </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            <div class="col-xs-6">
                <table class="table table-bordered table-invoice table-invoice-header" style="font-size: 15px;">
                    <tr>
                        <th class="cell-tight">{{ $order->company->name }}</th>
                    </tr>
                    <tr>
                        <td style="font-weight: 300;">{!! $order->company->bill_to !!}</td>
                    </tr>
                </table>
            </div>
            <div class="col-xs-6">
                <div class="pull-right">
                <table class="table table-bordered table-invoice table-invoice-header">
                    <tr>
                        <td align="right"> Subtotal: </td>
                        <td>{{ $order->currency_code }} {{ number_format($order->getLineTotal(),2) }}
                        </td>
                    </tr>
                    @if($order->discount != 0)
                        <tr>
                            <td align="right">Discount: </td>
                            <td>{{ $order->discount }}%</td>
                        </tr>
                        <tr>
                            <td align="right">Subtotal: </td>
                            <td>{{ $order->currency_code }} {{ number_format($order->sub_total_net,2) }}</td>
                        </tr>
                    @endif
                    @if($order->shipping_cost > 0)
                        <tr>
                            <td align="right">Freight Charge:  </td>
                            <td>{{ $order->currency_code }} {{ number_format($order->shipping_cost,2) }}</td>
                        </tr>
                    @endif
                    @if($order->taxcode->percent > 0)
                        <tr>
                            <td align="right">{{ $order->taxcode->name }} </td>
                            <td>{{ $order->tax_total }}</td>
                        </tr>
                    @endif
                    <tr>
                        <td align="right" >Total amount: </td>
                        <td>{{ $order->currency_code }} {{ number_format($order->total_gross,2) }}</td>
                    </tr>
                </table>
                </div>
            </div>
            <!-- /Table -->
        </div>
    </div>

@endsection

