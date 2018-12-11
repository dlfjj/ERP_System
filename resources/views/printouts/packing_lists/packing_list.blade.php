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
                    <strong>Ship by</strong>: {{ $order->container->name }}
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
                        <th class="cell-tight"  align="left">Qty</th>
                        <th class="cell-tight"  align="left">Unit</th>
                        <th class="cell-tight" align="left">Unit/Box</th>
                        <th class="cell-tight" align="left">Pkg</th>
                        <th class="cell-tight" align="left">N.W (KG)</th>
                        <th class="cell-tight" align="right">G.W (KG)</th>
                    </tr>
                    </thead>
                    <tbody>
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
            </div>
            <!-- /Table -->
        </div>
    </div>

@endsection

