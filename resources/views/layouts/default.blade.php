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
		<title>{{Auth::user()->company->name}}</title>

		<!-- Bootstrap -->
		<link href="{{asset('/bootstrap/css/bootstrap.min.css')}}" rel="stylesheet" type="text/css" />

		<!-- jQuery UI -->
		<!--<link href="plugins/jquery-ui/jquery-ui-1.10.2.custom.css" rel="stylesheet" type="text/css" />-->
	<!--[if lt IE 9]>
		<link rel="stylesheet" type="text/css" href="plugins/jquery-ui/jquery.ui.1.10.2.ie.css"/>
	<![endif]-->

	<!-- Theme -->
	<link href="/assets/css/main.css" rel="stylesheet" type="text/css" />
	<link href="/assets/css/plugins.css" rel="stylesheet" type="text/css" />
	<link href="/assets/css/responsive.css" rel="stylesheet" type="text/css" />
	<link href="/assets/css/icons.css" rel="stylesheet" type="text/css" />
	<link rel="stylesheet" href="/assets/css/fontawesome/font-awesome.min.css">
<!--
	<link rel="stylesheet" href="{{asset('/froala/css/froala_editor.css')}}">
	<link rel="stylesheet" href="{{asset('/froala/css/froala_style.css')}}">
	<link rel="stylesheet" href="{{asset('/froala/css/plugins/table.min.css')}}">
	<link rel="stylesheet" href="{{asset('/froala/css/plugins/image.min.css')}}">
	<link rel="stylesheet" href="{{asset('/froala/css/plugins/image_manager.min.css')}}"> -->
	<link href="//cdnjs.cloudflare.com/ajax/libs/font-awesome/4.4.0/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
	<script src="{{asset('/plugins/chart/Chart.min.js')}}"></script>
	<!--[if IE 7]>
		<link rel="stylesheet" href="/assets/css/fontawesome/font-awesome-ie7.min.css">
	<![endif]-->

	<!--[if IE 8]>
		<link href="/assets/css/ie8.css" rel="stylesheet" type="text/css" />
	<![endif]-->

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
</style>

<!--=== JavaScript ===-->

<script type="text/javascript" src="/assets/js/libs/jquery-1.10.2.min.js"></script>
<script type="text/javascript" src="/plugins/jquery-ui/jquery-ui-1.10.2.custom.min.js"></script>

<script type="text/javascript" src="/bootstrap/js/bootstrap.min.js"></script>
<script type="text/javascript" src="/assets/js/libs/lodash.compat.min.js"></script>

<script type="text/javascript" src="/js/global.js"></script>

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

	<!--
	<script type="text/javascript" src="/plugins/sparkline/jquery.sparkline.min.js"></script>
	<script type="text/javascript" src="/plugins/flot/jquery.flot.min.js"></script>
	<script type="text/javascript" src="/plugins/flot/jquery.flot.tooltip.min.js"></script>
	<script type="text/javascript" src="/plugins/flot/jquery.flot.resize.min.js"></script>
	<script type="text/javascript" src="/plugins/flot/jquery.flot.time.min.js"></script>
	<script type="text/javascript" src="/plugins/flot/jquery.flot.growraf.min.js"></script>
	<script type="text/javascript" src="/plugins/easy-pie-chart/jquery.easy-pie-chart.min.js"></script>
-->

<script type="text/javascript" src="/plugins/daterangepicker/moment.min.js"></script>
<script type="text/javascript" src="/plugins/daterangepicker/daterangepicker.js"></script>
<script type="text/javascript" src="/plugins/blockui/jquery.blockUI.min.js"></script>


<!-- Noty -->
<script type="text/javascript" src="/plugins/noty/jquery.noty.js"></script>
<script type="text/javascript" src="/plugins/noty/layouts/top.js"></script>
<script type="text/javascript" src="/plugins/noty/themes/default.js"></script>

<!-- Forms -->
<script type="text/javascript" src="/plugins/uniform/jquery.uniform.min.js"></script>
<script type="text/javascript" src="/plugins/select2/select2.min.js"></script>
<script type="text/javascript" src="/plugins/bootstrap-switch/bootstrap-switch.min.js"></script>
<script type="text/javascript" src="/plugins/uniform/jquery.uniform.min.js"></script>
<script type="text/javascript" src="/plugins/autosize/jquery.autosize.min.js"></script>
<script type="text/javascript" src="/plugins/tagsinput/jquery.tagsinput.min.js"></script>

<!-- DataTables -->
<script type="text/javascript" src="/plugins/datatables/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="/plugins/datatables/tabletools/TableTools.min.js"></script>
<script type="text/javascript" src="/plugins/datatables/colvis/ColVis.min.js"></script>
<script type="text/javascript" src="/plugins/datatables/columnfilter/jquery.dataTables.columnFilter.js"></script>
<script type="text/javascript" src="/plugins/datatables/DT_bootstrap.js"></script>

<!-- App -->
<script type="text/javascript" src="/assets/js/app.js"></script>
<script type="text/javascript" src="/assets/js/plugins.js"></script>
<script type="text/javascript" src="/assets/js/plugins.form-components.js"></script>
<script type="text/javascript" src="/plugins/fileinput/fileinput.js"></script>

