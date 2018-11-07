<?php
$uri_segment = Request::segment(1);
$is_smartphone = false;
$is_internet_explorer = false;

/*
	if (!Session::has('browser_name')){
		$browser = get_browser();
		Session::put('browser_name', $browser->browser);
		Session::put('browser_version', $browser->version);
		Session::put('browser_ismobiledevice', $browser->ismobiledevice);
	}
*/

if(Session::get('browser_name') == 'Internet Explorer'){
    App::abort(500, 'Unsupported Browser Error');
}

//	echo '<pre>'; print_r($browser); echo '</pre>'; die();

?>
        <!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{Auth::user()->company->name}}</title>

    <!-- jQuery UI -->
    {{--<link href="plugins/jquery-ui/jquery-ui-1.10.3.custom.css" rel="stylesheet" type="text/css" />--}}
    <link rel="stylesheet" href="{{ asset('/plugins/jquery-ui/jquery-ui.css') }}"/>

    <!--[if lt IE 9]>
    <link rel="stylesheet" type="text/css" href="plugins/jquery-ui/jquery.ui.1.10.3.ie.css"/>
    <![endif]-->

    <!-- Bootstrap -->
{{--<link href="{{asset('/bootstrap/css/bootstrap.min.css')}}" rel="stylesheet" type="text/css" />--}}

    <!-- Theme -->
    {{--<link href="/assets/css/main.css" rel="stylesheet" type="text/css" />--}}
    <link rel="stylesheet" href="{{ asset('css/main.css') }}" />
{{--    <link rel="stylesheet" href="{{ asset('css/app.css') }}">--}}
    <link href="{{asset('/bootstrap/css/bootstrap.min.css')}}" rel="stylesheet"/>
    <link href="/assets/css/plugins.css" rel="stylesheet" type="text/css" />
    <link href="/assets/css/responsive.css" rel="stylesheet" type="text/css" />
    <link href="/assets/css/icons.css" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="/assets/css/fontawesome/font-awesome.min.css" />
{{--    <link href="{{ asset('/plugins/noty/noty.css') }}" />--}}

    <script src="{{asset('/plugins/chart/Chart.min.js')}}"></script>
    <!--[if IE 7]>
    <link rel="stylesheet" href="/assets/css/fontawesome/font-awesome-ie7.min.css">
    <![endif]-->

    <!--[if IE 8]>
    <link href="/assets/css/ie8.css" rel="stylesheet" type="text/css" />
    <![endif]-->

    <!-- datatable styles -->
    <link rel="stylesheet" href="{{ asset('plugins/datatables/datatables.min.css') }}">



    <style>
        @font-face {
            font-family: 'Open Sans';
            font-style: normal;
            font-weight: 400;
            src: url("/fonts/opensans/OpenSans-Regular.ttf");
        }
        @font-face {
            font-family: 'Open Sans';
            font-style: normal;
            font-weight: 600;
            src: url("/fonts/opensans/OpenSans-Semibold.ttf");
        }
        @font-face {
            font-family: 'Open Sans';
            font-style: normal;
            font-weight: 700;
            src: url("/fonts/opensans/OpenSans-Bold.ttf");
        }
        .form-group .row{
            padding-bottom: 10px;
        }

    </style>

    <!--=== JavaScript ===-->
    <script type="text/javascript" src="/assets/js/libs/jquery-1.10.2.min.js"></script>
    {{--<script src="{{ asset('plugins/jquery-ui/jquery-ui.min.js') }}"></script>--}}
    <script type="text/javascript" src="/plugins/jquery-ui/jquery-ui-1.10.2.custom.min.js"></script>
{{--    <script src="{{ asset('plugins/jquery-ui/jquery-ui-1.10.3.custom.css') }}"></script>--}}

    <!--datepicker-->
    {{--<script type="text/javascript" src="/plugins/daterangepicker/moment.min.js"></script>--}}
    {{--<script type="text/javascript" src="/plugins/daterangepicker/daterangepicker.js"></script>--}}
    {{--<script type="text/javascript" src="/plugins/blockui/jquery.blockUI.min.js"></script>--}}

    <!-- Forms -->
    <script type="text/javascript" src="/assets/js/libs/lodash.compat.min.js"></script>
    <script type="text/javascript" src="/plugins/uniform/jquery.uniform.min.js"></script>
    <script type="text/javascript" src="/plugins/select2/select2.min.js"></script>
    <script type="text/javascript" src="/plugins/bootstrap-switch/bootstrap-switch.min.js"></script>
    <script type="text/javascript" src="/plugins/uniform/jquery.uniform.min.js"></script>
    <script type="text/javascript" src="/plugins/autosize/jquery.autosize.min.js"></script>
    <script type="text/javascript" src="/plugins/tagsinput/jquery.tagsinput.min.js"></script>



    <!-- Bootbox -->
    <script type="text/javascript" src="/plugins/bootbox/bootbox.min.js"></script>

    <!-- Form Validation -->
    <script type="text/javascript" src="/plugins/validation/jquery.validate.min.js"></script>
    <script type="text/javascript" src="/plugins/validation/additional-methods.min.js"></script>

    <!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
    <!--[if lt IE 9]>
    <script src="/assets/js/libs/html5shiv.js"></script>
    <![endif]-->

    <!-- Smartphone Touch Events -->
    <?php if($is_smartphone):?>
    <script type="text/javascript" src="/plugins/touchpunch/jquery.ui.touch-punch.min.js"></script>
    <script type="text/javascript" src="/plugins/event.swipe/jquery.event.move.js"></script>
    <script type="text/javascript" src="/plugins/event.swipe/jquery.event.swipe.js"></script>
