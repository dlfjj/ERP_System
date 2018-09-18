@layout('layouts.default')

@section('page-crumbs')
	<ul id="breadcrumbs" class="breadcrumb">
		<li>
			<i class="icon-home"></i>
			<a href="/">Dashboard</a>
		</li>
		<li>
			<a href="/products/" title="">Products</a>
		</li>
		<li class="current">
			<a href="/products/{{$product->id}}" title="">Details</a>
		</li>
	</ul>

	<ul class="crumb-buttons">
		<li>
			<a href="javascript:void(0);" class="form-submit-conf" data-target-form="create" title=""><i class="icon-plus"></i><span>Create</span></a>
		</li>
		<li>
			<a href="javascript:void(0);" class="order_update" title=""><i class="icon-save"></i><span>Save</span></a>
		</li>
		<li>
			<a href="/products/print_pis/{{$product->id}}" title=""><i class="icon-print"></i><span>Print</span></a>
		</li>
		<li>
			<a href="/products/{{$product->id}}" class="" title=""><span>Cancel</span></a>
		</li>
		<li class="dropdown">
			<a href="#" title="" data-toggle="dropdown"><i class="icon-tasks"></i><span>More <strong></strong></span><i class="icon-angle-down left-padding"></i></a>
			<ul class="dropdown-menu pull-right">
				<li>
					<a href="javascript:void(0);" class="form-submit-conf" data-target-form="delete" title=""><i class="icon-double-angle-right"></i><span>Delete</span></a>
				</li>
				<li>
					<a href="javascript:void(0);" class="form-submit-conf" data-target-form="duplicate" title=""><i class="icon-double-angle-right"></i><span>Duplicate</span></a>
				</li>
			</ul>
		</li>
		<li>
			<a href="javascript:void(0);" title=""><i class="icon-calendar"></i><span><?=date('F d, Y \(\K\W:W) H:i:s');?></span></a>
		</li>
	</ul>
@stop


@section('page-header')
	<div class="page-header">
		<div class="page-title">
		</div>
		<!-- Page Stats -->
		<ul class="page-stats">
		</ul>
		<!-- /Page Stats -->

	</div>
@stop

@section('content')

<div class="row">
	<div class="col-md-12">
		<div class="widget box">
			<div class="widget-header">
				<h4><i class="icon-reorder"></i> Please confirm</h4>
			</div>
			<div class="widget-content">
				<form enctype="multipart/form-data" id="main" class="form-horizontal row-border form-validate" action="/products/{{$product->id}}/approve" method="POST">
					<div class="form-group">
						<div class="col-md-12">
							<div class="row">
								<div class="col-md-12">
									<input type="hidden" name="product_id" class="" value="{{ $product->id}}">
								</div>
							</div>
							<div class="row">
								<div class="col-md-12">
									<h5>Are you sure you want to approve the Product with Part Number {{$product->part_number}}?</h5>
									<p>This means you have verified this product is unique and all its required details are filled in correctly and orderly.<p>
								</div>
							</div>
						</div>
					</div>

					<div class="row">
						<div class="col-md-12">
						</div>
					</div>
					<div class="form-actions">
						<input type="submit" value="Approve, sure" class="btn btn-success pull-right">
						<a href="/products/{{$product->id}}" class="btn btn-default pull-right">Cancel</a>
					</div>
				</div>
			</form>
		</div>
	</div>
</div>




@stop
