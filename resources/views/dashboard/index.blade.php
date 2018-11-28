@extends('layouts.default')

@section('page-crumbs')
	<ul id="breadcrumbs" class="breadcrumb">
		<li class="current">
			<i class="icon-home"></i>
			<a href="/">Dashboard</a>
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
			<span>Hello there, {{ Auth::user()->first_name }} {{ Auth::user()->last_name }}!</span>
		</div>
		<ul class="page-stats">
			<li>
				<div class="summary">
                    @if(Auth::user()->company->company_logo != "")
                        <img src="/public/global/companies/{{ Auth::user()->company->company_logo }}" alt="logo" width="200px"/>
                    @else
                        <img src="/assets/img/logo.png" alt="logo" width="200px"/>
                    @endif
				</div>
			</li>
		</ul>
	</div>
@stop

@section('content')
@stop
