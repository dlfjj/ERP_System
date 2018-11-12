@extends('layouts.print')

@section('content')

    <div class="content">
        <div class="row-fluid">
            <div class="div-left">
                <img src="{{public_path('public/global/companies/').$order->company->company_logo}}"  class="logo-quotation" align="left">
            </div>
            <h1 align="right" class="quotation">Quotation</h1>
            <hr style="width:310px; margin-right: 20px;">
            <hr>
            <ul class="company-details" style="padding-top:0px;list-style-type:none;">
                <li align="left"> Company Name:<strong> {{ $customer->customer_name }}</strong></li>
                <li align="left" > Contact Name: {{$order->customerContact->contact_name}}</li>
                <li align="left" > Customer Order: {{$order->customer_order_number}}</li>
            </ul>
            <div class="div-right" align="right" style="width:300px;float: right; padding-top:20px;">
                <table>
                    <tr>
                        <td>Number:</td>
                        <td>{{$order->order_no}}</td>
                    </tr>
                    <tr>
                        <td>Date:</td>
                        <td> {{$order->order_date}}</td>
                    </tr>
                    <tr>
                        <td>Shipping:</td>
                        <td>{{$order->from_port}}</td>
                    </tr>
                    <tr>
                        <td>Payment:</td>
                        <td> {{$payment_terms[0]['name']}}</td>
                    </tr>
                </table>
            </div>
        </div>

        <ul class="company-details" style="padding-top:0px;list-style-type:none;">
            <li align="left"> Company Name:<strong> {{ $customer->customer_name }}</strong></li>
            <li align="left" > Contact Name: {{$order->customerContact->contact_name}}</li>
            <li align="left" > Customer Order: {{$order->customer_order_number}}</li>
        </ul>
        <div class="row-fluid">
            <table class="address-tab">
                <tr style="background-color:#e5e3e3; color:black; font-weight: bold;">
                    <td align="left">Address</td>
                    <td align="left">Shipping To</td>
                </tr>
                <tr class="address-detail">
                    <td align="left">{{ $order->delivery_address }}</td>
                    <td  align="left">{{ $order->delivery_address }}</td>
                </tr>
            </table>
            <table>
                @if($order->order_remarks_public != "")
                    <tr style="background-color:#e5e3e3; color:black; font-weight: bold">
                        <td>Remarks:</td>
                    </tr>
                    <tr class="remark-detail">
                        <td>{{ $order->order_remarks_public }}</td>
                    </tr>
                @endif
            </table>
            <table class="table table-bordered table-condensed table-invoice">
                <tr style="background-color:#e5e3e3; color:black; font-weight: bold;">
                    <td align="left">Item</td>
                    <td  align="left">Qty</td>
                    <td  align="left">in ctn/total ctn</td>
                    <td  align="left">Price</td>
                    <td  align="right">Line total</td>
                </tr>
                {{--<tbody>--}}
                {{--<td align="left"></td>--}}
                {{--<td  align="right">{{$order->delivery_Address}}</td>--}}
                @foreach($order->items as $okey=>$order_item)
                    <tr>
                        {{--<td align="left">{{$order_items[0]['product_name']}}</td>--}}
                        {{--<td  align="right">{{$order_items[0]['quantity']}}</td>--}}
                        {{--<td  align="right">{{$order_items[0]['cbm']}}</td>--}}
                        {{--<td  align="right">{{$order_items[0]['unit_price_net']}}  </td>--}}
                        {{--<td  align="right">{{$order_items[0]['amount_net']}} </td>--}}
                        <td>{{ $order_item->product_name }}</td>
                        <td>{{ $order_item->quantity }}</td>
                        <td>{{ $order_item->cbm }}</td>
                        <td>{{ $order_item->unit_price_net }}</td>
                        <td align="right">{{ $order_item->amount_net }} </td>
                    </tr>
                @endforeach

                <tr>
                    <td colspan="5" align="right">Subtotal {{ $order->currency_code }}: {{ number_format($order->getLineTotal(),2) }}</td>
                    {{--                <td colspan="5" align="right">{{ number_format($order->getLineTotal(),2) }}</td>--}}
                </tr>
                @if($order->discount != 0)
                    <tr>
                        {{--<td colspan="4" align="right">Discount:</td>--}}
                        <td colspan="5" align="right">Discount: {{ $order->discount }}%</td>
                    </tr>
                    <tr>
                        {{--<td colspan="4" align="right">Subtotal {{ $order->company->currency_code }}:</td>--}}
                        <td colspan="5" align="right">Subtotal: {{ number_format($order->sub_total_net,2) }}</td>
                    </tr>
                @endif
                @if($order->shipping_cost > 0)
                    <tr>
                        {{--<td colspan="4" align="right">Freight Charge:</td>--}}
                        <td colspan="5" align="right">Freight Charge: {{ number_format($order->shipping_cost,2) }}</td>
                    </tr>
                @endif
                @if($order->taxcode->percent > 0)
                    <tr>
                        {{--<td colspan="4" align="right">{{ $order->taxcode->name }}</td>--}}
                        <td colspan="5" align="right">{{ $order->taxcode->name }} {{ $order->tax_total }}</td>
                    </tr>
                @endif
                <tr>
                    {{--<td colspan="4" align="right">Total amount:</td>--}}
                    <td colspan="5" align="right" >Total amount: {{ number_format($order->total_gross,2) }}</td>
                </tr>

            </table>

            @if(count($order->orderitems) > 8)
                <div class="row-fluid" id="inv_oi_line"></div>
                <div style="page-break-after:always"></div>
            @else
                <div class="row-fluid" id="inv_oi_line"></div>
            @endif


            {{--below section should go to the footer--}}
            <div class="row-fluid">
                <div>
                    <p><strong>{{ $order->company->name }}</strong></p>
                    <p>{!! nl2br($order->company->ship_to) !!}</p>
                </div>
            </div>

            <div class="row-fluid">
                <hr style="width:748px;">
            </div>
            <div class="row-fluid">
                <p>{!! $order->company->bank_info !!}</p>
                <p style="font-style:italic; font-size: 11px; line-height: 12px;">
                    {{ $order->company->df_quote }}
                </p>
            </div>
        </div>
    </div>
    <footer>footer on each page</footer>
@endsection