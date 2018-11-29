<table class="table table-hover table-bordered">
    <thead>
    <tr>
        <th style="width: 200px;">Category</th>
        <th>Details</th>
        <th class='text-right'>Amount</th>
    </tr>
    </thead>
    <tbody>
    @php
        $expenses 	= App\Expense::where('company_id', return_company_id())
            ->where('date_created','>=',$date_start)
            ->where('date_created','<=',$date_end)
            ->where('amount_conv',0)
            ->get();

        foreach($expenses as $e){
            $e->amount_conv 	= convert_currency($e->currency_code,"USD",$e->amount, $e->date_created);
            $e->save();
        }
        unset($expenses);

        $category_ids = App\ChartOfAccount::where('company_id', return_company_id())
            ->where('type','Expense')
            ->pluck('id');

        $expense_total = App\Expense::where('company_id', return_company_id())
            ->where('date_created','>=',$date_start)
            ->where('date_created','<=',$date_end)
            ->whereIn('account_id', $category_ids)
            ->sum('amount_conv')
        ;
    @endphp

    @foreach($categories as $category)
        @php
            $expenses = App\Expense::where('account_id',$category->id)
                ->where('company_id', return_company_id())
                ->where('date_created','>=',$date_start)
                ->where('date_created','<=',$date_end)
                ->whereIn('account_id', $category_ids)
                ->get();

            $category_total = App\Expense::where('account_id',$category->id)
                ->where('company_id', return_company_id())
                ->where('date_created','>=',$date_start)
                ->where('date_created','<=',$date_end)
                ->whereIn('account_id', $category_ids)
                ->sum('amount_conv');
        @endphp
        <tr class="nohide" style="font-weight: 500;">
            <td>{{ $category->name }}</td>
            <td></td>
            <td class='text-right'>
                $ {{ number_format($category_total,2) }}
            </td>
        </tr>
        @foreach($expenses as $expense)
            <tr class="dohide">
                <td></td>
                <td>{{ $expense->description }}</td>
                <td class='text-right'>
                    $ {{ number_format($expense->amount_conv,2) }}
                </td>
            </tr>
        @endforeach
    @endforeach
    <tr class='nohide' style="border-top: 2px solid #999;">
        <td></td>
        <td></td>
        <td class="text-right" style="font-size: 1.1em;">
            <strong>Total:</strong> {{ Auth::user()->company->currency_symbol }} {{ number_format($expense_total,2)}}
        </td>
    </tr>
    </tbody>
</table>
