<!DOCTYPE html>
{{--<html>--}}
{{--<head>--}}
    {{--<meta charset="utf-8">--}}
    {{--<link href="{{ asset('assets/css/pdf.css') }}" rel="stylesheet"/>--}}
    {{--<link href="{{ asset('assets/css/pdf.css') }}" rel="stylesheet"/>--}}
{{--</head>--}}
{{--<body>--}}
{{--<div class="row" style="background-color: #0f2452">--}}
    {{--<div class="col-xs-12">--}}
        {{--<div id="printout_footer_line"></div>--}}
    {{--</div>--}}
{{--</div>--}}
{{--<table style="padding-bottom: 60px;">--}}
    {{--<tbody>--}}
    {{--<tr>--}}
{{--        <td style="width: 500px;"> {!! nl2br($purchase->company->bill_to) !!} </td>--}}
        {{--<td style="width: 200px;" align="center">fuck mate</td>--}}
        {{--<td style="width: 500px; margin-bottom: 10px;" align="right">www.americandunnage.com </td>--}}
    {{--</tr>--}}
    {{--</tbody>--}}
{{--</table>--}}
{{--</body>--}}
{{--</html>--}}
<html>
<head>
    <meta charset="utf-8">
    <link href="{{ asset('assets/css/pdf.css') }}" rel="stylesheet"/>
    <link href="{{ asset('assets/css/pdf.css') }}" rel="stylesheet"/>
    <script>
        function subst() {
            var vars={};
            var x=document.location.search.substring(1).split('&');
            for(var i in x) {var z=x[i].split('=',2);vars[z[0]] = decodeURI(z[1]);}
            var x=['frompage','topage','page','webpage','section','subsection','subsubsection'];
            for(var i in x) {
                var y = document.getElementsByClassName(x[i]);
                for(var j=0; j<y.length; ++j) y[j].textContent = vars[x[i]];
            }
        }
    </script>
</head>
<body style="border:0; margin: 0;" onload="subst()">
{{--<table style="width: 100%; padding-bottom: 60px;">--}}
<table style="width: 100%;">
    <tr>
        <td align="left" style="padding-top: 10px;">{!! nl2br($purchase->company->bill_to) !!}</td>
        {{--<td class="section" align="center"></td>--}}

        <td style="text-align:right">
            Page <span class="page"></span> of <span class="topage"></span>
        </td>
    </tr>
</table>
</body>
</html>