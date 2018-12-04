@extends('layouts.email_format')


@section('content')
    <div class="content">
        <p>{{ $mail_data['mail_body'] }}</p>
        <p>{!! nl2br($signature) !!}</p>
        {{--<img src="{{ asset('public/global/companies/8.png') }}" style="width: 30%;">--}}
        <hr class="linebreak">
    </div>
@endsection
