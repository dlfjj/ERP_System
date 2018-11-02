@extends('layouts.default')

@section('page-module-menu')
    @include('purchases.top_menu')
@stop

@section('page-crumbs')
    @include('purchases.bread_crumbs')
@stop

@section('page-header')
    @include('purchases.page_header')
@stop


@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="widget box">
                <div class="widget-header">
                    <h4><i class="icon-reorder"></i> Payment history</h4>
                </div>
                <div class="widget-content no-padding">
                    <form style="margin: 0px; padding: 0px;" action="/purchases/{{$purchase->id}}/receive" method="POST">
                        <table class="table table-hover table-bordered table-highlight-head">
                            <thead>
                            <tr>
                                <th>Date</th>
                                <th>Account</th>
                                <th>Transaction Ref#</th>
                                <th>Remark</th>
                                <th>CUR</th>
                                <th>Amount</th>
                                <th></th>
                            </tr>
                            </thead>
                            <tbody>
                            @if(count($purchase->payments)>0)
                                @foreach($purchase->payments as $payment)
                                    <tr>
                                        <td>
                                            {{$payment->date_created }}
                                        </td>
                                        <td>
                                            {{$payment->account->code}} {{$payment->account->name}}
                                        </td>
                                        <td>
                                            {{$payment->transaction_reference}}
                                        </td>
                                        <td>
                                            {{$payment->description }}
                                        </td>
                                        <td>
                                            {{$payment->currency_code}}
                                        </td>
                                        <td>
                                            {{$payment->amount}}
                                        </td>
                                        <td class="align-right">
                                            <a href="/purchases/payment-delete/{{$payment->id}}" class="btn btn-xs btn-danger conf">X</a>
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="6">No payments recorded so far</td>
                                </tr>
                            @endif
                            </tbody>
                        </table>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modal_journal_entries">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title">Record new Expense</h4>
                </div>
                <form autocomplete="off" enctype="multipart/form-data" id="customer_contact" class="form-validate1" action="/purchases/payment-add/{{ $purchase->id }}" method="POST">
                    <div class="modal-body">
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-6">
                                    {{ Form::text('date_created', date("Y-m-d"), array("class"=>"form-control datepicker")) }}
                                    <span class="help-block">Date</span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-6">
                                    {{ Form::select('bank_account', $select_bank_accounts, "", array("class"=>"form-control")) }}
                                    <span class="help-block">Account</span>
                                </div>
                                <div class="col-md-6">
                                    {!!  $select_accounts !!}
                                    <span class="help-block">Account</span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-6">
                                    {{ Form::select('currency_code', $select_currency_codes, Auth::user()->company->currency_code, array("class"=>"form-control")) }}
                                    <span class="help-block">Currency</span>
                                </div>
                                <div class="col-md-6">
                                    <input type="text" name="amount" class="form-control" value="">
                                    <span class="help-block">Amount</span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-6">
                                    {{ Form::text('transaction_reference', "", array("class"=>"form-control")) }}
                                    <span class="help-block">Transaction Reference</span>
                                </div>
                                <div class="col-md-6">
                                    <input type="text" name="description" class="form-control" value="">
                                    <span class="help-block">Description</span>
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <input type="submit" class="btn btn-primary" value="Submit">
                    </div>
                </form>
            </div>
        </div>
    </div>
@stop