<?php endif;?>

<!-- General -->
    <script type="text/javascript" src="/assets/js/libs/breakpoints.js"></script>
    <script type="text/javascript" src="/plugins/respond/respond.min.js"></script> <!-- Polyfill for min/max-width CSS3 Media Queries (only for IE8) -->
    <script type="text/javascript" src="/plugins/cookie/jquery.cookie.min.js"></script>
    <script type="text/javascript" src="/plugins/slimscroll/jquery.slimscroll.min.js"></script>
    <script type="text/javascript" src="/plugins/slimscroll/jquery.slimscroll.horizontal.min.js"></script>

    <!-- Page specific plugins -->
    <!-- Charts -->
    <!--[if lt IE 9]>
    <script type="text/javascript" src="/plugins/flot/excanvas.min.js"></script>
    <![endif]-->

    <!-- Noty -->
    <script type="text/javascript" src="/plugins/noty/jquery.noty.js"></script>
    {{--<script src="{{ asset('/plugins/noty/noty.js') }}"></script>--}}
    <script type="text/javascript" src="/plugins/noty/layouts/top.js"></script>
    <script type="text/javascript" src="/plugins/noty/themes/default.js"></script>




    <!-- DataTables -->
    {{--<script type="text/javascript" src="/plugins/datatables/jquery.dataTables.min.js"></script>--}}
    <script  src="{{ asset('plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script type="text/javascript" src="/plugins/datatables/tabletools/TableTools.min.js"></script>
    {{--<script type="text/javascript" src="/plugins/datatables/colvis/ColVis.min.js"></script>--}}
    {{--<script type="text/javascript" src="/plugins/datatables/columnfilter/jquery.dataTables.columnFilter.js"></script>--}}
    <script src="{{ asset('plugins/datatables/DT_bootstrap.js') }}"></script>

    <!-- App -->

    <script src="{{ asset('assets/js/app.js') }}"></script>
    {{--<script type="text/javascript" src="/assets/js/plugins.js"></script>--}}
    <script src="{{ asset('/js/plugins.js') }}"></script>
    <script type="text/javascript" src="/assets/js/plugins.form-components.js"></script>
    <script type="text/javascript" src="/plugins/fileinput/fileinput.js"></script>

    <!--?php if($uri_segment == 'user_calendar'):?-->
    {{--<script type="text/javascript" src="/plugins/fullcalendar/fullcalendar.min.js"></script>--}}
    {{--<script type="text/javascript" src="/assets/js/demo/pages_calendar.js"></script>--}}
    <!--?php endif;?>


        <!?php if($uri_segment == 'orders'):?-->
    <!--?php endif;?>
    	<!?php if($uri_segment == 'infopages' || $uri_segment == 'webshop_settings'):?-->
    {{--<script type="text/javascript" src="/js/infopages.js"></script>--}}
    <!--?php endif;?>
    	<!?php if($uri_segment == 'products'):?=-->
    {{--<script type="text/javascript" src="/js/products.js"></script>--}}
    <!--?php endif;?-->



    <script>
        $(document).ready(function(){
            "use strict";

            App.init(); // Init layout and core plugins
            Plugins.init(); // Init all plugins
            FormComponents.init(); // Init all form-specific plugins
        });
    </script>
</head>

<body>

{{--<div id="dvLoading">--}}
{{--    <img src="{{asset('/assets/img/loader.gif')}}" id="loading_image">--}}
    <!-- <input type="hidden" value="" class="loading_img"> -->
{{--</div>--}}
@if (Session::has('flash_error'))
<script type="text/javascript">
    noty({
        text: "{{ Session::get('flash_error') }}",
        type: 'error',
        timeout: 500
    });
