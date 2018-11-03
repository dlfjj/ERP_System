<html>
<link href="{{ public_path('assets/css/pdf.css') }}" rel="stylesheet" type="text/css" />
<body>
	<h2 align="center">ORDER ACKNOWLEDGEMENT</h2>

		<table class="acknowledge">
			<tr>
				<td>Customer order Number
				</td>
				<td>{{$order->customer_order_number}}</td>
			</tr>
			<tr>
				<td>oder remarks
				</td>
				<td>{{$order->orde_remarks}}</td>
			</tr>
			<tr>
				<td>Delivry Address
				</td>
				<td>{{$order->delivery_address}}</td>
			</tr>
			<tr>
				<td>Billing Address
				</td>
				<td>{{$order->billing_address}}</td>
			</tr>
			<tr>
				<td>Order Date
				</td>
				<td>{{$order->order_date}}</td>
			</tr>
			<tr>
				<td>Shipping date
				</td>
				<td>{{$order->shipping_date}}</td>
			</tr>
			<tr>
				<td>Shipping date
				</td>
				<td>{{$order->shipping_date}}</td>
			</tr>
			<tr>
				<td>Order Status
				</td>
				<td>{{$order_status[0]['name']}}</td>
			</tr>
			<tr>
				<td>Estimated Arrival date
				</td>
				<td>{{$order->estimated_finish_date}}</td>
			</tr>
			<tr>
				<td>Net Weight
				</td>
				<td>{{$order->net_weight}}</td>
			</tr>
			<tr>
				<td>Shipping Method
				</td>
				<td>{{$order->shipping_method}}</td>
			</tr>
			<tr>
				<td>Tracking id
				</td>
				<td>{{$order->tracking_id}}</td>
			</tr>
			<tr>
				<td>Shipping cost
				</td>
				<td>{{$order->shipping_cost_actual}}</td>
			</tr>
			<tr>
				<td>Total Amount
				</td>
				<td>{{$order->total_gross}}</td>
			</tr>
		</table>
</body>
</html>