@extends('layouts.default')

@section('page-module-menu')
    @include('products.top_menu')
@stop

@section('page-crumbs')
    @include('products.bread_crumbs')
@stop

@section('page-header')
    @include('products.page_header')
@stop

@section('content')
<div class="row">
			{{ Form::open(array("url"=>"/products/destroy/$product->id","method"=>"post","class"=>"form-inline","id"=>"delete")) }}
			</form>

			<form class="form-inline" id="create" action="/products/create" method="POST">
			</form>

			<form class="form-inline" id="duplicate" action="/products/duplicate/{{$product->id}}" method="POST">
			</form>

	<!--=== Vertical Forms ===-->
	<div class="col-md-12">
		<div class="widget box">
			<div class="widget-header">
				<h4><i class="icon-reorder"></i> Product Details</h4>
			</div>
			<div class="widget-content">
				<form autocomplete="off" enctype="multipart/form-data" id="main" class="form-vertical row-border form-validate" action="" method="POST">
					<div class="form-group">
						<div class="row">
							<div class="col-md-4">
                                <table class="table">
                                    <thead>
                                        <th>Company Sync</th>
                                    </thead>
                                    <tr>
                                        <td>
{{ Form::hidden('product_id',$product->id) }}
Syncing this Product will synchronize all basic Details (General Tab) to all Companies in the System. If the Product does not exist for one or more companies, it will be created.
</td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <input type="hidden" name='action' value="sync" />
                                            <input type="submit" value="SYNC NOW" class="btn btn-sm btn-success" disabled>
                                        </td>
                                    </tr>
                                </table>
							</div>
                        <div class="col-md-8">
                                <table class="table">
                                    <thead>
                                        <th>Company Name</th>
                                        <th>Last Sync</th>
                                    </thead>
@foreach($companies as $company)
<?php

    if(!$slave){
        $last_update = "-";
    } else {
        $last_update = $slave->last_sync;
    }
?>

<tr>
    <td>{{ $company->name }}</td>
    <td>
        {{ $last_update }}
    </td>
</tr>
@endforeach
                                </table>
							</div>

                        </div>
						<div class="form-actions">
                            @if(has_role('products_edit'))
                            @endif
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
	<!-- /Vertical Forms -->
</div>


<div class="row">
<div class="col-md-12">
		<div class="widget box">
			<div class="widget-header">
				<h4><i class="icon-reorder"></i> Product Details</h4>
			</div>
			<div class="widget-content">
				<form autocomplete="off" enctype="multipart/form-data" id="main" class="form-vertical row-border form-validate" action="/products/sync/{{$product->id}}" method="POST">
					<div class="form-group">
						<div class="row">
							<div class="col-md-2">
								<label class="control-label">Serial #</label>
								{{ Form::text('', $product->id, array("class"=>"form-control","readonly")) }}
							</div>
							<div class="col-md-2">
								<label class="control-label">Part Number</label>
                                {{ Form::text('', $product->product_code, array("class"=>"form-control","readonly")) }}
							</div>
							<div class="col-md-2">
                                <label class="control-label">Sync with ERP</label>
                                {{ Form::select('is_erp', array("0" => "No", "1" => "Yes"), $product->is_erp, array("class"=>"form-control")) }}
							</div>
							<div class="col-md-3">
								<label class="control-label">Company Sync?</label><br />
								<div class="make-switch" data-on-label="Yes" data-off-label="No">
									<input type="checkbox" name="company_sync" class="toggle" {{ $product->company_sync == 1 ? 'checked' : '' }} />
								</div>
							</div>
							<div class="col-md-2">
							</div>
						</div>
                        <p>&nbsp;</p>
						<div class="form-actions">
                            @if(has_role('products_edit'))
                                <input type="hidden" name='action' value="save" />
                                <input type="submit" value="Save" class="btn btn-sm btn-success pull-right" disabled>
                            @endif
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
	<!-- /Vertical Forms -->
</div>



<div class="row">
	<div class="col-md-12 no-padding">
		<p class="record_status">Created: {{$product->created_at}} | Created by: {{$user_created}} | Updated: {{$product->updated_at}} | Updated by: {{$user_updated}} | <a href="/products/changelog/{{ $product->id }}">Changelog</a></p>
	</div>
</div>

@stop