</script>
@endif
@if (Session::has('flash_success'))
    <script type="text/javascript">
        noty({
            text: "{{ Session::get('flash_success') }}",
            type: 'success',
            timeout: 500
        });
    </script>
@endif

<!-- Header -->
<header class="header navbar navbar-fixed-top" role="banner">
    <!-- Top Navigation Bar -->
    <div class="container-fluid">


        <!-- Sidebar Toggler -->
        {{--<a href="#" class="toggle-sidebar"><i class="icon-reorder"></i></a>--}}
        <!-- /Sidebar Toggler -->

        <!-- Top Left Menu -->
        <ul class="nav navbar-nav navbar-left hidden-xs hidden-sm">
            @section('page-module-menu')
                {{--<li class="text-center">--}}
                {{--<li class="text-center" style="font-size: 15px; background: #0f2452; margin-top:10px; padding-left: 10px; padding-right: 10px;">--}}
                    {{--AMERICAN DUNNAGE INC.--}}
                {{--</li>--}}


        </ul>
            @show

        </ul>
        <!-- /Top Left Menu -->

        <!-- Top Right Menu -->
        <ul class="nav navbar-nav navbar-right">
            <!-- Notifications -->
            <li class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                    <i class="icon-warning-sign"></i>
                    <?php if(count($errors->all())>0):?>
                    <span class="badge"><?=count($errors->all());?></span>
                    <?php endif;?>
                </a>
                <ul class="dropdown-menu extended notification">
                    <li class="title">
                        <p>You have <?=count($errors->all());?> new notifications</p>
                    </li>
                    <?php foreach ($errors->all() as $message):?>
                    <li>
                        <a href="javascript:void(0);">
                            <span class="label label-danger"><i class="icon-warning-sign"></i></span>
                            <span class="message"><?=$message;?></span>
                        </a>
                    </li>
                    <?php endforeach;?>

                    <li>
                        <a href="javascript:void(0);">
                            <span class="label label-success"><i class="icon-plus"></i></span>
                            <span class="message">New user registration.</span>
                            <span class="time">1 mins</span>
                        </a>
                    </li>
                    <li class="footer">
                        <a href="javascript:void(0);">View all notifications</a>
                    </li>
                    -->
                </ul>
            </li>

            <!-- .row .row-bg Toggler -->
            <li>
                <a href="#" class="dropdown-toggle row-bg-toggle">
                    <i class="icon-resize-vertical"></i>
                </a>
            </li>


            <!-- User Login Dropdown -->
            <li class="dropdown user">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                    <!--<img alt="" src="/assets/img/avatar1_small.jpg" />-->
                    <i class="icon-male"></i>
                    @if(Auth::check())
                        <span class="username">{{ Auth::user()->first_name . " " . Auth::user()->last_name }}</span>
                        <i class="icon-caret-down small"></i>
                    @endif
                </a>
                <ul class="dropdown-menu">
                    <li><a href="/userProfiles"><i class="icon-user"></i> My Profile</a></li>
                    <li><a href="/currency_calculator"><i class="icon-calendar"></i> Rates Calculator</a></li><!--remove link convert_currency-->
                    <!-- if(has_role('company_admin')) -->
                    <li><a href="/settings"><i class="icon-tasks"></i> Settings</a></li>
                    <!-- endif -->
                    @if(has_role('admin'))
                    {{--<li><a href="/webshop_settings/getIndex"><i class="icon-tasks"></i> Webshop Settings</a></li>--}}
                    <li><a href="/companies"><i class="icon-tasks"></i> Companies</a></li>
                    <li><a href="/usersList"><i class="icon-tasks"></i> Users</a></li>
                    @endif
                    <li class="divider"></li>
                    <li><a href="/logout"><i class="icon-key"></i> Log Out</a></li>
                </ul>
            </li>
            <!-- /user login dropdown -->
        </ul>
        <!-- /Top Right Menu -->
    </div>
    <!-- /top navigation bar -->

    <!--=== Project Switcher ===-->
    <div id="project-switcher" class="container project-switcher">
        <div id="scrollbar">
            <div class="handle"></div>
        </div>
    </div> <!-- /#project-switcher -->
</header> <!-- /.header -->

