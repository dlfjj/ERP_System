<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <link href="{{asset('/bootstrap/css/bootstrap.min.css')}}" rel="stylesheet"/>
    {{--<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">--}}
    {{--<link rel="stylesheet" href="{{ asset('css/main.css') }}" />--}}
    <link href="{{ asset('assets/css/pdf.css') }}" rel="stylesheet"/>
    <style>
        @import url(http://fonts.googleapis.com/css?family=Bree+Serif);
    </style>
</head>
<body>
    @yield('content')
</body>
</html>