<html>
<head>
<link href="{{ public_path('assets/css/pdf.css') }}" rel="stylesheet" type="text/css" />
	</head>
<body>
	<h1 align="center"> Your order has been confirmed</h1>
	<h2 align="center">Thanks for Your Order </h2>
	<div class="netx" style="border:1px solid black; ">
	<table>
		<tr>
			<td>Order No </td>
			<td>{{$order->order_no}}</td>
		</tr>
		<tr>
			<td>order Date</td>
			<td>{{$order->order_date}}</td>
			
		</tr>
		<tr>
			<td>Estimated Delivery</td>
			<td>{{$order->estimated_finish_date}}</td>
		</tr>
		<tr>
			<td>Delivery address</td>
			<td>{{$order->delivery_address}}</td>
		</tr>
		<tr>
			<td>Customer Email</td>
			<td>{{$order->customer_email}}</td>
		</tr>
		<hr>
		<tr>
			<td> Tax  Amount</td>
			<td>{{$order->tax_total}}</td>
		</tr>
		<tr>
			<td> Shipping amount</td>
			<td>{{$order->shipping_cost_actual}}</td>
		</tr>
		<tr>
			<td> TOtal Amount</td>
			<td>{{$order->total_gross}}</td>
		</tr>
	</table>
</div>

</body>
</html>