<!--?php if($uri_segment == 'user_calendar'):?-->
<script type="text/javascript" src="/plugins/fullcalendar/fullcalendar.min.js"></script>
<script type="text/javascript" src="/assets/js/demo/pages_calendar.js"></script>
	<!--?php endif;?>


		<!?php if($uri_segment == 'orders'):?-->
		<script type="text/javascript" src="/js/orders.js"></script>
    <!--?php endif;?>
    	<!?php if($uri_segment == 'infopages' || $uri_segment == 'webshop_settings'):?-->
    	<script type="text/javascript" src="/js/infopages.js"></script>
    <!--?php endif;?>
    	<!?php if($uri_segment == 'products'):?=-->
    	<script type="text/javascript" src="/js/products.js"></script>
    	<!--?php endif;?-->

    	<script>
    		$(document).ready(function(){
    			"use strict";

		App.init(); // Init layout and core plugins
		Plugins.init(); // Init all plugins
		FormComponents.init(); // Init all form-specific plugins
	});
</script>


	<!--
		<script type="text/javascript" src="/assets/js/custom.js"></script>
		<script type="text/javascript" src="/assets/js/demo/charts/chart_filled_blue.js"></script>
		<script type="text/javascript" src="/assets/js/demo/charts/chart_simple.js"></script>
	-->
</head>

