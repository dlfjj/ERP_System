@extends('layouts.default')
@section('style')
    {{--<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js"></script>--}}
    {{--<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" />--}}
    {{--<script src="https://cdn.datatables.net/1.10.12/js/jquery.dataTables.min.js"></script>--}}
    {{--<script src="https://cdn.datatables.net/1.10.12/js/dataTables.bootstrap.min.js"></script>--}}
    {{--<link rel="stylesheet" href="https://cdn.datatables.net/1.10.12/css/dataTables.bootstrap.min.css" />--}}
    {{--<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>--}}
@endsection

@section('page-module-menu')
    <li><a href="/purchases">Purchases</a></li>
@stop

@section('page-crumbs')
    <ul id="breadcrumbs" class="breadcrumb">
        <li>
            <i class="icon-home"></i>
            <a href="/">Dashboard</a>
        </li>
        <li class="current">
            <a href="/purchases/" title="">Purchases</a>
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
            @if(has_role('purchases_edit'))
                <a class="btn btn-success" href="/purchases/vendorsList"><i class="icon-plus-sign"></i> New Purchase</a>
            @endif
        </div>

        {{--<ul class="page-stats">--}}
        {{--</ul>--}}
    </div>
@stop

@section('content')
    {{--<script type="text/javascript" charset="utf-8">--}}
        {{--$(document).ready(function() {--}}
            {{--return 1;--}}
            {{--$("body").on("click", "tbody tr", function(e){--}}
                {{--//	$(this).closest("tr").remove();--}}
                {{--var target = $(this).find("td").first().text();--}}
                {{--window.location.href = "/purchases/show/"+target;--}}
            {{--});--}}
        {{--});--}}
    {{--</script>--}}
    {{--<form class="form-inline" id="create" action="/purchases/create" method="POST">--}}
    {{--</form>--}}
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                {{--<div class="widget-header">--}}
                    <div class="panel-heading"><i class="icon-repurchase"></i> {{ $purchase_index_msg }}</div>
                    {{--<div class="toolbar no-padding">--}}
                        {{--<div class="btn-group">--}}
                            {{--<span class="btn btn-xs widget-collapse"><i class="icon-angle-down"></i></span>--}}
                        {{--</div>--}}
                    {{--</div>--}}
                {{--</div>--}}
                <div class="panel-body">
                    <table class="table table-hover table-bordered table-striped" id="purchases-table" style="width: 100%;">
                        <thead>
                        <tr>
                            <th class="cell-tight">P.O #</th>
                            <th class="cell-tight">STATUS</th>
                            <th class="cell-tight">PLACED</th>
                            <th class="cell-tight">REQUIRED</th>
                            <th class="cell-tight">VENDOR</th>
                            <th class="cell-tight">CUR</th>
                            <th class="cell-tight">GR TOTAL</th>
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
    <!-- /Normal -->
@stop

@push('scripts')
    <script>
        // jquery getting data for purchase table
        $(function() {
            $('#purchases-table').DataTable({
                "oLanguage": {

                    "sSearch": "<i class='icon-search icon-large table-search-icon'></i>"

                },
                processing: true,
                serverSide: true,
                ajax: '{!! route('purchase/getdata') !!}',
                columns: [
                    { data: 'id', name: 'id' },
                    { data: 'status', name: 'status' },
                    { data: 'date_required', name: 'date_required' },
                    { data: 'date_placed', name: 'date_placed' },
                    { data: 'company_name', name: 'company_name' },
                    { data: 'currency_code', name: 'currency_code' },
                    { data: 'gross_total', name: 'gross_total' },
                    {data: 'action', name: 'action', orderable: false, searchable: false}
                ]
            });
        });
    </script>
@endpush