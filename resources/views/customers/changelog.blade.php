@layout('layouts.default')

@section('page-module-menu')
	<li><a href="/customers/show/{{$customer->id}}">General</a></li>
	<li><a href="/customers/history/{{$customer->id}}">History</a></li>
@stop

@section('page-crumbs')
	<ul id="breadcrumbs" class="breadcrumb">
		<li>
			<i class="icon-home"></i>
			<a href="/">Dashboard</a>
		</li>
		<li>
			<a href="/customers/" title="">Customers</a>
		</li>
		<li>
			<a href="/customers/show/{{$customer->id}}" title="">Details</a>
		</li>
		<li class="current">
			<a href="/customers/changelog/{{$customer->id}}" title="">Changelog</a>
		</li>
	</ul>

	<ul class="crumb-buttons">
		<li>
			<a href="javascript:void(0);" title=""><i class="icon-calendar"></i><span><?=date('F d, Y \(\K\W:W) H:i:s');?></span></a>
		</li>
	</ul>
@stop

@section('page-header')
<?php
		if(has_role('invoices')){
			$invoices_amount = 0;
			$invoices = Invoice::where('status','!=','VOID')
				->where('status','!=','PAID')
				->where('customer_id',$customer->id)
				->get();

			foreach($invoices as $invoice){
				$invoices_amount += convert_currency($invoice->currency_code,"USD",$invoice->total,date("Y-m-d"));	
				foreach($invoice->payments as $payment){
					$invoices_amount -= convert_currency($payment->currency_code,"USD",$payment->amount,date("Y-m-d"));	
				}
			}
		}
?>
	<div class="page-header">
		<div class="page-title">
		</div>
		<ul class="page-stats">
			@if(has_role('invoices'))
			<li>
				<div class="summary">
					<span>Outstandings</span>
					<h3>{{number_format($invoices_amount,2)}} USD</h3>
				</div>
			</li>
			@endif
		</ul>
	</div>

@stop

@section('content')

<div class="row">
	<div class="col-md-12">
		<div class="widget box">
			<div class="widget-header">
				<h4><i class="icon-reorder"></i> Changelog</h4>
				<div class="toolbar no-padding">
					<div class="btn-group">
					</div>
				</div>
			</div>
			<div class="widget-content">
				<table class="table table-hover">
					<thead>
						<tr>
							<th>Timestamp</th>
							<th>Username</th>
							<th>Fieldname</th>
							<th>Old Value</th>
							<th>New Value</th>
							<th>Message</th>
							<th class="align-right"></th>
						</tr>
					</thead>
					<tbody>
					@foreach(Changelog::where('parent_model','Customer')->where('parent_id',$customer->id)->get() as $history)
						<tr>
								<td>{{$history->created_at}}</td>
								<td>{{$history->user->username }}</td>
								<td>{{$history->field_name}}</td>
								<td>{{nl2br($history->old_value)}}</td>
								<td>{{nl2br($history->new_value)}}</td>
								<td>{{nl2br($history->message)}}</td>
								<td class="align-right">
								</td>
						</tr>
					@endforeach
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>

@stop
