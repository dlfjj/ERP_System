@extends('layouts.print')


@section('content')

    <header>
        <div class="row">
            <div clas="col-xs-4">
                <img src="{{public_path('public/global/companies/8.png')}}" class="purchase_header">
            </div>
            <div class="col-xs-4 text-center">Purchase Order</div>
            <div class="col-xs-4 text-right">
                <h5><strong>{{ $settings->company_name }}</strong></h5>
                <p style="font-size: 11px;">{{ nl2br($settings['company_bill_to']) }}</p>
            </div>
        </div>
    </header>

@endsection