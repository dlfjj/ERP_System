@extends('layouts.default')
@section('page-module-menu')
	<li><a href="/reports/createExpensesExcel"><i class="icon-download-alt"></i>&nbsp Exports Current Table</a></li>
@stop

@section('page-crumbs')


	<ul id="breadcrumbs" class="breadcrumb">
		<li>
			<i class="icon-home"></i>
			<a href="/">Dashboard</a>
		</li>
		<li>
			<a href="/reports">Reports</a>
		</li>
		<li class="current">
			<a href="/dashboards/reports/getExpensesByCategory">Expenses</a>
		</li>
	</ul>

	<ul class="crumb-buttons">
		<li><a href="#modal_set_dates" data-toggle="modal" class="" title=""><i class="icon-cog"></i><span>Report Scope</span></a></li>
		<li>
			<a href="javascript:void(0);" title=""><i class="icon-calendar"></i><span><?=date('F d, Y \(\K\W:W) H:i:s');?></span></a>
		</li>
	</ul>
@stop

@section('page-header')
	<div class="page-header">
		<div class="page-title">
			<h3>Reports</h3>
			<span>Expenses for period {{ $date_start }} - {{ $date_end }}</span>
		</div>

		<ul class="page-stats">
			<li>
				<div class="summary">
					<span></span>
					<h3></h3>
				</div>
			</li>
		</ul>


	</div>
@stop

@section('content')

	<!--=== Page Content ===-->
	<div class="row">
		<!--=== Invoice ===-->
		<div class="col-md-12">
			<div class="widget invoice">
				<div class="widget-header">
					<div class="pull-left">
					</div>
					<div class="pull-right">
					</div>
				</div>
				<div class="widget-content">
					<div class="row">
						<div class="col-md-12">
							<table class="table table-hover table-bordered">
								<thead>
								<tr>
									<th style="width: 200px;">Category</th>
									<th>Details</th>
									<th class='text-right'>Amount</th>
								</tr>
								</thead>
								<tbody>
                                @php
                                $expenses 	= App\Expense::where('company_id', return_company_id())
                                    ->where('date_created','>=',$date_start)
                                    ->where('date_created','<=',$date_end)
                                    ->where('amount_conv',0)
                                    ->get();

                                foreach($expenses as $e){
                                    $e->amount_conv 	= convert_currency($e->currency_code,"USD",$e->amount, $e->date_created);
                                    $e->save();
                                }
                                unset($expenses);

                                $category_ids = App\ChartOfAccount::where('company_id', return_company_id())
                                    ->where('type','Expense')
                                    ->pluck('id');

                                $expense_total = App\Expense::where('company_id', return_company_id())
                                    ->where('date_created','>=',$date_start)
                                    ->where('date_created','<=',$date_end)
                                    ->whereIn('account_id', $category_ids)
                                    ->sum('amount_conv')
                                ;
                                @endphp

								@foreach($categories as $category)
                                    @php
                                    $expenses = App\Expense::where('account_id',$category->id)
                                        ->where('company_id', return_company_id())
                                        ->where('date_created','>=',$date_start)
                                        ->where('date_created','<=',$date_end)
                                        ->whereIn('account_id', $category_ids)
                                        ->get();

                                    $category_total = App\Expense::where('account_id',$category->id)
                                        ->where('company_id', return_company_id())
                                        ->where('date_created','>=',$date_start)
                                        ->where('date_created','<=',$date_end)
                                        ->whereIn('account_id', $category_ids)
                                        ->sum('amount_conv');
                                    @endphp
									<tr class="nohide" style="font-weight: 500;">
										<td>{{ $category->name }}</td>
										<td></td>
										<td class='text-right'>
											$ {{ number_format($category_total,2) }}
										</td>
									</tr>
									@foreach($expenses as $expense)
										<tr class="dohide">
											<td></td>
											<td>{{ $expense->description }}</td>
											<td class='text-right'>
												$ {{ number_format($expense->amount_conv,2) }}
											</td>
										</tr>
									@endforeach
								@endforeach
								<tr class='nohide' style="border-top: 2px solid #999;">
									<td></td>
									<td></td>
									<td class="text-right" style="font-size: 1.1em;">
										<strong>Total:</strong> {{ Auth::user()->company->currency_symbol }} {{ number_format($expense_total,2)}}
									</td>
								</tr>
								</tbody>
							</table>
						</div>
					</div>

					<div class="row padding-top-10px">
						<div class="col-md-6">
						</div>

						<div class="col-md-6 align-right">
							<div class="buttons">
							</div>
						</div>
					</div> <!-- /.row -->
				</div>
			</div> <!-- /.widget box -->
		</div> <!-- /.col-md-12 -->
		<!-- /Invoice -->
	</div> <!-- /.row -->
	<!-- /Page Content -->

	<div class="modal fade" id="modal_set_dates" style="overflow:hidden;">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h4 class="modal-title">Adjust Report Paramenters</h4>
				</div>
				<form autocomplete="off" enctype="multipart/form-data" class="" action="" method="POST">

					<div class="modal-body">
						<div class="form-group">
							<div class="row">
								<div class="col-md-6">
									{{ Form::text('date_start',$date_start, array("class"=>"form-control datepicker")) }}
									<span class="help-block">Date end</span>
								</div>
								<div class="col-md-6">
									{{ Form::text('date_end',$date_end, array("class"=>"form-control datepicker")) }}
									<span class="help-block">Date end</span>
								</div>
							</div>
						</div>

					</div>

					<div class="modal-footer">
						<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
						<input type="submit" class="btn btn-primary" value="Submit">
					</div>

				</form>

			</div>
		</div>
	</div>





@stop
