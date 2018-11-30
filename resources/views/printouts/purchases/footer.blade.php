<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <link href="{{ asset('assets/css/pdf.css') }}" rel="stylesheet"/>
    <link href="{{ asset('assets/css/pdf.css') }}" rel="stylesheet"/>
</head>
<body>
<div class="row" style="background-color: #0f2452">
    <div class="col-xs-12">
        <div id="printout_footer_line"></div>
    </div>
</div>
{{--<div class="row" style="padding-bottom: 60px;">--}}
    {{--<div class="col-xs-5" style="background-color: #0e0e0e">--}}
        {{--<p class="purchase_order_ font" >{!! nl2br($purchase->company->bill_to) !!}</p>--}}
    {{--</div>--}}
    {{--<div class="col-xs-6" style="background-color: #0e90d2">--}}
        {{--<p>www.americandunnage.com</p>--}}
    {{--</div>--}}
{{--</div>--}}
<table style="padding-bottom: 60px;">
    <tbody>
    <tr>
        <td style="width: 500px;"> {!! nl2br($purchase->company->bill_to) !!} </td>
        {{--<td style="width: 200px;" align="center"></td>--}}
        <td style="width: 500px; float: none;" align="right"> www.americandunnage.com </td>
    </tr>
    </tbody>
</table>
</body>
</html>