<body>

	<div id="dvLoading">
		<img src="{{asset('/assets/img/loader.gif')}}" id="loading_image">
		<!-- <input type="hidden" value="" class="loading_img"> -->
	</div>
	@if (Session::has('flash_error')) -->
	<script type="text/javascript">
		noty({
			text: "{{ Session::get('flash_error') }}",
			type: 'error',
			timeout: 1000
		});
	</script>
	@endif
	@if (Session::has('flash_success'))
	<script type="text/javascript">
		noty({
			text: "{{ Session::get('flash_success') }}",
			type: 'success',
			timeout: 1000
		});
	</script>
	@endif

	<!-- Header -->
	<header class="header navbar navbar-fixed-top" role="banner">
		<!-- Top Navigation Bar -->
		<div class="container">

			<!-- Only visible on smartphones, menu toggle -->
			<!-- <ul class="nav navbar-nav">
				<li class="nav-toggle"><a href="javascript:void(0);" title=""><i class="icon-reorder"></i></a></li>
			</ul> -->

			<!-- Logo -->
			<a class="navbar-brand" href="/">
				<p style="font-size: 11px; margin: 0px; padding: 0px;"></p>
			</a>
			<!-- /logo -->

			<!-- Sidebar Toggler -->
			<a href="#" class="toggle-sidebar"><i class="icon-reorder"></i></a>
			<!-- /Sidebar Toggler -->

			<!-- Top Left Menu -->
			<ul class="nav navbar-nav navbar-left hidden-xs hidden-sm">
				@section('page-module-menu')
				<li>
					AMERICAN DUNNAGE INC.
				</li>
				@show
				 <!-- <li class="dropdown">
					<a href="#" class="dropdown-toggle" data-toggle="dropdown">
						Employees
						<i class="icon-caret-down small"></i>
					</a>
					<ul class="dropdown-menu">
						<li><a href="#"><i class="icon-user"></i> Example #1</a></li>
						<li><a href="#"><i class="icon-calendar"></i> Example #2</a></li>
						<li class="divider"></li>
						<li><a href="#"><i class="icon-tasks"></i> Example #3</a></li>
					</ul>
				</li> -->
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

			<!-- Project Switcher Button -->
				<!--
				<li class="dropdown">
					<a href="#" class="project-switcher-btn dropdown-toggle">
						<i class="icon-folder-open"></i>
						<span>Settings</span>
					</a>
				</li>
			-->

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
					<li><a href="/userprofiles/getIndex"><i class="icon-user"></i> My Profile</a></li>
					<li><a href="#"><i class="icon-calendar"></i> Rates Calculator</a></li><!--remove link convert_currency-->
					<!-- if(has_role('company_admin')) -->
					<li><a href="/settings/getIndex"><i class="icon-tasks"></i> Settings</a></li>
					<!-- endif -->
					<!-- if(has_role('admin')) -->
					<li><a href="/webshop_settings/getIndex"><i class="icon-tasks"></i> Webshop Settings</a></li>
					<li><a href="/company/getIndex"><i class="icon-tasks"></i> Companies</a></li>
					<li><a href="/users/getIndex"><i class="icon-tasks"></i> Users</a></li>
					<!-- endif -->
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

		<div id="frame">
			<ul class="project-list">
				<li>
					<a href="javascript:void(0);">
						<span class="image"><i class="icon-desktop"></i></span>
						<span class="title">Lorem ipsum dolor</span>
					</a>
				</li>
				<li>
					<a href="javascript:void(0);">
						<span class="image"><i class="icon-compass"></i></span>
						<span class="title">Dolor sit invidunt</span>
					</a>
				</li>
				<li class="current">
					<a href="javascript:void(0);">
						<span class="image"><i class="icon-male"></i></span>
						<span class="title">Consetetur sadipscing elitr</span>
					</a>
				</li>
				<li>
					<a href="javascript:void(0);">
						<span class="image"><i class="icon-thumbs-up"></i></span>
						<span class="title">Sed diam nonumy</span>
					</a>
				</li>
				<li>
					<a href="javascript:void(0);">
						<span class="image"><i class="icon-female"></i></span>
						<span class="title">At vero eos et</span>
					</a>
				</li>
				<li>
					<a href="javascript:void(0);">
						<span class="image"><i class="icon-beaker"></i></span>
						<span class="title">Sed diam voluptua</span>
					</a>
				</li>
				<li>
					<a href="javascript:void(0);">
						<span class="image"><i class="icon-desktop"></i></span>
						<span class="title">Lorem ipsum dolor</span>
					</a>
				</li>
				<li>
					<a href="javascript:void(0);">
						<span class="image"><i class="icon-compass"></i></span>
						<span class="title">Dolor sit invidunt</span>
					</a>
				</li>
				<li>
					<a href="javascript:void(0);">
						<span class="image"><i class="icon-male"></i></span>
						<span class="title">Consetetur sadipscing elitr</span>
					</a>
				</li>
				<li>
					<a href="javascript:void(0);">
						<span class="image"><i class="icon-thumbs-up"></i></span>
						<span class="title">Sed diam nonumy</span>
					</a>
				</li>
				<li>
					<a href="javascript:void(0);">
						<span class="image"><i class="icon-female"></i></span>
						<span class="title">At vero eos et</span>
					</a>
				</li>
				<li>
					<a href="javascript:void(0);">
						<span class="image"><i class="icon-beaker"></i></span>
						<span class="title">Sed diam voluptua</span>
					</a>
				</li>
			</ul>
		</div> <!-- /#frame -->
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
						Dashboard
					</a>
				</li>

				<!-- if(has_role('orders')) -->
				<li class="<?=($segment == 'orders' ? "current" : "");?>">
					<a href="/orders/getIndex">
						<i class="icon-money"></i>
						Orders
					</a>
				</li>
				<!-- endif -->
				<!-- if(has_role('purchases')) -->
				<li class="<?=($segment == 'purchases' ? "current" : "");?>">
					<a href="/purchases/get_index">
						<i class="icon-money"></i>
						Purchases
					</a>
				</li>
				<!-- endif -->

				<!-- if(has_role('products')) -->
				<li class="<?=($segment == 'products' ? "current" : "");?>">
					<a href="/product/getIndex">
						<i class="icon-folder-open-alt"></i>
						Products
					</a>
				</li>
				<!-- endif -->

				<!-- if(has_role('expenses')) -->
				<li class="<?=($segment == 'expenses' ? "current" : "");?>">
					<a href="/expenses/getIndex">
						<i class="icon-money"></i>
						Expenses
					</a>
				</li>
				<!-- endif -->


				<!-- if(has_role('customers')) -->
				<li class="<?=($segment == 'customers' ? "current" : "");?>">
					<a href="/customer/getIndex">
						<i class="icon-user"></i>
						Customers
					</a>
				</li>
				<!-- endif -->
				<!-- if(has_role('vendors')) -->
				<li class="<?=($segment == 'vendors' ? "current" : "");?>">
					<a href="/vendor/getIndex">
						<i class="icon-user"></i>
						Vendors
					</a>
				</li>

				<!-- endif -->

				<!-- if(has_role('reports')) -->
				<li>
					<a href="/report/getIndex">
						<i class="icon-bar-chart"></i>
						Reports
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
			<!-- /Breadcrumbs line -->

			<!--=== Page Header ===-->

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
			@yield('content')
			<!-- /Page Content -->
		</div>
		<!-- /.container -->

	</div>
</div>

<script type="text/javascript" src="/froala/js/froala_editor.min.js"></script>
<script type="text/javascript" src="/froala/js/plugins/table.min.js"></script>
<script type="text/javascript" src="/froala/js/plugins/link.min.js"></script>
<script type="text/javascript" src="/froala/js/plugins/lists.min.js"></script>
<script type="text/javascript" src="/froala/js/plugins/video.min.js"></script>
<script type="text/javascript" src="/froala/js/plugins/colors.min.js"></script>
<script type="text/javascript" src="/froala/js/plugins/align.min.js"></script>
<script type="text/javascript" src="/froala/js/plugins/image.min.js"></script>
<script type="text/javascript" src="/froala/js/plugins/image_manager.min.js"></script>

<script type="text/javascript" src="/froala/js/froala_editor.min.js"></script>
<script type="text/javascript" src="/froala/js/plugins/table.min.js"></script>
<script type="text/javascript" src="/froala/js/plugins/link.min.js"></script>
<script type="text/javascript" src="/froala/js/plugins/lists.min.js"></script>
<script type="text/javascript" src="/froala/js/plugins/align.min.js"></script>

<script>
$(window).unload(function(){
	 $("#dvLoading").show();
	 $('#dvLoading').fadeOut(116000);
});
$(window).load(function(){
	$("#dvLoading").hide();
})
</script>
</body>
</html>
