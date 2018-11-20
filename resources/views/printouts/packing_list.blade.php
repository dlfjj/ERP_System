@extends('layouts.print')


@section('content')
    <header>
        @if( $order->company->id == "8" )
            <img src="{{public_path('public/global/companies/8.png')}}" class="header-logo">
        @else
            <img src="{{public_path('public/global/companies/').$order->company->company_logo}}"  class="header-logo" align="left">
        @endif
    </header>
    <h2 align="center">Packing List</h2>
    <hr>
    <div class="content">
        <div class="row">
            <div class="col-xs-6" >
                <ul class="company-details">
                    <li align="left">Company Name:<strong> {{ $customer->customer_name }}</strong></li>
                    <li align="left" >Contact Name: {{ $order->customerContact->contact_name }}</li>
                    <li align="left" >Customer Order: {{ $order->customer_order_number }}</li>
                    <li align="left">Ship by: {{ $order->container->name }}</li>
                </ul>
            </div>

            <div class="col-xs-6" >
                <table class="order-info">
                    <tr>
                        <td>Number</td>
                        <td>{{$order->order_no}}</td>
                    </tr>
                    <tr>
                        <td>Date</td>
                        <td> {{$order->order_date}}</td>
                    </tr>
                    <tr>
                        <td>Shipping Date</td>
                        <td> {{$order->estimated_finish_date}}</td>
                    </tr>
                    <tr>
                        <td>Container Number</td>
                        <td>{{ $order->container_number }}</td>
                    </tr>
                </table>
            </div>
        </div>
        <div class="row" style="background: #e5e3e3; height: 15px; margin-top: 14px; padding-bottom: 5px; width:740px;">
            <div class="col-xs-5">
                <p><strong>Address:</strong></p>
            </div>
            <div class="col-xs-2">
            </div>
            <div class="col-xs-5">
                <p><strong>Shipping to:</strong></p>
            </div>
        </div>

        <div class="row">
            <div class="col-xs-5">
                <p>{!! nl2br($order->billing_address ) !!}</p>
            </div>
            <div class="col-xs-2">
            </div>
            <div class="col-xs-5">
                <p>{!! nl2br($order->delivery_address ) !!}</p>
            </div>
        </div>
        <div class="row-fluid" style="padding-top: 20px;">
            {{--<table class="address-tab">--}}
                {{--<tr style="background-color:#e5e3e3; color:black; font-weight: bold;">--}}
                    {{--<td align="left">Address</td>--}}
                    {{--<td align="left">Shipping To</td>--}}
                {{--</tr>--}}
                {{--<tr class="address-detail">--}}
                    {{--<td align="left">{!! $order->billing_address !!}</td>--}}
                    {{--<td  align="left">{{ $order->delivery_address }}</td>--}}
                {{--</tr>--}}
            {{--</table>--}}
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
                    <td  align="left">Unit</td>
                    <td  align="left">Unit/Box</td>
                    <td  align="left">Pkg</td>
                    <td  align="left">N.W (KG)</td>
                    <td  align="right">G.W (KG)</td>
                </tr>

                @foreach($order->items as $okey=>$order_item)
                    <tr>
                        <td>
                            {{ $order_item->product_code }}
                            <br />
                            {{ $order_item->product_name }}
                            @if( $order_item->remark != "")
                            <br />
                            Remark: {{ $order_item->remark }}
                            @endif
                        </td>
                        <td>
                            {{ $order_item->quantity }}
                        </td>
                        <td>
                            {{ getUom($order_item) }}
                        </td>
                        <td>
                            {{ ($order->container_type != 4) ? $order_item->product->pluck('pack_unit')[0] : $order_item->product->pluck('pack_unit_hq')[0] }}
                        </td>
                        <td>
                            {{ getNumberOfItemPackages($order_item) }}
                        </td>
                        <td>
                            {{ number_format(getItemNetWeight($order_item),2) }}
                        </td>
                        <td>
                            {{ number_format(getItemGrossWeight($order_item),2) }}
                        </td>
                    </tr>
                @endforeach
                <tr style="font-weight: bold; font-size:12px;">
                    <td colspan="7">Net Weight Total: {{ number_format(($order->net_weight > 0 ? $order->net_weight : $nt_weight_total), 2)}}KG &nbsp; &nbsp; Gross Weight Total: {{ number_format(($order->gross_weight > 0 ? $order->gross_weight : $gr_weight_total), 2)}}KG &nbsp;&nbsp; Packages: {{ getNumberOfPackages($order) }} &nbsp; &nbsp;Pallets: {{ getNumberOfPallets($order) }} &nbsp;&nbsp; Pallets weight: {{ $order->weight_of_pallets }} KG</td>
                </tr>
            </table>
            @if(count($order->orderitems) > 8)
                <div class="row-fluid" id="inv_oi_line"></div>
                <div style="page-break-after:always"></div>
            @else
                <div class="row-fluid" id="inv_oi_line"></div>
            @endif
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
