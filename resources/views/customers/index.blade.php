@extends('layouts.default')

@section('page-module-menu')
	<li><a href="/customer/getIndex">Customers</a></li>
	<!-- if(has_role('customers_export')) -->
	<!-- endif -->
@stop

@section('page-crumbs')
	<ul id="breadcrumbs" class="breadcrumb">
		<li>
			<i class="icon-home"></i>
			<a href="/dashboard">Dashboard</a>
		</li>
		<li class="current">
			<a href="/customers" title="">Customers</a>
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
			@if(has_role('customers_edit'))
				<a class="btn btn-success" href="/customers/create"><i class="icon-plus-sign"></i> New Customer</a>
			@endif
		</div>

		<ul class="page-stats">
			<li>
				<div class="summary">
					<span>OUTSTANDINGS</span>
					<h4>{{ $outstanding_balance_currency_code }} {{ number_format($outstanding_balance_amount,2) }}</h4>
				</div>
			</li>
		</ul>

	</div>
@stop


@section('content')
				<div class="row">
					<div class="col-md-12">
						<div class="panel panel-default">
							{{--<div class="">--}}
								<div class="panel-heading"><i class="icon-reorder"></i> Customer Index</div>
								{{--<div class="toolbar no-padding">--}}
									{{--<div class="btn-group">--}}
										{{--<span class="btn btn-xs widget-collapse"><i class="icon-angle-down"></i></span>--}}
									{{--</div>--}}
								{{--</div>--}}
							{{--</div>--}}
							<div class="panel-body">
								<table class="table table-striped table-bordered table-hover"  id="customers-table" >
									<thead>
										<tr>
											<th class="cell-tight">Status</th>
											<th class="cell-tight">Code</th>
											<th>Company Name</th>
											<th>City</th>
											<th>Country</th>
											<th>view</th>
										</tr>
									</thead>
									{{--<tbody>--}}
										{{--@foreach($customers as $customer)--}}
										  {{--<tr>--}}
												{{--<td>{{$customer->status}}</td>--}}
												{{--<td>{{$customer->code}}</td>--}}
												{{--<td>{{$customer->customer_name}}</td>--}}
												{{--<td>{{$customer->inv_city}}</td>--}}
												{{--<td>{{$customer->inv_country}}</td>--}}
												{{--<td><a href="/customers/{{ $customer->id }}" class="bs-tooltip" title="View"><i class="icon-search"></i></a> </td>--}}
											{{--</tr>--}}
										{{--@endforeach--}}

									{{--</tbody>--}}
								</table>
							</div>
						</div>
					</div>
				</div>

@stop
@push('scripts')
	<script>
        // jquery getting data for purchase table
        $(function() {
            $('#customers-table').DataTable({
                "oLanguage": {
                    // "sSearch": "<i class='icon-search icon-large table-search-icon'></i>",
                    "oPaginate": {
                        // "sNext": "<i class='icon-chevron-right icon-large'></i>",
                        // "sPrevious": "<i class='icon-chevron-left icon-large'></i>",
                        // "sFirst ": "<i class='icon-backward icon-large'></i>"
                    }
                },
                "pagingType": "full_numbers",
                processing: true,
                serverSide: true,
                ajax: '{!! route('customers/getdata') !!}',
                columns: [
                    { data: 'status', name: 'status' },
                    { data: 'customer_code', name: 'customer_code' },
                    { data: 'customer_name', name: 'customer_name' },
                    { data: 'inv_city', name: 'inv_city' },
                    { data: 'inv_country', name: 'inv_country' },
                    { data: 'action', name: 'action', orderable: false, searchable: false}
                ]
            });
        });
	</script>
@endpush
