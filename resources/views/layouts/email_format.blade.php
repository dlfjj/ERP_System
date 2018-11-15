<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link href="{{asset('/bootstrap/css/bootstrap.min.css')}}" rel="stylesheet"/>

    {{--<title>American Dunnage</title>--}}
    <style>
        hr{
            width: 40%;
            margin: 0px;
            border-top: 2px solid #0f2452;

        }
        h4{
            color: #0f2452;
            font-weight: 600;
        }
        p{
            font-size: 12px;
            font-family: sans-serif;
        }
        .content{
            padding: 10px 0px 0px 15px;
        }
        .linebreak{
            margin-top: 28px;
            margin-bottom: 10px;
        }
    </style>
</head>
<body style="background-color: transparent;">
    @yield('content')
</body>
</html>