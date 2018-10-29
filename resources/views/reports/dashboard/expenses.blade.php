@layout('layouts.default')

@section('page-module-menu')
	<li><a href="/reports">Reports</a></li>
@stop

@section('page-crumbs')
	<ul id="breadcrumbs" class="breadcrumb">
		<li class="current">
			<i class="icon-home"></i>
			<a href="/">Dashboard</a>
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
			<h3>Expense Details</h3>
			<span>Business KPI's {{ $date_start }} - {{ $date_end }}</span>
		</div>
	</div>
@stop

@section('content')
	<div class="row row-bg"> <!-- .row-bg -->
        <div class="col-sm-6 col-md-3">
			<div class="statbox widget box box-shadow">
				<div class="widget-content">
					<div class="visual cyan">
						<i class="icon-money"></i>
					</div>
					<div class="title">EXPENSES TOTAL</div>
					<div class="value">{{ $currency_code }} {{ number_format($expenses_total,2) }}</div>
				</div>
			</div>
		</div>
    </div>

	<!--=== Statboxes ===-->

<div class="row">
		<div class="col-md-12">
			<div class="widget box">
				<div class="widget-header">
					<h4><i class="icon-reorder"></i> Expense Report by Category</h4>
					<div class="toolbar no-padding">
						<div class="btn-group">
							<span class="btn btn-xs widget-collapse"><i class="icon-angle-down"></i></span>
						</div>
					</div>
				</div>
				<div class="widget-content no-padding">
					<?php
                        $category = ChartOfAccount::where('id',10)->first();
                        $categories = $category->Descendants()->get();
					?>
					<table class="table table-striped table-bordered table-hover">
						<thead>
							<tr>
								<th>Code</th>
								<th>Category</th>
								<th>Amount {{ $currency_code }}</th>
								<th>Percentage %</th>
							</tr>
						</thead>
						<tbody>
                            @foreach($categories as $category)
<?php
    $expense_amount = $category->getExpenses($currency_code,$date_start,$date_end);
    if($expenses_total > 0){
        $expense_percent = $expense_amount / $expenses_total * 100;
    } else {
        $expense_percent = 0;
    }
?>
                                <tr>
                                    <td>{{ $category->code }}</td>
                                    <td>{{ $category->name }}</td>
                                    <td>{{ number_format($expense_amount,2) }}</td>
                                    <td>{{ number_format($expense_percent,2) }}</td>
<td>
</td>
                                </tr>
                            @endforeach
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>




<div class="row">
	<div class="col-md-12">
		<div class="widget box">
			<div class="widget-header">
				<h4><i class="icon-reorder"></i> Please confirm</h4>
			</div>
			<div class="widget-content">
				<form enctype="multipart/form-data" id="main" class="form-horizontal row-border form-validate" action="" method="POST">
					<div class="form-group">
						<div class="col-md-12">
							<div class="row">
								<div class="col-md-2">
									<label class="control-label">Start Date</label>
									{{ Form::text('date_start', $date_start, array("class"=>"form-control datepicker")) }}
								</div>
								<div class="col-md-2">
									<label class="control-label">End Date</label>
									{{ Form::text('date_end', $date_end, array("class"=>"form-control datepicker")) }}
								</div>
								<div class="col-md-2">
									<label class="control-label">Currency</label>
									{{ Form::select('currency_code', $select_currency_codes, $currency_code, array("class"=>"form-control")) }}
								</div>
							</div>
						</div>
					</div>

					<div class="row">
						<div class="col-md-12">
						</div>
					</div>
					<div class="form-actions">
						<input type="submit" value="Recalculate" class="btn btn-success pull-right">
					</div>
				</div>
			</form>
		</div>
	</div>
</div>


<div class="row">
	<div class="col-md-12 no-padding">
		<p class="record_status">Page Generated in {{ $seconds_used }} seconds</p>
	</div>
</div>

@stop
