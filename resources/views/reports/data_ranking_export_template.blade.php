<table class="table table-hover">
    <thead>
    <tr>
        <th class="cell-tight">product name</th>
        <th class="cell-tight">quantity</th>
        <th class="cell-tight">Amount gross (<small>{{$currency_code[0]}}</small>)</th>
    </tr>
    </thead>
    <tbody>
    @php
        $grand_total = 0;

        if($report_type == 'value'){
            $report_base = $results;
        } else {
            $report_base = $quantities;
        }

    @endphp
    @if(count($report_base)> 0)

        @foreach($report_base as $k => $v)
            @php
                $product = App\Models\Product::findorfail($k);
                $grand_total += $results[$k];
                $top++;
            @endphp
            <tr class="stockorder-form-row">
                <td class="cell-tight"><a href="/products/{{$product->id}}">{{$product->product_name }}</a></td>
                <td class="cell-tight">{{ $quantities[$k]}}</td>
                <td class="cell-tight">{{ number_format($results[$k],2) }}</td>
            </tr>
            @if($loop->count == 51)
                @break;
            @endif

        @endforeach
        <tr style="font-weight: bold;">
            <td>Total:</td>
            <td>{{array_sum($quantities)}}</td>
            <td>{{ number_format($grand_total,2) }}</td>
        </tr>

    @else
        <tr class="stockorder-form-row">
            <td class="cell-tight">not found</td>
        </tr>

    @endif

    </tbody>
</table>