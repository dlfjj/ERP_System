<table class="table table-hover">
    <thead>
    <tr>
        <th>Product Code</th>
        <th>MPN</th>
        <th>Product Name</th>
        <th>Location</th>
        <th>Stock Qty</th>
        <th class="cell-tight">Amount 20' (<small>{{ $currency_code }}</small>)</th>
        <th class="cell-tight">Amount 40' (<small>{{ $currency_code }}</small>)</th>
    </tr>
    </thead>
    <tbody>
    @php
        $grand_total_20 = 0;
        $grand_total_40 = 0;
    @endphp
    @if(count($products)> 0)

        @foreach($products as $product)
            @php

                $value_20 = $product->stock * ($product->base_price_20 * $product->landed_20);
                $value_40 = $product->stock * ($product->base_price_40 * $product->landed_40);

                $grand_total_20 += $value_20;
                $grand_total_40 += $value_40;
            @endphp

            <tr>
                <td>{{ $product->product_code }}</td>
                <td>{{ $product->mpn }}</td>
                <td><a href="/products/{{$product->id}}">{{substr($product->product_name,0,50) }}</a></td>
                <td>{{ $product->location }}</td>
                <td>{{ $product->stock }}</td>
                <td class="cell-tight">{{ number_format($value_20,2) }}</td>
                <td class="cell-tight">{{ number_format($value_40,2) }}</td>
            </tr>

        @endforeach
        <tr style="font-weight: bold">
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td>Total:</td>
            <td>{{ number_format($grand_total_20,2) }}</td>
            <td>{{ number_format($grand_total_40,2) }}</td>
        </tr>

    @else
        <tr class="stockorder-form-row">
            <td class="cell-tight">No results / Adjust report parameters to Generate</td>
        </tr>

    @endif

    </tbody>
</table>
