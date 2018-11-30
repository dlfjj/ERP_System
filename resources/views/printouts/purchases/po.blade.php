@extends('layouts.print')

@section('content')
    <div class="container" style="padding-top: 5px;">
        {{--header section--}}
        {{--<div class="row">--}}
            {{--<div class="col-xs-4">--}}
                {{--<img src="{{ asset('/public/global/companies/8.png') }}" width="300"/>--}}
            {{--</div>--}}
            {{--<div class="col-xs-4 text-center">--}}
                {{--<h4>PURCHASE ORDER</h4>--}}
                {{--@if($purchase->status == "DRAFT")--}}
                    {{--<p><strong>DRAFT ONLY!</strong></p>--}}
                {{--@elseif ($purchase->status == "OPEN")--}}
                {{--@elseif ($purchase->status == "CLOSED")--}}
                    {{--<p><strong>CLOSED</strong></p>--}}
                {{--@elseif ($purchase->status == "VOID")--}}
                    {{--<p><strong>VOID</strong></p>--}}
                {{--@endif--}}
            {{--</div>--}}
            {{--<div class="col-xs-4 text-right">--}}
                {{--<h5><strong>{{ $purchase->company->company_name }}</strong></h5>--}}
                {{--<p class="purchase_order_font">{!! nl2br($purchase->company->bill_to) !!}</p>--}}
            {{--</div>--}}
        {{--</div>--}}

        {{--<div class="row">--}}
            {{--<div class="col-xs-12">--}}
                {{--<div id="printout_header_line"></div>--}}
                {{--<p>&nbsp;</p>--}}
            {{--</div>--}}
        {{--</div>--}}
        <h2 align="center">Purchase Order</h2>
        <div class="row">
            <div class="col-xs-12">
                <div id="printout_header_line"></div>
                <p>&nbsp;</p>
            </div>
        </div>

        <div class="row">
            <div class="col-xs-4 pull-left">
                <p style="text-decoration: underline;">Issued To:</p>
                <p class="purchase_order_font">
                    <strong>{{ $vendor->company_name }}</strong><br />
                    @if($vendor->local_address != "")
                    {{ $vendor->local_address }} </br >
                    @endif
                    @if($vendor->street_1 != "")
                    {{ $vendor->street_1 }} </br >
                    @endif
                    @if($vendor->street_2 != "")
                    {{ $vendor->street_2 }} </br >
                    @endif
                    {{ $vendor->city }}, {{ $vendor->country }} </br >

                    <br /> Att: {{$purchase->vendor_contact}}
                </p>
            </div>
            <div class="col-xs-4 pull-left">
                <p style="text-decoration: underline;">Deliver To:</p>
                @if($purchase->ship_to != "")
                    <p class="purchase_order_font">{{ nl2br($purchase->ship_to) }}</p>
                @else
                    <p class="purchase_order_font">{{ nl2br($purchase->company->deliver_to) }}</p>
                @endif
            </div>




            <div class="col-xs-4 pull-right">
                <table class="table table-bordered table-invoice table-invoice-header table-no-break table-font">
                    <tr>
                        <td>Purchase No 文档编号</td>
                        <td>
                            {{$purchase->id}}
                        </td>
                    </tr>
                    <tr>
                        <td>Date printed</td>
                        <td>{{ date("Y-m-d") }}</td>
                    </tr>
                    <tr>
                        <td>Order Status</td>
                        @if($purchase->status == "DRAFT")
                            <td>DRAFT ONLY!</td>
                        @elseif ($purchase->status == "OPEN")
                        @elseif ($purchase->status == "CLOSED")
                            <td>CLOSED</td>
                        @elseif ($purchase->status == "VOID")
                           <td>VOID</td>
                        @else
                            <td></td>
                        @endif
                    </tr>
                </table>
            </div>
        </div>

        <div class="row">
            <div class="col-xs-12">
                <table class="table table-bordered table-condensed table-invoice table-font">
                    <thead>
                    <tr>
                        <th>Placed 发行日期</th>
                        <th>Required 需要日期</th>
                        <th>Confirmed 确定日期</th>
                        <th>Payment Terms 付款条款</th>
                        <th>Your Contact 您的联系人</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td>{{ $purchase->date_placed }}</td>
                        <td>{{ $purchase->date_required }}</td>
                        <td>{{ $purchase->date_confirmed }}</td>
                        <td>{{ $purchase->payment_terms }}</td>
                        <td>{{$purchase->user->first_name}} {{$purchase->user->last_name}}</td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>



        <div class="row">
            <!--=== Table ===-->
            <div class="col-xs-12">
                <table class="table table-bordered table-highlight-head table-invoice table-font">
                    <thead>
                    <tr>
                        <th>Part # 零件号</th>
                        <th class="cell-wide">SKU / Description 描述</th>
                        <th>UOM 单位</th>
                        <th>QTY 数量</th>
                        <th>Net Price 净价</th>
                        <th>Net Amount 净额</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($purchase->items as $oi)
                        <tr>
                            <td class='no-wrap'>{{$oi->product->product_code }}</td>
                            <td>
                                {{ nl2br($oi->product->product_name ) }}
                                @if($oi->remarks != "")
                                    <br /><span class="text-muted">{{ nl2br($oi->remarks) }}</span>
                                @endif
                            </td>
                            <td>{{$oi->product->uom}}</td>
                            <td>{{$oi->quantity}}</td>
                            <td>{{round($oi->net_price,4)}}</td>
                            <td>{{round($oi->net_total,4)}}</td>
                        </tr>
                    @endforeach
                    <tr>
                        <td colspan="4"></td>
                        <td><strong>Sub Total</strong></td>
                        <td><strong>{{$purchase->currency_code}} {{$purchase->net_sub_total}}</strong></td>
                    </tr>
                    @if($purchase->net_shipping_amount > 0)
                        <tr>
                            <td colspan="4"></td>
                            <td>{{ $purchase->shipping_amount_label }}</td>
                            <td>{{$purchase->net_shipping_amount}}</td>
                        </tr>
                    @endif
                    @if($purchase->net_handling_amount > 0)
                        <tr>
                            <td colspan="4"></td>
                            <td class="no-wrap">{{ $purchase->handling_amount_label }}</td>
                            <td>{{$purchase->net_handling_amount}}</td>
                        </tr>
                    @endif
                    @if($purchase->taxcode_percent > 0)
                        <tr>
                            <td colspan="4"></td>
                            <td>{{ $purchase->taxcode_name }}</td>
                            <td>{{ number_format($purchase->tax_total,2) }}</td>
                        </tr>
                    @endif
                    @if($purchase->getPaidUntilNow() > 0)
                        <tr>
                            <td colspan="4"></td>
                            <td class="no-wrap">Paid til now</td>
                            <td>{{ number_format($purchase->getPaidUntilNow(), 2) }}</td>
                        </tr>
                    @endif
                    <tr>
                        <td colspan="4"></td>
                        <td class="no-wrap"><strong>Total Due</strong></td>
                        <td><strong>{{$purchase->currency_code}} {{number_format($purchase->getOpenBalance(),2)}}</strong></td>
                    </tr>
                    </tbody>
                </table>
            </div>
            <!-- /Table -->
        </div>

        @if($purchase->status == 'DRAFT')
            <div class="row">
                <div class="col-md-12">
                    <div class="alert alert-warning">
                        <strong>Warning!</strong> This P.O is a DRAFT ONLY!
                    </div>
                </div>
            </div>
        @endif

        @if($purchase->remarks_public != "")
            <div class="row padding-top-10px">
                <div class="col-xs-12">
                    <div class="well well-sm">
                        <p class="purchase_order_font"><strong>Remarks: </strong> {{$purchase->remarks_public}}</p>
                    </div>
                </div>
            </div>
        @endif

        <div class="row padding-top-10px">
            <div class="col-xs-12">
                <p class="purchase_order_font">
                    {!! nl2br($purchase->company->po_footer) !!}
                </p>
            </div>
        </div>

    </div>
@endsection
