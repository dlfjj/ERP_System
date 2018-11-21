@extends('layouts.email_format')


@section('content')
    <div class="content">
        <h4 class="text-uppercase">Order {{ str_replace('.','', preg_replace('/[0-9]+/', '',$mail_data['order_status'])) }}</h4>
        <p>{{ $mail_data['mail_body'] }}</p>
        <p>{!! nl2br($signature) !!}</p>
        {{--<img src="{{ asset('public/global/companies/8.png') }}" style="width: 30%;">--}}
        <hr class="linebreak">
    </div>
@endsection


