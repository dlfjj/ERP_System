<table class="table table-striped table-bordered table-hover kpi">

    <thead>

    <tr>

        <th>-</th>
        <th><a href="javascript:void(0)"  class="kpi_previous_last"><?=date("Y")-2;?></a></th>
        <th><a href="javascript:void(0)"  class="kpi_last_year"><?=date("Y")-1;?></a></th>
        <th><a href="javascript:void(0)" class="kpi_current_year"><?=date("Y");?></a></th>

    </tr>

    </thead>

    <tbody>

    <tr class="turnover_link">

        <td ><a href="javascript:void(0)" class="turnover_link">Turnover</a></td>
        <td  class="Turnover_2">{{ number_format($turnover_2,2) }}</td>
        <td  class="Turnover_1">{{ number_format($turnover_1,2) }}</td>
        <td class="Turnover_0">{{ number_format($turnover_0,2) }}</td>
        <input type="hidden" value="" name="turnover" id="turnover">

    </tr>



    <tr>

        <td><a href="javascript:void(0)" class="quantities_link">Quantities</a></td>
        <td class="quanties_2">{{ number_format($order_quantities_2) }}</td>
        <td class="quanties_1">{{ number_format($order_quantities_1) }}</td>
        <td class="quanties_0">{{ number_format($order_quantities_0) }}</td>
        <input type="hidden" value="" name="quantities" id="quantities">
    </tr>



    <tr>

        <td><a href="javascript:void(0)" class="order_count_link">Orders count</a></td>
        <td class="order_count_2">{{ $orders_count_2 }}</td>
        <td class="order_count_1">{{ $orders_count_1 }}</td>
        <td class="order_count_0">{{ $orders_count_0 }}</td>
        <input type="hidden" value="" name="order_count" id="order_count">

    </tr>
    <tr>

        <td><a  href="javascript:void(0)" class="unpaid_link">Unpaid Invoices</a></td>
        <td class="unpaid_invoices_2">{{ number_format($unpaid_invoices_2,2) }}</td>
        <td class="unpaid_invoices_1">{{ number_format($unpaid_invoices_1,2) }}</td>
        <td class="unpaid_invoices_0">{{ number_format($unpaid_invoices_0,2) }}</td>
        <input type="hidden" value="" name="unpaid_invoices" id="unpaid_invoices">

    </tr>



    <tr>

        <td><a href="javascript:void(0)" class="overdue_link">Overdue Invoices</td>
        <td class="overdue_invoices_2">{{ number_format($overdue_invoices_2,2) }}</td>
        <td class="overdue_invoices_1">{{ number_format($overdue_invoices_1,2) }}</td>
        <td class="overdue_invoices_0">{{ number_format($overdue_invoices_0,2) }}</td>
        <input type="hidden" value="" name="overdue_invoices" id="overdue_invoices">
    </tr>
    <tr>
        <td colspan="4"><strong>All monetary amounts are displayed in   {{$company_currency_code[0]['company']['currency_code']}}</strong></td>
    </tr>
    <tr>
        <td colspan="4"></td>
        {{--<td></td>--}}
    </tr>
    <tr>

        <th>-</th>
        <th><a href="javascript:void(0)"  class="kpi_previous_last">ACTIVE</a></th>
        <th><a href="javascript:void(0)"  class="kpi_last_year">INACTIVE</a></th>

    </tr>

    <tr>
        <td><a  href="javascript:void(0)" class="product_link">Products</a></td>
        <td  class ="products">{{ $product_count_active }}</td><td> {{ $product_count_inactive }}</td>
        <input type="hidden" value="" name="product" id="product">
    </tr>
    <tr>
        <td><a  href="javascript:void(0)" class="customer_link">Customers</a></td>
        <td class="customers">{{ $customer_count_active }}</td><td> {{ $customer_count_inactive }}</td>
        <input type="hidden" value="" name="customer" id="customer">
    </tr>

    </tbody>

</table>
