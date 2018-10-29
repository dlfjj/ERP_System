@extends('layouts.default')

@section('page-module-menu')
    @include('vendors.top_menu') 
@stop


@section('page-crumbs')
    @include('vendors.bread_crumbs') 
@stop

@section('page-header')
    @include('vendors.page_header') 
@stop


@section('content')

@if(has_role('orders'))
<div class="row">
	<div class="col-md-12">
		<div class="widget box">
			<div class="widget-header">
				<h4><i class="icon-reorder"></i> Purchases</h4>
				{{--<div class="toolbar no-padding">--}}
					{{--<div class="btn-group">--}}
						{{--<span class="btn btn-xs widget-collapse"><i class="icon-angle-down"></i></span>--}}
					{{--</div>--}}
				{{--</div>--}}
			</div>
			<div class="panel-body">
				<table class="table table-striped table-bordered table-hover" id="vendor_purchase_history_table">
					<thead>
						<tr>
							<th class="cell-tight">P.O ID</th>
							<th>Status</th>
							<th>Placed</th>
							<th>Part Number</th>
							<th>CUR</th>
							<th>Amount</th>
							<th>-</th>
						</tr>
					</thead>
					<tbody>
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>
@endif

@stop

@push('scripts')
    <script>
        // jquery getting data for purchase table
        $(function() {
            $('#vendor_purchase_history_table').DataTable({
                "oLanguage": {

                    // "sSearch": "<i class='icon-search icon-large table-search-icon'></i>"

                },
                processing: true,
                serverSide: true,
                ajax: '{!! route('history/getdata',[$vendor->id]) !!}',
                columns: [
                    { data: 'id', name: 'id' },
                    { data: 'status', name: 'status' },
                    { data: 'date_placed', name: 'date_placed' },
                    { data: 'product_code', name: 'products.product_code' },
                    { data: 'currency_code', name: 'currency_code'},
                    { data: 'gross_total', name: 'purchase_items.gross_total' },
                    // { data: 'total_gross', name: 'total_gross' },
                    {data: 'action', name: 'action', orderable: false, searchable: false}
                ]
            });
        });

    </script>
@endpush
