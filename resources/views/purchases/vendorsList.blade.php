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
        <li class="current">
            <a href="/purchases/create" title="">New Purchase</a>
        </li>
    </ul>

    <ul class="crumb-buttons">
        <li>
            <a href="/purchases" title=""><span>Cancel</span></a>
        </li>
        <li>
            <a href="javascript:void(0);" title=""><i class="icon-calendar"></i><span><?=date('F d, Y \(\K\W:W) H:i:s');?></span></a>
        </li>
    </ul>
@stop

@section('page-header')
    <div class="page-header">
        <div class="page-title">
            <a class="btn btn-default" href="/purchases/"><i class="icon-plus-sign"></i> Cancel</a>
        </div>
    </div>
@stop

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading"><i class="icon-repurchase"></i> Choosing your vendor</div>
                <div class="panel-body">
                    <table class="table table-hover table-bordered table-striped" id="vendors-table">
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
                ajax: '{!! route('vendorsList/getdata') !!}',
                columns: [
                    { data: 'company_name', name: 'company_name' },
                    {data: 'action', name: 'action', orderable: false, searchable: false}
                ]
            });
        });
    </script>
@endpush
