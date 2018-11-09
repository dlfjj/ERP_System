{{--<html>--}}
{{--<head>--}}
    {{--<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>--}}
    	{{--<link rel="stylesheet" href="{{ asset('css/main.css') }}" />--}}
    {{--<link href="{{ asset('assets/css/pdf.css') }}" rel="stylesheet"/>--}}

{{--    	<link href="{{asset('/bootstrap/css/bootstrap.min.css')}}" rel="stylesheet"/>--}}
    {{--<style>--}}
        {{--@import url(http://fonts.googleapis.com/css?family=Bree+Serif);--}}
    {{--</style>--}}
{{--</head>--}}
@extends('layouts.print')

@section('content')
{{--<body>--}}
<div class="content" style="border:5px solid #e5e3e3;">
    <div class="row">
        <div class="div-left">
            <img src="{{public_path('public/global/companies/').$order->company->company_logo}}"  class="logo-quotation" align="left">
        </div>
        <p class="quotation" align="right">Quotation</p>
        <hr>
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
        <li align="left"> Company Name:<strong> {{$customers_details[0]['name']}}</strong></li>
        <li align="left" > Contact Name: {{$customers_details[0]['contact_person']}}</li>
        <li align="left" > Customer Order: {{$customers_details[0]['ids_orders']}}</li>
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
            <tr style="background-color:#e5e3e3; color:black; font-weight: bold">
                <td>Remarks:</td>
            </tr>
            <tr class="remark-detail">
                <td>{{ $order->order_remarks_public }}</td>
            </tr>
        </table>
        <table class="table table-condensed">
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
{{--<th rowspan="3">{{ $customers_details[0]['ship_to'] }}</th>--}}
                <td colspan="4" align="right">Subtotal {{ $order->currency_code }}:</td>
                <td colspan="5" align="right">{{ number_format($order->getLineTotal(),2) }}</td>
            </tr>
            <tr style="border-top: 2px solid #eee;">
                <td colspan="4" align="right">Total amount:</td>
                <td colspan="5" align="right" >{{ number_format($order->total_gross,2) }}</td>
            </tr>
            {{--</tbody>--}}
            {{--<tr>--}}
                {{--<td align="left"></td>--}}
                {{--<td  align="right">{{$order->delivery_Address}}</td>--}}
            {{--</tr>--}}
            {{--<tr>--}}
                {{--<td align="right">Subtotal: {{ $order->currency_code }} {{ number_format($order->getLineTotal(),2) }}</td>--}}
                {{--<td align="right">Total Amount: {{ number_format($order->total_gross,2) }}</td>--}}
            {{--</tr>--}}
        </table>

        @if(count($order->orderitems) > 8)
            <div class="row-fluid" id="inv_oi_line"></div>
            <div style="page-break-after:always"></div>
        @else
            <div class="row-fluid" id="inv_oi_line"></div>
        @endif
        {{--<hr class="linebreak">--}}
        {{--<table>--}}
            {{--<tr style="background-color:#e5e3e3; color:black;"><td></td></tr>--}}
            {{--<tr>--}}
                {{--<td>--}}

        <div class="row-fluid">
            <div>
                <p><strong>{{ $order->company->name }}</strong></p>
                <p>{!! nl2br($order->company->ship_to) !!}</p>
            </div>
        </div>
        {{--<div class="row">--}}
            {{--<h1 align="right" style="border-bottom: 5px solid #e5e3e3; border-bottom-width:25px;"></h1>--}}
            {{--<p class="company-address">{{$customers_details[0]['bank_info']}}    call on:{{$customers_details[0]['contact_phone']}}</p>--}}
        {{--</div>--}}
        <hr style="width:400px;">
        {{--<div class="row-fluid" id="inv_oi_line"></div>--}}
        <div class="row-fluid">
            <p>{!! $order->company->bank_info !!}</p>
            <p style="font-style:italic; font-size: 11px; line-height: 12px;">
                {{ $order->company->df_quote }}
            </p>
        </div>
    </div>
</div>
{{--</body>--}}
@endsection