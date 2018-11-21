<?php
$uri_segment = Request::segment(2);
?>
<ul id="breadcrumbs" class="breadcrumb">
    <li>
        <i class="icon-home"></i>
        <a href="/">Dashboard</a>
    </li>
    <li>
        <a href="/purchases/" title="">Purchases</a>
    </li>
    <li>
        <a href="/purchases/{{$purchase->id}}" title="">Details</a>
    </li>
    @if($uri_segment != "" && $uri_segment != "show")
        <li class="current">
            <a href="/purchases/{{$uri_segment}}" title="">{{ $uri_segment }}</a>
        </li>
    @endif
</ul>
<ul class="crumb-buttons">
    <li>
        <a href="/purchases/change-vendor/{{$purchase->id}}" class="" title=""><i class="icon-pencil"></i><span>Change Vendor</span></a>
    </li>
    <li>
        <a href="/purchases/change-status/{{$purchase->id}}" class="" title=""><i class="icon-pencil"></i><span>Change Status</span></a>
    </li>
    <li>
        {{--<a href="/purchases/duplicate/{{$purchase->id}}" class="conf" title=""><i class="icon-print"></i><span>Duplicate</span></a>--}}
    </li>
    <li>
        <a target="_new" href="/pdf/purchase-pdf/{{$purchase->id}}" class="" title=""><i class="icon-print"></i><span>Print</span></a>
    </li>
    <li>
        <a href="javascript:void(0);" title=""><i class="icon-calendar"></i><span><?=date('F d, Y \(\K\W:W) H:i:s');?></span></a>
    </li>
</ul>
