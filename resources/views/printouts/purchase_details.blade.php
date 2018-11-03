<html>
<head>
	<link href="{{ public_path('assets/css/pdf.css') }}" rel="stylesheet" type="text/css" />
</head>
<body>
	<table class="purchase-table"  cellspacing="0" cellpadding="0">

		<tr>
			<td><img src="{{public_path('img/logo.png')}}" height="40" width="100" class="logo"></td>
			<td class="purchase-order-text"><h1 align="center">Purchase Order</h1></td>
			<td>{{$company_details[0]['ship_to']}}</td>
		</tr>
		<tr class="issue-text">
			<td>Issued To </td>
			<td>Deliver To</td>
			<td></td>
			<td></td>
			
		</tr>
		<tr>
			<td>TEST INC.</td>
			<td>{{$purchase->ship_to}}</td>
			<td>Purchase No :{{$purchase->id}}</td>
			
		</tr>
		<tr>
			<td></td>
			<td></td>
			<td>Date Placed : {{$purchase->date_placed}}</td>
		</tr>
	
	</table>
	
	<div class="inner_div">
		<table border="0.5px"   cellspacing="0" cellpadding="0">
			<tr>
				<th>Placed</th>
				<th>Required</th>
				<th>Confirmed</th>
				<th>payment Terms</th>
				<th> Your contact</th>
			</tr>
			
			<tr>
				<td>{{$purchase->date_placed}}</td>
				<td>{{$purchase->date_required}}</td>
				<td>{{$purchase->date_confirmed}}</td>
				<td>{{$purchase->payment_terms}}</td>
				<td>{{$purchase->vendor_contact}}</td>
			</tr>
		</table>
		
		<table border="0.5"  cellspacing="0" cellpadding="0">
			<tr>

				<th align="right">Part</th>
				<th align="left">SKU/Description</th>
				<th>UOM</th>
				<th>QTY</th>
				<th>Net Price </th>
				<th>Net Amount</th>
			</tr>
			<?php
			$purchase_items = $purchase->items->sortBy('sort_no');
			?>
			@if(count($purchase->items)>0)
			@foreach($purchase_items as $oi)
			<tr class="order-form-row">

				<td>
					{{ $oi->product_id }}
				</td>
				<td class="align-right"></td>
				<td class="align-right"></td>
				<td class="align-right">
						@if($oi->quantity % $oi->product->pack_unit == 0)
					{{$oi->quantity}}
					@else
					{{$oi->quantity}} (PU!)
					@endif
				</td>
				<td class="align-right" style="background: #EEE;">
				{{$oi->net_price}}
				</td>
				<td class="align-right">{{ $oi->net_total }}</td>
				<!-- <td class="align-right"></td> -->
				
			</tr>
			
			@endforeach
			@endif
			<tr>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td  class="align-right">sub total</td>
				<td class="align-right"><?php echo  $purchase->gross_total; ?></td>
			</tr>
				<tr>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td  class="align-right">Total Due</td>
				<td class="align-right">{{ $purchase->currency_code }}<?php echo  $purchase->gross_total; ?></td>
			</tr>
		</table>
		<p>ALL GOODS  MUST BE LIKE  APPROVED SAMPLES ALWAYS REFERENCE PO ID ON DELIVERY NOTES .ALL MATTERS MUST BEACHES COMPLAINT . PLEASE CONFIRM PO WITHIN HOURS</p>
	</div>

	<script type="text/javascript" src="/assets/js/libs/jquery-1.10.2.min.js"></script>

	<script type="text/javascript" src="/bootstrap/js/bootstrap.min.js"></script>
</body>
</html>