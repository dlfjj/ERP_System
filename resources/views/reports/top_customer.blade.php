@extends('layouts.default')
@section('page-module-menu')
    <li><a href="/reports">Reports</a></li>
    {{--<li><a href="/reports/downloads">Downloads</a></li>--}}
    <li>
        <a href="/reports/export_top_customers/start_date={{$date_start}}/end_date={{$date_end}}/currency_code={{$currency_code}}"><i class="icon-download-alt"></i>&nbsp Exports Current Table</a>

    </li>
@stop

@section('page-crumbs')
    <ul id="breadcrumbs" class="breadcrumb">
        <li>
            <i class="icon-home"></i>
            <a href="/">Dashboard</a>
        </li>
        <li><a href="/reports/">Reports</a></li>
        <li class="current"><a href="/reports/getTopCustomer">Top 50 Customer</a></li>
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
            <h3>Top 50 Customers</h3>
            <span>Report timeframe is {{ $date_start }} - {{ $date_end }}</span>
        </div>
    </div>
@stop

@section('content')

    <div class="row">
        <div class="col-md-12">
            <div class="widget box">
                <div class="widget-header">
                    <h4><i class="icon-reorder"></i> Query results</h4>
                    {{--<div class="toolbar no-padding">--}}
                        {{--<div class="btn-group">--}}
                            {{--<span class="btn btn-xs widget-collapse"><i class="icon-angle-down"></i></span>--}}
                        {{--</div>--}}
                    {{--</div>--}}
                </div>
                <div class="widget-content">
                    <table class="table table-hover">
                        <thead>
                        <tr>
                            <th class="cell-tight">#</th>
                            <th>Customer Name</th>
                            <th class="cell-tight">Amount gross (<small>{!! $currency_code[0] !!}</small>)</th>
                        </tr>
                        </thead>
                        <tbody>
                        @php
                            $grand_total = 0;
                        @endphp
                        @if(count($results)> 0)
                            @foreach($results as $customer_id => $amount)
                                @php
                                    $customer = App\Models\Customer::findorfail($customer_id);
                                    $grand_total += $amount;
                                    $top++
                                @endphp
                                    @if($loop->count == 51)
                                        @break;
                                    @endif

                                <tr class="stockorder-form-row">
                                    <td class="cell-tight">{{ $top }}</td>
                                    <td><a href="/customers/{{$customer->id}}">{{$customer->customer_name }}</a></td>
                                    <td class="cell-tight">{{ number_format($amount,2) }}</td>


                                </tr>

                            @endforeach
                            <tr style="font-weight: bold;">
                                <td></td>
                                <td>Total:</td>
                                <td>{{ number_format($grand_total,2) }}</td>
                            </tr>

                        @else
                            <tr class="stockorder-form-row">
                                <td class="cell-tight">not found</td>
                            </tr>

                        @endif

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>


    <div class="modal fade" id="modal_set_dates" style="overflow:hidden;">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title">Adjust report parameters</h4>
                </div>
                <form autocomplete="off" enctype="multipart/form-data" class="" action="" method="POST">

                    <div class="modal-body">
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-6">
                                    {{ Form::text('date_start',$date_start, array("class"=>"form-control datepicker")) }}
                                    <span class="help-block">Date start</span>
                                </div>
                                <div class="col-md-6">
                                    {{ Form::text('date_end',$date_end, array("class"=>"form-control datepicker")) }}
                                    <span class="help-block">Date end</span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-6">
                                    {{ Form::hidden('currency_code',Auth::user()->company->currency_code, array("class"=>"form-control")) }}
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
