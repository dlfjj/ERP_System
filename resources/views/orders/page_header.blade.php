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

$stock_status = "";
if($order->status_id == 7 && $order->stock_booked == 0){
    $stock_status = "<img src='/img/circle_orange.png' title='Stock not yet booked' width='24px' />";
}
if($order->stock_booked == '1'){
    $stock_status = "<img src='/img/circle_green.png' title='Stock booked' width='24px' />";
}
?>
<div class="page-header">
    <div class="page-title">
        @if(has_role('orders_edit'))
            @if($order->status == 'DRAFT' || $order->status == 'VOID')
                <a class="btn btn-success btn-lg form-submit-conf" data-target-form="post" href="javascript:void(0);"><i class="icon-th"></i> Post Order</a>
            @endif
        @endif
    </div>
    <!-- Page Stats -->
    <ul class="page-stats">
        <li>
            <div class="summary">
                <span>Order No</span>
                <h3>{{$order->order_no}}</h3>
            </div>
        </li>
        <li>
            <div class="summary">
                <span>Status</span>
                <h3>{{ $order->status->name }}</h3>
            </div>
        </li>
        <li>
            <div class="summary">
                <span>Order Total</span>
                <h3>{{$order->currency_code}} {{$order->total_gross}}</h3>
            </div>
        </li>
        @if(return_company_id()  == 1)
            <li>
                <div class="summary text_center">
                    <span>ERP</span>
                    <h3><img src='/img/circle_red.png' title='ERP Sync Issue, {$order->erp_sync_msg}' width='24px' /></h3>
                </div>
            </li>
        @endif
        <li>
            <div class="summary text_center">
                <span>Stock</span>
                <h3><img src='/img/circle_green.png' title='Stock booked' width='24px' /></h3>
            </div>
        </li>
    </ul>
</div>
