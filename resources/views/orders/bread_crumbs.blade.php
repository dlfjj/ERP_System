<?php
$uri_segment = Request::segment(2);
?>
<ul id="breadcrumbs" class="breadcrumb">
    <li>
        <i class="icon-home"></i>
        <a href="/">Dashboard</a>
    </li>
    <li>
        <a href="/orders/" title="">Orders</a>
    </li>
    <li>
        <a href="/orders/{{$order->id}}" title="">Details</a>
    </li>
    <li class="current">
        <a>{{ $uri_segment }}</a>
    </li>
</ul>

<ul class="crumb-buttons">
    <?php
    if($order->erp_sync_status == 'NEVER'){
        $sync_status = "<img src='/img/circle_orange.png' title='Initial ERP Sync pending' width='24px' />";
    } elseif($order->erp_sync_status == 'SYNCED'){
        $sync_status = "<img src='/img/circle_green.png' title='ERP Sync OK, ERP Order#{$order->erp_order_id}' width='24px' />";
    } elseif($order->erp_sync_status == 'OUTOFSYNC'){
        $sync_status = "<img src='/img/circle_orange.png' title='ERP re-sync pending, ERP Order#{$order->erp_order_id}' width='24px' />";
    } elseif($order->erp_sync_status == 'FAILED'){
        $sync_status = "<img src='/img/circle_red.png' title='ERP Sync Issue, {$order->erp_sync_msg}' width='24px' />";
    } else {
        $sync_status = "<img src='/img/circle_red.png' title='Unknown Sync Status' width='24px' />";
    }
    ?>
    {{--@if(return_company_id()  == 1)--}}
        {{--@if(has_role('orders_edit'))--}}
            {{--@if(has_role('orders_can_sync'))--}}
                {{--@if($order->erp_sync_status == "NEVER" || $order->erp_sync_status == "FAILED")--}}
                    {{--<li>--}}
                        {{--<a href="/erp/sync/{{$order->id}}" class="" title=""><i class="icon-retweet"></i><span>ERP Sync</span></a>--}}
                    {{--</li>--}}
                {{--@else--}}
                    {{--@if(has_role('admin'))--}}
                        {{--<li>--}}
                            {{--<a href="/erp/reset/{{$order->id}}" class="" title=""><i class="icon-retweet"></i><span>ERP Reset</span></a>--}}
                        {{--</li>--}}
                    {{--@endif--}}
                {{--@endif--}}
            {{--@endif--}}
        {{--@endif--}}
        {{--<li>--}}
            {{--<a href="/orders/change-customer/{{$order->id}}" class="" title=""><i class="icon-pencil"></i><span>Change Customer</span></a>--}}
        {{--</li>--}}
    {{--@endif--}}
    <li class="dropdown">
        <a href="#" title="" data-toggle="dropdown"><i class="icon-print"></i><span>Printing</span><i class="icon-angle-down left-padding"></i></a>
             <ul class="dropdown-menu">
            <li><a target="_new" href="/pdf/quotation/{{$order->id}}" class="" title=""><i class="icon-print"></i><span>Quotation</span></a></li>
            <li><a target="_new" href="/pdf/acknowledgement/{{$order->id}}" class="" title=""><i class="icon-print"></i><span>Acknowledgement</span></a></li>
            <li><a target="_new" href="/pdf/order-confirmation/{{$order->id}}" class="" title=""><i class="icon-print"></i><span>Confirmation</span></a></li>
            <li><a target="_new" href="/pdf/proforma_invoice/{{$order->id}}" class="" title=""><i class="icon-print"></i><span>Proforma Invoice</span></a></li>
            <li><a target="_new" href="/pdf/commercial_invoice/{{$order->id}}" class="" title=""><i class="icon-print"></i><span>Commercial Invoice</span></a></li>
            <li><a target="_new" href="/pdf/packing_list/{{$order->id}}" class="" title=""><i class="icon-print"></i><span>Packing List</span></a></li>
           
        </ul>
    </li>
    <li>
        <a href="javascript:void(0);" title=""><i class="icon-calendar"></i><span><?=date('F d, Y \(\K\W:W) H:i:s');?></span></a>
    </li>
</ul>

