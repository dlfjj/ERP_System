@extends('layouts.print')

@section('content')
    <div class="container">
        {{--<div class="row" style="margin-bottom: 0px; padding-bottom: 0px;">--}}
            {{--<div class="col-xs-4">--}}
                {{--<img src="/assets/img/logo.png" />--}}
            {{--</div>--}}
            {{--<div class="col-xs-4 text-center">--}}
                {{--<h4>QUOTATION</h4>--}}
            {{--</div>--}}
            {{--<div class="col-xs-4 text-right">--}}
                {{--<h5><strong>{{ $settings['company_name'] }}</strong></h5>--}}
                {{--<p style="font-size: 11px;">{{ nl2br($settings['company_bill_to']) }}</p>--}}
            {{--</div>--}}
        {{--</div>--}}

        <div class="row">
            <div class="col-xs-4">
                <p style="text-decoration: underline;">Customer Info:</p>
                <p>
                    <strong>{{ $customer->customer_name }}</strong><br />
                    <strong>Contact Name</strong>: {{ nl2br($order->customerContact->contact_name) }}<br />
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
                {{--<p style="font-weight: 800;">{{ $order->company->name }}</p>--}}
                {{--<p>--}}
                    {{--{{ $order->company->bill_to }}--}}
                {{--</p>--}}
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

            <div class="row">
            </div>
        </div>
    </div>

@endsection

