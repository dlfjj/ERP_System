@extends('layouts.default')

@section('page-crumbs')

	<ul id="breadcrumbs" class="breadcrumb">
		<li>
			<i class="icon-home"></i>
			<a href="/">Dashboard</a>
		</li>
		<li>
			<a href="/purchases/" title="">Purchases</a>
		</li>
		<li>
			<a href="/purchases/{{$purchase->id}}" title="">Details</a>
		</li>
		<li class="current">
			<a href="/purchases/change_vendor/{{$purchase->id}}" title="">Change Vendor</a>
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
			<a class="btn btn-default" href="/purchases/{{ $purchase->id }}">Cancel</a>
		</div>
		<!-- Page Stats -->
		<ul class="page-stats">
			<li>
				<div class="summary">
					<span>Status</span>
					<h3>{{$purchase->status}}</h3>
				</div>
			</li>
			<li>
				<div class="summary">
					<span>Purchase Total</span>
					<h3>{{$purchase->currency_code}} {{$purchase->gross_total}}</h3>
				</div>
			</li>
		</ul>
		<!-- /Page Stats -->
	</div>
@stop

@section('content')
<div class="row">
	<div class="col-md-12">
		<div class="widget box">
			<div class="widget-header">
				<h4><i class="icon-reorder"></i> Please confirm</h4>
			</div>
			<div class="widget-content">
				<h5>Are you sure you want to change the Vendor of This P.O? Be careful what you are doing.</h5>
				<table class="table table-striped table-bordered table-hover" id="vendors-table">
					<thead>
						<tr>
							<th>Vendor Name</th>
							<th class="cell-tight"></th>
						</tr>
					</thead>
					<tbody>
					</tbody>
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
            $('#vendors-table').DataTable({
                "oLanguage": {
                    "sSearch": "<i class='icon-search icon-large table-search-icon'></i>"
                },
                processing: true,
                serverSide: true,
                ajax: '{!! route('change_vendor/getdata',$purchase->id) !!}',
                columns: [
                    { data: 'company_name', name: 'company_name' },
                    {data: 'action', name: 'action', orderable: false, searchable: false}
                ]
            });
        });
    </script>
@endpush

