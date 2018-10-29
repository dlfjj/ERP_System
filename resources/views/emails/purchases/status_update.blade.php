<style>

table {
	width: 100%;
	border: 1px solid black;
	text-align: left;
	margin-bottom: 20px;
}

td, th { 
	border: 1px solid #CCC;
	padding: 4px;
}

table thead {
	background: #EEE;
}

</style>

<?php
	$purchase 	= $notification->notifications;
	$system_url = Config::get('app.url'); 
?>

<table cellspacing="0" cellpadding="0" border="0">
	<thead>
		<th>Vendor Code</th>
		<th>Vendor Name</th>
		<th>Required</th>
	</thead>
	<tbody>
		<tr>
			<td>{{ $purchase->vendor->code }}</td>
			<td>{{ $purchase->vendor->company_name }}</td>
			<td>{{ $purchase->date_required }}</td>
		</tr>
	</tbody>
</table>

<table cellspacing="0" cellpadding="0" border="0">
	<thead>
		<th>Partnumber</th>
		<th>Ordered</th>
		<th>Received</th>
		<th>Passed</th>
		<th>Reworked</th>
		<th>Rejected</th>
		<th>Open</th>
	</thead>
	<tbody>
		@foreach($purchase->items as $purchase_item)
			<tr>
				<td>{{ $purchase_item->product->part_number }}</td>
				<td>{{ $purchase_item->quantity }}</td>
				<td>{{ $purchase_item->quantity_delivered }}</td>
				<td>{{ $purchase_item->quantity_passed }}</td>
				<td>{{ $purchase_item->quantity_reworked }}</td>
				<td>{{ $purchase_item->quantity_rejected }}</td>
				<td>{{ $purchase_item->quantity_open }}</td>
			</tr>
		@endforeach
	</tbody>
</table>

<p><a href="{{ $system_url }}purchases/show/{{ $purchase->id }}">Click here to view the complete P.O</a></p>
