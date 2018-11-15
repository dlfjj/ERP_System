@extends('layouts.email_format')

@section('content')
    <div class="content">
        <h4>Order Quotation</h4>
        <p>{{ $mail_data['mail_body'] }}</p>
        <p>{!! nl2br($signature) !!}</p>
        <img src="{{ asset('public/global/companies/8.png') }}" style="width: 30%;">
        <hr class="linebreak">
    </div>
@endsection

{{--{{ $mail_data['mail_body'] }}--}}

{{--{{ nl2br($signature) }}--}}
