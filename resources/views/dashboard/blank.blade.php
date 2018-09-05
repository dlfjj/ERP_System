
@extends('layouts.default')

@section('page-crumbs')
	<ul id="breadcrumbs" class="breadcrumb">
		<li class="current">
			<i class="icon-home"></i>
			<a href="/dashboard">Dashboard</a>
		</li>
	</ul>

	<ul class="crumb-buttons">
		<li>
			<a href="javascript:void(0);" title=""><i class="icon-calendar"></i><span><?=date('F d, Y \(\K\W:W) H:i:s');?></span></a>
		</li>
	</ul>
@stop

@section('page-header')
	<div class="page-header">
		<div class="page-title">
			<h3>Dashboard</h3>
			<span>Hello there!</span>
		</div>
		<ul class="page-stats">
			<li>
				<div class="summary">
					<!-- <php
									 $img_path = "{{asset('app.public_folder')}}" . "global/companies/" . Auth::user()->company->company_logo;
											// $img_path = Config::get('app.public_folder') . "global/companies/" . Auth::user()->company->company_logo;
									?>
									if(file_exists($img_path))
											<img src="/public/global/companiesAuth::user()->company->company_logo" alt="logo" width="200px"/>
									else
											<img src="/assets/img/logo.png" alt="logo" width="200px"/>
									endif -->
				</div>

			</li>
		</ul>
	</div>
@stop

@section('content')
@stop
