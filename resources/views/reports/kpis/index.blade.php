@extends('layouts.default')

@section('page-module-menu')

	{{--<li><a href="/report/">Reports</a></li>--}}

	<li><a href="/reports/getDownloads">Downloads</a></li>

	<li><a href="/reports/export_kpis">Exports</a></li>

@stop



@section('page-crumbs')

	<ul id="breadcrumbs" class="breadcrumb">

		<li>
			<i class="icon-home"></i>
			<a href="/dashboard">Dashboard</a>
		</li>

		<li>
            <a href="/reports/">Reports</a>
        </li>
		<li class="current">
            <a href="/reports/kpi">KPI</a>
        </li>

	</ul>



	<ul class="crumb-buttons">

		<li>

			<a href="javascript:void(0);" title=""><i class="icon-calendar"></i><span><?=date('F d, Y \(\K\W:W) H:i:s');?></span></a>

		</li>

	</ul>

@stop



@section('page-header')

	<div class="page-header">

		<div class="page-title">

			<h3>KPI Report</h3>

			<span>Common statistics</span>

		</div>

	</div>

@stop



@section('content')





	<div class="row">

		<div class="col-md-12">

			<div class="widget box">

				<div class="widget-content">

					<table class="table table-striped table-bordered table-hover kpi">

						<thead>

						<tr>

							<th>-</th>
							<th><a href="javascript:void(0)"  class="kpi_previous_last"><?=date("Y")-2;?></a></th>
							<th><a href="javascript:void(0)"  class="kpi_last_year"><?=date("Y")-1;?></a></th>
							<th><a href="javascript:void(0)" class="kpi_current_year"><?=date("Y");?></a></th>

						</tr>

						</thead>

						<tbody>

						<tr class="turnover_link">

							<td ><a href="javascript:void(0)" class="turnover_link">Turnover</a></td>
							<td  class="Turnover_2">{{ number_format($turnover_2,2) }}</td>
							<td  class="Turnover_1">{{ number_format($turnover_1,2) }}</td>
							<td class="Turnover_0">{{ number_format($turnover_0,2) }}</td>
							<input type="hidden" value="" name="turnover" id="turnover">

						</tr>



						<tr>

							<td><a href="javascript:void(0)" class="quantities_link">Quantities</a></td>
							<td class="quanties_2">{{ number_format($order_quantities_2) }}</td>
							<td class="quanties_1">{{ number_format($order_quantities_1) }}</td>
							<td class="quanties_0">{{ number_format($order_quantities_0) }}</td>
							<input type="hidden" value="" name="quantities" id="quantities">
						</tr>



						<tr>

							<td><a href="javascript:void(0)" class="order_count_link">Orders count</a></td>
							<td class="order_count_2">{{ $orders_count_2 }}</td>
							<td class="order_count_1">{{ $orders_count_1 }}</td>
							<td class="order_count_0">{{ $orders_count_0 }}</td>
							<input type="hidden" value="" name="order_count" id="order_count">

						</tr>
						<tr>

							<td><a  href="javascript:void(0)" class="unpaid_link">Unpaid Invoices</a></td>
							<td class="unpaid_invoices_2">{{ number_format($unpaid_invoices_2,2) }}</td>
							<td class="unpaid_invoices_1">{{ number_format($unpaid_invoices_1,2) }}</td>
							<td class="unpaid_invoices_0">{{ number_format($unpaid_invoices_0,2) }}</td>
							<input type="hidden" value="" name="unpaid_invoices" id="unpaid_invoices">

						</tr>



						<tr>

							<td><a href="javascript:void(0)" class="overdue_link">Overdue Invoices</td>
							<td class="overdue_invoices_2">{{ number_format($overdue_invoices_2,2) }}</td>
							<td class="overdue_invoices_1">{{ number_format($overdue_invoices_1,2) }}</td>
							<td class="overdue_invoices_0">{{ number_format($overdue_invoices_0,2) }}</td>
							<input type="hidden" value="" name="overdue_invoices" id="overdue_invoices">
						</tr>

						<tr>
							<td></td>
							<td></td>
						</tr>

						<tr>
							<td><a  href="javascript:void(0)" class="product_link">Products (Active / Inactive)</a></td>
							<td  class ="products"colspan="3">{{ $product_count_active }} / {{ $product_count_inactive }}</td>
							<input type="hidden" value="" name="product" id="product">
						</tr>
						<tr>
							<td><a  href="javascript:void(0)" class="customer_link">Customers (Active / Inactive)</a></td>
							<td class="customers" colspan="3">{{ $customer_count_active }} / {{ $customer_count_inactive }}</td>
							<input type="hidden" value="" name="customer" id="customer">
						</tr>
						<tr>
							<td colspan="4"><strong>All monetary amounts are displayed in   {{$company_currency_code[0]['company']['currency_code']}}</strong></td>
						</tr>
						</tbody>

					</table>

				</div>

			</div>

		</div>

	</div>

	{{--<canvas id="BarChartyearly1"></canvas>--}}
	<!-- <canvas id="BarChartyearly2"></canvas>
		< -canvas id="BarChartyearly3"></canvas-->
	{{--<script>--}}
        {{--$(document).ready(function(){--}}
            {{--// $('#BarChartyearly2').hide();--}}
            {{--// $('#BarChartyearly3').hide();--}}
            {{--var kpi;--}}
            {{--var turnover = new Array();--}}
            {{--turnover = $('#turnover').val();--}}
            {{--var array = new Array();--}}
            {{--var last;--}}
            {{--var current;--}}
            {{--var previous_last;--}}
            {{--for(var i=0;i<$('.kpi tbody tr').length;i++){--}}
                {{--$(".kpi tbody tr:nth-child("+i+")").on('click',function(){--}}
                    {{--var data_label = ['2016','2017','2018'];--}}
                    {{--var previous_last = $(this).find("td:nth-child(2)").text().split(',').join('');--}}
                    {{--// console.log(previous_last);--}}
                    {{--var last  = $(this).find("td:nth-child(3)").text().split(',').join('');--}}
                    {{--var current = $(this).find("td:nth-child(4)").text().split(',').join('');--}}
                    {{--array = [];--}}
                    {{--array.push(previous_last,last,current);--}}
                    {{--$('#turnover').val(array);--}}
                    {{--var kpi  = new Array();--}}
                    {{--var kpi = $('#turnover').val().split(',');--}}
                    {{--$(".kpi tbody tr:nth-child(7)").on('click',function(){--}}
                        {{--var product = 	$('.products').text().split('/');--}}
                        {{--var data_label = ['Active','Inactive'];--}}
                        {{--var  kpi = product;--}}
                        {{--var ctx = document.getElementById('BarChartyearly1').getContext('2d');--}}
                        {{--var myBarChartyearly1 = new Chart(ctx, {--}}
                            {{--type: 'bar',--}}
                            {{--// axisY: {--}}
                            {{--//   "decimalSeparator": ",",--}}
                            {{--//   "thousandSeparator": "."--}}
                            {{--// },--}}
                            {{--showTooltips: false,--}}
                            {{--data: {--}}
                                {{--labels: data_label,--}}
                                {{--datasets: [{--}}
                                    {{--label: "Bar chart For kpi",--}}
                                    {{--backgroundColor: 'rgb(255, 99, 132)',--}}
                                    {{--borderColor: 'rgb(255, 99, 132)',--}}
                                    {{--data: kpi,--}}
                                {{--}]--}}
                            {{--},--}}
                            {{--options: {--}}
                                {{--scales: {--}}
                                    {{--yAxes: [{--}}
                                        {{--ticks: {--}}
                                            {{--// Include a dollar sign in the ticks--}}
                                            {{--callback: function(value, index, values) {--}}
                                                {{--return '$' + value;--}}
                                            {{--}--}}
                                        {{--},gridLines: {--}}
                                            {{--color: "rgba(0, 0, 0, 0)",--}}
                                        {{--}--}}
                                    {{--}],--}}
                                    {{--xAxes: [{--}}
                                        {{--gridLines: {--}}
                                            {{--color: "rgba(0, 0, 0, 0)",--}}
                                        {{--}--}}
                                    {{--}],--}}
                                {{--}--}}
                            {{--}--}}
                        {{--});--}}
                    {{--});--}}
                    {{--$(".kpi tbody tr:nth-child(8)").on('click',function(){--}}
                        {{--var customer = 	$('.customers').text().split('/');--}}
                        {{--var data_label = ['Active','Inactive'];--}}
                        {{--kpi = customer;--}}
                        {{--var ctx = document.getElementById('BarChartyearly1').getContext('2d');--}}
                        {{--var myBarChartyearly1 = new Chart(ctx, {--}}
                            {{--type: 'bar',--}}
                            {{--// axisY: {--}}
                            {{--//   "decimalSeparator": ",",--}}
                            {{--//   "thousandSeparator": "."--}}
                            {{--// },--}}
                            {{--showTooltips: false,--}}
                            {{--data: {--}}
                                {{--labels: data_label,--}}
                                {{--datasets: [{--}}
                                    {{--label: "Bar chart For kpi",--}}
                                    {{--backgroundColor: 'rgb(255, 99, 132)',--}}
                                    {{--borderColor: 'rgb(255, 99, 132)',--}}
                                    {{--data: kpi,--}}
                                {{--}]--}}
                            {{--},--}}
                            {{--options: {--}}
                                {{--scales: {--}}
                                    {{--yAxes: [{--}}
                                        {{--ticks: {--}}
                                            {{--// Include a dollar sign in the ticks--}}
                                            {{--callback: function(value, index, values) {--}}
                                                {{--return '$' + value;--}}
                                            {{--}--}}
                                        {{--},gridLines: {--}}
                                            {{--color: "rgba(0, 0, 0, 0)",--}}
                                        {{--}--}}
                                    {{--}],--}}
                                    {{--xAxes: [{--}}
                                        {{--gridLines: {--}}
                                            {{--color: "rgba(0, 0, 0, 0)",--}}
                                        {{--}--}}
                                    {{--}],--}}
                                {{--}--}}
                            {{--}--}}
                        {{--});--}}
                    {{--});--}}
                    {{--var ctx = document.getElementById('BarChartyearly1').getContext('2d');--}}
                    {{--var myBarChartyearly1 = new Chart(ctx, {--}}
                        {{--type: 'bar',--}}
                        {{--// axisY: {--}}
                        {{--//   "decimalSeparator": ",",--}}
                        {{--//   "thousandSeparator": "."--}}
                        {{--// },--}}
                        {{--showTooltips: false,--}}
                        {{--data: {--}}
                            {{--labels: data_label,--}}
                            {{--datasets: [{--}}
                                {{--label: "Bar chart For kpi",--}}
                                {{--backgroundColor: 'rgb(255, 99, 132)',--}}
                                {{--borderColor: 'rgb(255, 99, 132)',--}}
                                {{--data: kpi,--}}
                            {{--}]--}}
                        {{--},--}}
                        {{--options: {--}}
                            {{--scales: {--}}
                                {{--yAxes: [{--}}
                                    {{--ticks: {--}}
                                        {{--// Include a dollar sign in the ticks--}}
                                        {{--callback: function(value, index, values) {--}}
                                            {{--return '$' + value;--}}
                                        {{--}--}}
                                    {{--},gridLines: {--}}
                                        {{--color: "rgba(0, 0, 0, 0)",--}}
                                    {{--}--}}
                                {{--}],--}}
                                {{--xAxes: [{--}}
                                    {{--gridLines: {--}}
                                        {{--color: "rgba(0, 0, 0, 0)",--}}
                                    {{--}--}}
                                {{--}],--}}
                            {{--}--}}
                        {{--}--}}
                    {{--});--}}
                {{--})--}}
                {{--// console.log(array);--}}
            {{--}--}}
        {{--});--}}
	{{--</script>--}}

@stop