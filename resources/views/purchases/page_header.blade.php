
<div class="page-header">
    <div class="page-title">
        @if(Request::segment(2) == "payments")
            <a class="btn btn-success" data-toggle="modal" href="#modal_journal_entries" style="margin-top: 40px"><i class="icon-plus-sign"></i> Add Payment</a>
        @endif

        @if(Request::segment(2) == "show")
            @if(has_role('purchases_edit'))
                @if($purchase->status == 'Draft' || $purchase->status == 'Void')
                    <a class="btn btn-success form-submit-conf" data-target-form="post" href="javascript:void(0);"><i class="icon-th"></i> Post P.O</a>
                @endif
            @endif
        @endif

    </div>

    <ul class="page-stats">
        <li>
            <div class="summary">
                <span>Status</span>
                <h3>{{$purchase->status}}</h3>
            </div>
        </li>
        <li>
            <div class="summary">
                <span>Total / Open</span>
                <h3>{{ $purchase->currency_code }} {{ number_format($purchase->gross_total,2) }} / {{ number_format($purchase->getOpenBalance(),2) }}</h3>
            </div>
        </li>
    </ul>
</div>
