<html>
<head>
	<link href="{{ asset('assets/css/pdf.css') }}" rel="stylesheet"/>
</head>
<body>
	<div class="content" style="border:3px solid  #e5e3e3;">
		<div class="div-left" aligh="left">
			<img src="{{public_path('public/global/companies/').$order->company->company_logo}}" height="70" width="150" align="left" class="logo-quotation" >
		</div>
		<h1 class="invoice" align="right" >Performa Invoice  </h1>
		<hr>
		<div class="div-right" align="right" style="width:300px;float: right; padding-top:20px;">

			<table>
				<tr>
					<td>Number</td>
					<td>{{$order->order_no}}</td>
				</tr>
				<tr>
					<td>Date</td>
					<td> {{$order->order_date}}</td>
				</tr>
				<tr>
					<td>shipping</td>
					<td>{{$order->from_port}}</td>
				</tr>

				<tr>
					<td>payment</td>
					<td> {{$payment_terms[0]['name']}}</td>
				</tr>

				<tr>
					<td>Finish date</td>
					<td> {{$order->estimated_finish_date}}</td>
				</tr>

			</table>
			</div>
			<div class="company_details">

				<p align="left"> company Name: {{$customers_details[0]['name']}}</p>
				<p align="left" > Contact name:{{$customers_details[0]['contact_person']}}</p>
				<p align="left" > customer Order {{$customers_details[0]['ids_orders']}}</p>
				<p align="left" > Ship By {{$order->shipping_method}}</p>
			</div>


			<div class="outer-address-content" >

				<table>
					<tr style="background-color: #e5e3e3; color:black;">
						<td align="left">Address</td>
						<td  align="right">Shipping To</td>	
					</tr>
					<tr>
						<td align="left"></td>
						<td  align="right">{{$order->delivery_Address}}</td>	
					</tr>
				</table>
				<table>
					<tr style="background-color: #e5e3e3; color:black;">
						<td align="left">Item</td>
						<td  align="right">QTY</td>	
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
					<tr>
						<td align="left"></td>
						<td  align="right">{{$order->delivery_Address}}</td>	
					</tr>
				</table>
				<table>
					<tr style="background-color: #e5e3e3; color:black;"><td></td></tr>
					<tr><td>
						<p>{{$customers_details[0]['name']}}</p>
						<p>{{$customers_details[0]['ship_to']}}</p>
					</td>
				</tr>
				<tr>
					<td> <p>Subtotal USD:{{$customers_details[0]['currency_code']}}</p>
						<p>Total Amount:{{$order_items[0]['amount_net']}}</p>
					</td></tr>


				</table>
				<h1 align="right" style="border-bottom:3px solid  #e5e3e3; border-bottom-width:13px;"></h1>
				<p  class="company-address">{{$customers_details[0]['bank_info']}}    call on:{{$customers_details[0]['contact_phone']}}</p>
			</div>
		</div>
	</body>
	</html>