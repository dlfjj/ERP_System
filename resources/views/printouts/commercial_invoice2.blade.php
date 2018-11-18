@extends('layouts.print')


@section('content')
	<header>
		@if( $order->company->id == "8" )
			<img src="{{public_path('public/global/companies/8.png')}}" class="header-logo">
		@else
			<img src="{{public_path('public/global/companies/').$order->company->company_logo}}"  class="header-logo" align="left">
		@endif
	</header>
	<h2 align="center">Invoice</h2>
	<hr>
	<div class="content">
		<div class="row">
			<div class="col-xs-6">
				<ul class="company-details">
					<li align="left">Company Name:<strong> {{ $customer->customer_name }}</strong></li>
					<li align="left" >Contact Name: {{ $order->customerContact->contact_name }}</li>
					<li align="left" >Customer Order: {{ $order->customer_order_number }}</li>
					<li align="left">Ship by: {{ $order->container->name }}</li>
                    <li align="left">Tax ID: {{ $order->customer->tax_id }}</li>

					{{--<li>Number: {{$order->order_no}} </li>--}}
				</ul>
			</div>
			<div class="col-xs-6">
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
					<tr>
						<td>Shipping Date:</td>
						<td>{{ $order->estimated_finish_date }}</td>
					</tr>
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
                    {{--<tr>--}}
                        {{--<td>Packages</td>--}}
                        {{--<td>{{ $order->getNumberOfPackages() }}</td>--}}
                    {{--</tr>--}}
                    <tr>
                        <td>Weight&nbsp;(NT/GR)</td>
{{--                        <td>{{ number_format(getNetWeight($order),2) }}KG / {{ number_format($order->getGrossWeight(),2) }}KG</td>--}}
                        <td>{{ number_format($net_weight,2) }}KG / {{ number_format($order->getGrossWeight(),2) }}KG </td>
                    </tr>
                    {{--<tr>--}}
                        {{--<td>Volume</td>--}}
                        {{--<td><?=number_format($order->getCbm(),3);?> m&sup3;</td>--}}
                    {{--</tr>--}}
				</table>
			</div>
		</div>
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