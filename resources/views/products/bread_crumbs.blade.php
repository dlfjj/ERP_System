<?php
    $uri_segment = Request::segment(2);
?>
<ul id="breadcrumbs" class="breadcrumb">
    <li>
        <i class="icon-home"></i>
        <a href="/">Dashboard</a>
    </li>
    <li>
        <a href="/products/" title="">Products</a>
    </li>
    <li>
        <a href="/products/{{$product->id}}" title="">Details</a>
    </li>
    @if($uri_segment != "" && $uri_segment != "show")
    <li class="current">
        <a href="/products/{{$uri_segment}}" title="">{{ $uri_segment }}</a>
    </li>
    @endif
</ul>
<ul class="crumb-buttons">
    @if($product->parent_id != null)
    <li>
        <a href="javascript:void(0);" class="basic-alert" rel="This Product is managed by OEMSERV. This means that you cannot change fields other than the localized web description, prices and status." title=""><i class="icon-warning-sign"></i><span>MANAGED</span></a>
    </li>
    @endif
    @if(has_role('products_edit') && $product->company_id == 1)
        <li>
            <a href="javascript:void(0);" class="form-submit-conf" data-target-form="duplicate" title=""><i class="icon-double-angle-right"></i><span>Duplicate</span></a>
        </li>
    @endif
    <li>
        <a href="javascript:void(0);" title=""><i class="icon-calendar"></i><span><?=date('F d, Y \(\K\W:W) H:i:s');?></span></a>
    </li>
</ul>
