<?php
namespace App\Http\Controllers;
use Illuminate\Routing\Controller as BaseController;

class DashboardController extends BaseController {

    public function __construct(){
        $this->middleware('auth');
        //$this->middleware('auth');
    }

    public $layout = 'layouts.default';

    public function dashboard() {
        return view('dashboard.blank');
        // echo Auth::user()->id;die;
        // if(Auth::user()->company_id > 1){

        //     $month_expenses = 0;
        //     return view('dashboard.blank');
        // }

        $this_year_1 = "2016-01-01";
        $this_year_2 = "2016-12-31";

        $last_year_1 = "2015-01-01";
        $last_year_2 = "2015-12-31";

        $total_orders_a = Order::whereIn("status_id",[5,6,7])
            ->where('order_date', '>=', $this_year_1)->where('order_date', '<=', $this_year_2)->count();
        $total_orders_b = Order::where("status_id","=",7)
            ->where('order_date', '>=', $this_year_1)->where('order_date', '<=', $this_year_2)->count();

        $total_orders_a_last = Order::whereIn("status_id",[5,6,7])
            ->where('order_date', '>=', $last_year_1)->where('order_date', '<=', $last_year_2)->count();
        $total_orders_b_last = Order::where("status_id","=",7)
            ->where('order_date', '>=', $last_year_1)->where('order_date', '<=', $last_year_2)->count();

        $total_sales_a = Order::whereIn("status_id",[5,6,7])
            ->where('order_date', '>=', $this_year_1)->where('order_date', '<=', $this_year_2)->sum('total_net');
        $total_sales_b = Order::where("status_id","=",7)
            ->where('estimated_finish_date', '>=', $this_year_1)
            ->where('estimated_finish_date', '<=', $this_year_2)
            ->sum('total_net');

        $total_sales_a_last = Order::whereIn("status_id",[5,6,7])
            ->where('order_date', '>=', $last_year_1)->where('order_date', '<=', $last_year_2)->sum('total_net');
        $total_sales_b_last = Order::where("status_id","=",7)
            ->where('estimated_finish_date', '>=', $last_year_1)
            ->where('estimated_finish_date', '<=', $last_year_2)
            ->sum('total_net');

        $open_invoices_a = Order::whereRaw('status_id IN (6,7) and total_paid < total_net')
            ->count();
        $open_invoices_b = DB::table('orders')
            ->select(DB::raw('SUM(total_net-total_paid) AS open_amount'))
            ->whereRaw('status_id IN(6,7) and total_paid < total_net')
            ->first();
        $open_invoices_b = $open_invoices_b->open_amount;

        $quotations_a = Order::where("status_id","=",1)
            ->where('order_date', '>=', $this_year_1)
            ->where('order_date', '<=', $this_year_2)
            ->count();

        $quotations_b = Order::where("status_id","=",1)
            ->where('order_date', '>=', $this_year_1)
            ->where('order_date', '<=', $this_year_2)
            ->sum('total_net');

        $customers_a = Customer::where('company_id',return_company_id())->count();
        $customers_b = DB::table('orders')
            ->select('id')
            ->orderBy('customer_id', 'desc')
            ->groupBy('customer_id')
            ->get();
        $customers_b = count($customers_b);

        $products_a = Product::where('company_id',return_company_id())->count();
        $products_b = Product::where('is_visible','=','1')
            ->count();

        $customers_top_25 = DB::select(DB::raw("SELECT customers.customer_name, SUM(orders.total_net) AS order_total FROM orders,customers WHERE orders.customer_id=customers.id AND orders.status_id IN (5,6,7) GROUP BY orders.customer_id ORDER BY order_total DESC LIMIT 25"));

        $products_top_50_sql = "SELECT
products.product_name,
SUM(order_items.unit_price_net) AS order_total
FROM products
JOIN order_items ON order_items.product_id = products.id
JOIN orders ON order_items.order_id = orders.id
WHERE orders.status_id IN (5,6,7)
GROUP BY order_items.product_id
ORDER BY order_total
DESC LIMIT 50";
        $products_top_50  = DB::select(DB::raw($products_top_50_sql));

        $pieces_top_50_sql = "SELECT
        products.product_name,
        SUM(order_items.quantity) AS order_total
        FROM products
        JOIN order_items ON order_items.product_id = products.id
        JOIN orders ON order_items.order_id = orders.id
        WHERE orders.status_id IN (5,6,7)
        GROUP BY order_items.product_id
        ORDER BY order_total
        DESC LIMIT 50";
        $pieces_top_50  = DB::select(DB::raw($pieces_top_50_sql));

        $unpaid_orders = DB::table('orders')
            ->leftJoin('customers', 'orders.customer_id', '=', 'customers.id')
            ->select(
                'orders.id',
                'orders.order_no',
                'customers.customer_name',
                'orders.customer_order_number',
                'orders.total_net',
                DB::raw('orders.total_net - orders.total_paid AS open_amount')
            )
            ->where("orders.total_net",">","orders.total_paid")
            ->whereIn("orders.status_id",array(6,7))
            ->where("orders.company_id",return_company_id())
            ->orderBy('id','DESC')
            ->get();

        $overdue_invoices = DB::table('orders')
            ->leftJoin('customers', 'orders.customer_id', '=', 'customers.id')
            ->select(
                'orders.id',
                'orders.order_no',
                'customers.customer_name',
                'orders.customer_order_number',
                'orders.estimated_finish_date',
                'orders.total_net',
                DB::raw('orders.total_net - orders.total_paid AS open_amount')
            )
            ->where("orders.total_net",">","orders.total_paid")
            ->whereIn("orders.status_id",array(6,7))
            ->where("orders.estimated_finish_date","<",date("Y-m-d"))
            ->where("orders.company_id",return_company_id())
            ->orderBy("orders.estimated_finish_date","DESC")
            ->get();

        $whiteboards = Order::whereIn("status_id",[4,5,6])
            ->orderBy('estimated_finish_date','ASC')
            ->where("orders.company_id",return_company_id())
            ->get();

        $this->layout->content = View::make('dashboard.sales')
            ->with('whiteboards', $whiteboards)
            ->with('overdue_invoices', $overdue_invoices)
            ->with('unpaid_orders', $unpaid_orders)
            ->with('customers_top_25', $customers_top_25)
            ->with('products_top_50', $products_top_50)
            ->with('pieces_top_50', $pieces_top_50)
            ->with('total_orders_a', $total_orders_a)
            ->with('total_orders_b', $total_orders_b)
            ->with('total_orders_a_last', $total_orders_a_last)
            ->with('total_orders_b_last', $total_orders_b_last)
            ->with('total_sales_a', $total_sales_a)
            ->with('total_sales_b', $total_sales_b)
            ->with('total_sales_a_last', $total_sales_a_last)
            ->with('total_sales_b_last', $total_sales_b_last)
            ->with('open_invoices_a', $open_invoices_a)
            ->with('open_invoices_b', $open_invoices_b)
            ->with('quotations_a', $quotations_a)
            ->with('quotations_b', $quotations_b)
            ->with('customers_a', $customers_a)
            ->with('customers_b', $customers_b)
            ->with('products_a', $products_a)
            ->with('products_b', $products_b)
        ;
    }
}
