<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    {{--	<link rel="stylesheet" href="{{ asset('css/main.css') }}" />--}}
    <link href="{{ asset('assets/css/pdf.css') }}" rel="stylesheet"/>

    {{--	<link href="{{asset('/bootstrap/css/bootstrap.min.css')}}" rel="stylesheet"/>--}}
    <style>
        @import url(http://fonts.googleapis.com/css?family=Bree+Serif);
    </style>
</head>
<body>
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

    <ul class="company-details" style="padding-top:120px;list-style-type:none;">
        <li align="left"> Company Name:<strong> {{$customers_details[0]['name']}}</strong></li>
        <li align="left" > Contact Name: {{$customers_details[0]['contact_person']}}</li>
        <li align="left" > Customer Order: {{$customers_details[0]['ids_orders']}}</li>
    </ul>
    <div class="outer-address-content">
        <table class="address-tab">
            <tr style="background-color:#e5e3e3; color:black;">
                <td align="left">Address</td>
                <td align="left">Shipping To</td>
            </tr>
            <tr class="address-detail">
                <td align="left">{{ $order->delivery_address }}</td>
                <td  align="left">{{ $order->delivery_address }}</td>
            </tr>
        </table>
        <table>
            <tr style="background-color:#e5e3e3; color:black;">
                <td>Remarks:</td>
            </tr>
            <tr class="remark-detail">
                <td>{{ $order->order_remarks_public }}</td>
            </tr>
        </table>
        <table>
            <tr style="background-color:#e5e3e3; color:black;">
                <td align="left">Item</td>
                <td  align="right">Qty</td>
                <td  align="right">in ctn/total ctn </td>
                <td  align="right">price  </td>
                <td  align="right">line total </td>
            </tr>
            <tr>
                <td align="left"></td>
                <td  align="right">{{$order->delivery_Address}}</td>
            </tr>
            <tr >
                <td align="left">{{$order_items[0]['product_name']}}</td>
                <td  align="right">{{$order_items[0]['quantity']}}</td>
                <td  align="right">{{$order_items[0]['cbm']}}</td>
                <td  align="right">{{$order_items[0]['unit_price_net']}}  </td>
                <td  align="right">{{$order_items[0]['amount_net']}} </td>
            </tr>
            {{--<tr>--}}
                {{--<td align="left"></td>--}}
                {{--<td  align="right">{{$order->delivery_Address}}</td>--}}
            {{--</tr>--}}
        </table>
        <hr class="linebreak">
        {{--<table>--}}
            {{--<tr style="background-color:#e5e3e3; color:black;"><td></td></tr>--}}
            {{--<tr>--}}
                {{--<td>--}}
        <div class="wrapper">
            <div class="container">
                    <p align="left">{{ $customers_details[0]['name'] }}</p>
                    <p align="left">{{ $customers_details[0]['ship_to'] }}</p>
            </div>
            {{--</td>--}}
            {{--</tr>--}}
            {{--<tr>--}}
                {{--<td>--}}
            <div class="div-right">
                    <p align="right">Subtotal USD:{{$customers_details[0]['currency_code']}}</p>
                    <p align="right">Total Amount:{{$order_items[0]['amount_net']}}</p>
            </div>
                {{--</td>--}}
            {{--</tr>--}}

        {{--</table>--}}
        </div>
        <h1 align="right" style="border-bottom:5px solid #e5e3e3; border-bottom-width:25px;"></h1>
        <p class="company-address">{{$customers_details[0]['bank_info']}}    call on:{{$customers_details[0]['contact_phone']}}</p>
    </div>
</div>
</body>



</html>