@extends('layouts.print')

@section('content')
    <header>
        @if( $order->company->id == "8" )
            <img src="{{public_path('public/global/companies/8.png')}}" class="header-logo">
        @else
            <img src="{{public_path('public/global/companies/').$order->company->company_logo}}"  class="header-logo" align="left">
        @endif
    </header>

    <div class="content">
        <h1 class="text-center">Quotation</h1>
        <hr>
        <div class="row">
            {{--<div class="div-left">--}}
            {{--<img src="{{public_path('public/global/companies/').$order->company->company_logo}}"  class="logo-quotation" align="left">--}}
            {{--</div>--}}
            {{--<div class="col-sm-6">--}}
            {{--<h1 align="left" class="quotation">Quotation</h1>--}}
            {{--<hr style="width:310px; margin-right: 20px;">--}}
            {{--<hr>--}}
            <div class="col-xs-6">
                <ul class="company-details">
                    {{--<li><h1>Quotation</h1></li>--}}
                    {{--<li><hr></li>--}}
                    <li align="left"> Company Name:<strong> {{ $customer->customer_name }}</strong></li>
                    <li align="left" > Contact Name: {{$order->customerContact->contact_name}}</li>
                    <li align="left" > Customer Order: {{ $order->customer_order_number }}</li>
                </ul>
            </div>
            {{--</div>--}}
            {{--<div class="col-sm-6">--}}
            <div class="col-xs-6">
                {{--<ul class="order-info">--}}
                {{--<li>Number: {{$order->order_no}}</li>--}}
                {{--</ul>--}}
                <table class="order-info">
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
            {{--</div>--}}
            {{--<div class="div-right" align="right" style="width:300px;float: right; padding-top:20px;">--}}
            {{--<table>--}}
            {{--<tr>--}}
            {{--<td>Number:</td>--}}
            {{--<td>{{$order->order_no}}</td>--}}
            {{--</tr>--}}
            {{--<tr>--}}
            {{--<td>Date:</td>--}}
            {{--<td> {{$order->order_date}}</td>--}}
            {{--</tr>--}}
            {{--<tr>--}}
            {{--<td>Shipping:</td>--}}
            {{--<td>{{$order->from_port}}</td>--}}
            {{--</tr>--}}
            {{--<tr>--}}
            {{--<td>Payment:</td>--}}
            {{--<td> {{$payment_terms[0]['name']}}</td>--}}
            {{--</tr>--}}
            {{--</table>--}}
            {{--</div>--}}
        </div>


        {{--<div class="row-fluid">--}}
        {{--<div class="col-xs-6">--}}
        {{--<h1>--}}
        {{--<a href="https://twitter.com/tahirtaous">--}}
        {{--<img src="logo.png">--}}
        {{--Logo here--}}
        {{--</a>--}}
        {{--</h1>--}}
        {{--</div>--}}
        {{--<div class="col-xs-6 text-right">--}}
        {{--<h1>INVOICE</h1>--}}
        {{--<h1><small>Invoice #001</small></h1>--}}
        {{--</div>--}}
        {{--</div>--}}


        {{--<ul class="company-details" style="padding-top:0px;list-style-type:none;">--}}
        {{--<li align="left"> Company Name:<strong> {{ $customer->customer_name }}</strong></li>--}}
        {{--<li align="left" > Contact Name: {{$order->customerContact->contact_name}}</li>--}}
        {{--<li align="left" > Customer Order: {{$order->customer_order_number}}</li>--}}
        {{--</ul>--}}
        <div class="row-fluid" style="padding-top: 20px;">
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

                @foreach($order->items as $okey=>$order_item)
                    <tr>
                        <td>{{ $order_item->product_name }}</td>
                        <td>{{ $order_item->quantity }}</td>
                        <td>{{ $order_item->cbm }}</td>
                        <td>{{ $order_item->unit_price_net }}</td>
                        <td align="right">{{ $order_item->amount_net }} </td>
                    </tr>
                @endforeach

                <tr>
                    <td colspan="5" align="right">Subtotal {{ $order->currency_code }}: {{ number_format($order->getLineTotal(),2) }}</td>
                </tr>
                @if($order->discount != 0)
                    <tr>
                        <td colspan="5" align="right">Discount: {{ $order->discount }}%</td>
                    </tr>
                    <tr>
                        <td colspan="5" align="right">Subtotal: {{ number_format($order->sub_total_net,2) }}</td>
                    </tr>
                @endif
                @if($order->shipping_cost > 0)
                    <tr>
                        <td colspan="5" align="right">Freight Charge: {{ number_format($order->shipping_cost,2) }}</td>
                    </tr>
                @endif
                @if($order->taxcode->percent > 0)
                    <tr>
                        <td colspan="5" align="right">{{ $order->taxcode->name }} {{ $order->tax_total }}</td>
                    </tr>
                @endif
                <tr>
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
            {{--<div class="row-fluid">--}}
            {{--<div>--}}
            {{--<p><strong>{{ $order->company->name }}</strong></p>--}}
            {{--<p>{!! nl2br($order->company->ship_to) !!}</p>--}}
            {{--</div>--}}
            {{--</div>--}}

            {{--<div class="row-fluid">--}}
            {{--<hr style="width:748px;">--}}
            {{--</div>--}}
            {{--<div class="row-fluid">--}}
            {{--<p>{!! $order->company->bank_info !!}</p>--}}
            {{--<p style="font-style:italic; font-size: 11px; line-height: 12px;">--}}
            {{--{{ $order->company->df_quote }}--}}
            {{--</p>--}}
            {{--</div>--}}


        </div>
    </div>
    <footer>
        <div class="row">
            <div class="col-xs-5 footer-section1">
                <p>{{ $order->company->name }}</p>
                <p>{!! nl2br($order->company->ship_to) !!}</p>
            </div>


            <div class="col-xs-6 footer-section2">
                <p>{!! $order->company->bank_info !!}</p>
                <p style="font-style:italic; font-size: 11px; line-height: 12px;">
                {{ $order->company->df_quote }}
                </p>
            </div>
        </div>
    </footer>
@endsection