<div id="container" class="<?=(Auth::user()->sidebar_visible == 0 ? "sidebar-closed" : "");?>">
    <div id="sidebar" class="sidebar-fixed">
        <div id="sidebar-content">
        <?php $segment = Request::segment(1);?>
        <!--=== Navigation ===-->
            <ul id="nav">
                <li class="<?=($segment == 'dashboard' ? "current" : "");?>">
                    <a href="/dashboard">
                        <i class="icon-dashboard"></i>
                        <div class="sidebar-text">Dashboard</div>
                    </a>
                </li>

                <!-- if(has_role('orders')) -->
                <li class="<?=($segment == 'orders' ? "current" : "");?>">
                    <a href="/orders">
                        <i class="icon-money"></i>
                        <div class="sidebar-text">Orders</div>
                    </a>
                </li>
                <!-- endif -->

                <!-- if(has_role('purchases')) -->
                <li class="<?=($segment == 'purchases' ? "current" : "");?>">
                    <a href="/purchases">
                        <i class="icon-money"></i>
                        <div class="sidebar-text">Purchases</div>
                    </a>
                </li>
                <!-- endif -->

                <!-- if(has_role('products')) -->
                <li class="<?=($segment == 'products' ? "current" : "");?>">
                    <a href="/products">
                        <i class="icon-folder-open-alt"></i>
                        <div class="sidebar-text">Products</div>
                    </a>
                </li>
                <!-- endif -->

                <!-- if(has_role('expenses')) -->
                <li class="<?=($segment == 'expensesdsfsd' ? "current" : "");?>">
                    <a href="/expenses">
                        <i class="icon-money"></i>
                        <div class="sidebar-text">Accounting</div>
                    </a>
                </li>
                <!-- endif -->
                <!-- if(has_role('customers')) -->
                <li class="<?=($segment == 'customers' ? "current" : "");?>">
                    <a href="/customers">
                        <i class="icon-user"></i>
                        <div class="sidebar-text">Customers</div>
                    </a>
                </li>
                <!-- endif -->
                <!-- if(has_role('vendors')) -->
                <li class="<?=($segment == 'vendors' ? "current" : "");?>">
                    <a href="/vendors">
                        <i class="icon-user"></i>
                        <div class="sidebar-text">Vendors</div>
                    </a>
                </li>

                <!-- endif -->

                <!-- if(has_role('reports')) -->
                <li>
                    <a href="/reports">
                        <i class="icon-bar-chart"></i>
                        <div class="sidebar-text">Reports</div>
                    </a>
                </li>
                <!-- endif -->
            </ul>

            <!-- /Navigation -->
            <div class="sidebar-title">
				<span>
					<img style="height: 35px;" src="/img/top_logo.png" alt="logo" />
				</span>
            </div>
            <ul class="notifications demo-slide-in">
            </ul>

        </div>
        <div id="divider" class="resizeable"></div>
    </div>
    <!-- /Sidebar -->

    <div id="content">
        {{--<div class="container" style="padding: 50px 20px 20px 20px;">--}}
        <div class="container">
            <!-- Breadcrumbs line -->
            <div class="crumbs">
                @section('page-crumbs')
                    <ul id="breadcrumbs" class="breadcrumb">
                        <li>
                            <i class="icon-home"></i>
                            <a href="index.html">Dashboard</a>
                        </li>
                        <li class="current">
                            <a href="pages_calendar.html" title="">Users</a>
                        </li>
                    </ul>

                    <ul class="crumb-buttons">
                        <li><a href="charts.html" title=""><i class="icon-signal"></i><span>Statistics</span></a></li>
                        <li class="dropdown"><a href="#" title="" data-toggle="dropdown"><i class="icon-tasks"></i><span>Users <strong>(+3)</strong></span><i class="icon-angle-down left-padding"></i></a>
                            <ul class="dropdown-menu pull-right">
                                <li><a href="form_components.html" title=""><i class="icon-plus"></i>Add new User</a></li>
                                <li><a href="tables_dynamic.html" title=""><i class="icon-reorder"></i>Overview</a></li>
                            </ul>
                        </li>
                        <li class="range"><a href="#">
                                <i class="icon-calendar"></i>
                                <span></span>
                                <i class="icon-angle-down"></i>
                            </a></li>
                    </ul>
                @show
            </div>


            @section('page-header')
                <div class="page-header">
                    <div class="page-title">
                    </div>
                    <ul class="page-stats">
                    </ul>
                </div>
                <!-- /Page Header -->


        @show

        <!--=== Page Content ===-->
            {{--using vuejs components in the future--}}
            <div id="app"></div>
        @yield('content')
        <!-- /Page Content -->
        </div>
        <!-- /.container -->

    </div>
</div>
<script src="{{ asset('bootstrap/js/bootstrap.min.js') }}"></script>
<script src="{{ asset('/js/global.js') }}"></script>
<script src="{{ asset('/js/orders.js') }}"></script>

<script>
    // $(window).unload(function(){
    //     $("#dvLoading").show();
    //     $('#dvLoading').fadeOut(116000);
    // });
    // $(window).load(function(){
    //     $("#dvLoading").hide();
    // })
</script>
{{--<script src="{{ asset('js/app.js') }}" defer></script>--}}
@stack('scripts')
</body>
</html>
