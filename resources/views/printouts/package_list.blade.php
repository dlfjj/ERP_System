<html>
<head>
	<link href="{{ public_path('assets/css/commercial_pdf.css') }}" rel="stylesheet" type="text/css" />
</head>
<body>
	<div class="content" style="border:3px solid grey;">
		<div class="div-left" aligh="left">
			<img src="{{public_path('public/global/companies/').$order->company->company_logo}}" height="70" width="150" align="left" class="logo-quotation" >
		</div>
		<h1 class="invoice" align="right">Packing List  </h1>
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
					<td>Shipping date</td>
					<td> {{$order->estimated_finish_date}}</td>
				</tr>
				<tr>
					<td> container Number: </td>
					<td>{{$payment_terms[0]['name']}}</td>
				</tr>

			</table>
			<!-- <div class="order">
				<p align="right"> Number: {{$order->order_no}}</p>
				<p align="right" > Date: {{$order->order_date}}</p>
				<p align="right" > shipping date: {{$order->shipping_date}}</p>
				<p align="right" > container Number: {{$payment_terms[0]['name']}}</p>
			</div> -->
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
					<td  align="right">Unit </td>	
					<td  align="right">Unit/Box  </td>	
					<td  align="right">Pkg</td>	
					<td  align="right">N.W(kg)</td>
					<td  align="right">G.W(kg)</td>
				</tr>
				<tr>
					<td align="left"></td>
					<td  align="right">{{$order->delivery_Address}}</td>	
				</tr>
				<tr >
					<td align="left">{{$order_items[0]['product_name']}}</td>
					<td  align="right">{{$order_items[0]['quantity']}}</td>	
					<td  align="right">pallette</td>	
					<td  align="right">{{$order_items[0]['units_per_pallette']}}  </td>	
					<td  align="right">{{$order_items[0]['pack_unit']}}</td>	
					<td  align="right">{{$order_items[0]['net_weight']}}  </td>	
					<td  align="right">{{$order_items[0]['gross_weight']}} </td>	
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