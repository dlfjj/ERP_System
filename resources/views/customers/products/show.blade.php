@extends('layouts.default')

@section('page-module-menu')
    @include('customers.top_menu')
@stop


@section('page-crumbs')
    <ul id="breadcrumbs" class="breadcrumb">
        <li>
            <i class="icon-home"></i>
            <a href="/dashboard">Dashboard</a>
        </li>
        <li>
            <a href="/customers" title="">Customers</a>
        </li>
        <li>
            <a href="/customers/{{$customer->id}}" title="">Details</a>
        </li>
        <li class="current">
            <a href="/customers/products/{{$customer->id}}" title="">Purchased Products</a>
        </li>
    </ul>

    <ul class="crumb-buttons">
        @if(has_role('customers_pricelist'))
            <li><a href="/customer/getPricelist/{{ $customer->id }}" class="" title=""><i class="icon-table"></i><span>Pricelist</span></a></li>
        @endif
        @if(has_role('customers_opos'))
            <li><a href="/customer/getOpos/{{ $customer->id }}" class="" title=""><i class="icon-table"></i><span>OPOS</span></a></li>
        @endif
        <li>
            <a href="javascript:void(0);" title=""><i class="icon-calendar"></i><span><?=date('F d, Y \(\K\W:W) H:i:s');?></span></a>
        </li>
    </ul>
@stop

@section('page-header')
    <?php
    ?>
    <div class="page-header">
        <div class="page-title">
        </div>
        <ul class="page-stats">
            <!-- if(has_role('invoices')) -->
            <li>
                <div class="summary">
                    <span>Outstandings</span>
                    <h3></h3>
                </div>
            </li>
            <!-- endif -->
        </ul>
    </div>
    @stop

    @section('content')

    {{ Form::open(array("url"=>"/customers/destroy/$customer->id","method"=>"post","class"=>"form-inline","id"=>"delete")) }}

    {{--<form class="form-inline" id="create" action="/customers/create" method="POST">--}}
    {{--</form>--}}

    {{--<form class="form-inline" id="duplicate" action="/customers/duplicate/{{$customer->id}}" method="POST">--}}
    {{--</form>--}}

    <div class="row">
        <div class="col-md-12">
            <div class="widget box">
                <div class="widget-header">
                    <h4><i class="icon-reorder"></i> Products bought by this customer</h4>
                    <div class="toolbar no-padding">
                        <div class="btn-group">
                        </div>
                    </div>
                </div>
                <div class="widget-content">
                    <table class="table table-striped table-bordered table-hover table-chooser datatable" data-dataTable='{"aaSorting": [[ 5, "desc" ]]}'>
                        <thead>
                        <tr>
                            <th>Product Code</th>
                            <th>Product Name</th>
                            @foreach($years as $year)
                                <th>{{ $year }}</th>
                            @endforeach
                            <th>-</th>
                        </tr>
                        </thead>
                        <tbody>
                        @if(count($customer_products)>0)
                            @foreach($customer_products as $product_id => $qty_years)
                                <!-- <?php
                                // $product = Product::find($product_id);
                                ?> -->
                                <tr>
                                    <td>{{ $product->product_code }}</td>
                                    <td>{{ substr($product->product_name,0,60) }}</td>
                                    @foreach($qty_years as $qty_year)
                                        <td>{{ $qty_year }}</td>
                                    @endforeach
                                    <td>
                                        <a href="/products/{{ $product->id }}">Show</a>
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="3">Nothing found</td>
                            </tr>
                        @endif
                        </tbody>
                    </table>
                </div>
            </div>
            <!-- /Simple Table -->
        </div>
    </div>


    <div class="row">
        <div class="col-md-12 no-padding">
            <p class="record_status">Created: {{$customer->created_at}} | Created by: {{$created_user}} | Updated: {{$customer->updated_at}} | Updated by: {{$updated_user}} | <a href="/customers/changelog/{{ $customer->id }}">Changelog</a></p>
        </div>
    </div>


@stop
