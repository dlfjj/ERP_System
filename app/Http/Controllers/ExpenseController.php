<?php

namespace App\Http\Controllers;

use App\Models\CustomerPayment;
use App\Models\Expense;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\ChartOfAccount;
use App\Models\ValueList;
use Validator;
use Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;


class ExpenseController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     *`
     */
    public function __construct(){
        $this->middleware('auth');
    }
    public $layout = 'layouts.default';

    //responsible for display the table data
    public function index()
    {
//        $expenses = Expense::find(1);
//        return view('expenses.index',compact('expenses'));
//        $expenses = Expense::all();
//
//        $content = view('expenses.index',compact('expenses'));
//        return view($this->layout, compact('content'));

//        foreach ($expenses as $expense) {
//            return $expense;
//        }


//        return $transaction;


//        $expenses = $transaction;


//        $expenses = Expense::Leftjoin('users','expenses.user_id','=','users.id')
//            ->Leftjoin('chart_of_accounts','expenses.account_id','=','chart_of_accounts.id')
//            ->select(
//                array(
//                    'expenses.id',
//                    'chart_of_accounts.type',
//                    'expenses.date_created',
//                    'users.username',
//                    'chart_of_accounts.name',
//                    'expenses.currency_code',
//                    'expenses.amount',
//                    'expenses.description'
//                ))
//            ->where('expenses.company_id',return_company_id())->get();

        $tree = ChartOfAccount::where('company_id',return_company_id())->get()->toHierarchy();
        $select_accounts = printSelect($tree,10,'account_id');
        $select_bank_accounts  = ValueList::where('uid','=','BANK_ACCOUNTS')->orderBy('name', 'asc')->pluck('name','name')->toArray();
        $select_currency_codes = ValueList::where('uid','=','CURRENCY_CODES')->orderBy('name', 'asc')->pluck('name','name')->toArray();

        $account_name = getChartofaccountName();
//        return $account_name;

        return view('expenses.index',compact('select_accounts','select_bank_accounts','select_currency_codes','account_name'));

    }

    public function getExpenseData(){

        $transactions = Expense::Leftjoin('users','expenses.user_id','=','users.id')
            ->Leftjoin('chart_of_accounts','expenses.account_id','=','chart_of_accounts.id')
            ->select(
                array(
                    'expenses.id as id',
                    'chart_of_accounts.type as type',
                    'expenses.date_created as date',
                    'users.username as username',
                    'chart_of_accounts.name as account',
                    'expenses.currency_code as cur',
                    'expenses.amount as amount',
                    'expenses.description as description',
                    'expenses.user_id as hidden'
                ))
            ->addSelect(DB::raw("'expenses' as Table_name"))
            ->where('expenses.company_id',return_company_id())
            ->union(
                DB::table('customer_payments')
                    ->leftJoin('users','customer_payments.created_by','=','users.id')
                    ->leftjoin('chart_of_accounts','customer_payments.account_id','=','chart_of_accounts.id')
                    ->select(
                        array(
                            'customer_payments.id as id',
                            'customer_payments.type as type',
                            'customer_payments.date as date',
                            'users.username as username',
                            'chart_of_accounts.name as account',
                            'customer_payments.currency_code as cur',
                            'customer_payments.amount as amount',
                            'customer_payments.remark as description',
                            'customer_payments.order_id as hidden'
                        )
                    )
                    ->addSelect(DB::raw("'customer_payments' as Table_name"))
                    ->where('customer_payments.company_id',return_company_id())
            )
            ->union(
                DB::table('purchase_payments')
                ->leftJoin('users', 'users.id','=','purchase_payments.created_by')
                ->leftJoin('chart_of_accounts','chart_of_accounts.id','=','purchase_payments.account_id')
                ->select(
                    array(
                        'purchase_payments.id as id',
                        'chart_of_accounts.type as type',
                        'purchase_payments.date_created',
                        'users.username as username',
                        'chart_of_accounts.name as account',
                        'purchase_payments.currency_code as cur',
                        'purchase_payments.amount as amount',
                        'purchase_payments.description as description',
                        'purchase_payments.purchase_id as hidden'
                    )
                )
                    ->addSelect(DB::raw("'purchase_payments' as Table_name"))
                ->where('purchase_payments.company_id', return_company_id())
            )
            ->get();
//        foreach($transactions as $transaction){
//            if($transaction->Table_name == 'customer_payments'){
//                return $transaction;
//                $order_id = CustomerPayment::where('id','=', $transaction->id)->pluck('order_id');
//                return $order_id;
//            }
//        }


        return Datatables::of($transactions)
            ->addColumn('action', function ($transaction) {
                if($transaction->Table_name == 'expenses') {
                    return '<a href="/expenses/' . $transaction->id . '" class="bs-tooltip" title="View"><i class="icon-search"></i></a>';
                }elseif($transaction->Table_name == 'customer_payments'){
                    return '<a href="/orders/payments/' . $transaction->hidden . '" class="bs-tooltip" title="View"><i class="icon-search"></i></a>';
                }elseif($transaction->Table_name == 'purchase_payments'){
                    return '<a href="/purchases/payments/' . $transaction->hidden . '" class="bs-tooltip" title="View"><i class="icon-search"></i></a>';

                }
            })
            ->make(true);


//        $expenses = Expense::Leftjoin('users','expenses.user_id','=','users.id')
//            ->Leftjoin('chart_of_accounts','expenses.account_id','=','chart_of_accounts.id')
//            ->select(
//                array(
//                    'expenses.id',
//                    'chart_of_accounts.type',
//                    'expenses.date_created',
//                    'users.username',
//                    'chart_of_accounts.name',
//                    'expenses.currency_code',
//                    'expenses.amount',
//                    'expenses.description'
//                ))
//            ->where('expenses.company_id',return_company_id())->get();
//
//        return Datatables::of($expenses)
//            ->addColumn('action', function ($expense) {
//                return '<a href="/expenses/'.$expense->id.'" class="bs-tooltip" title="View"><i class="icon-search"></i></a>';
//            })
//            ->make(true);

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
//        return $request;
//        $rules = [
//            'id' => 'required|integer|digits_between:1,6',
//            'account_id' => 'required|integer|digits_between:1,6',
//            'description' => 'required|between:1,5000',
//            'currency_code' => 'alpha|between:3,3',
//            'amount' => 'numeric|required|regex:/^\d*(\.\d{1,2})?$/',
//            'order_id' => 'nullable|integer',
//            'purchase_id' => 'nullable|integer'
//        ];
//        $input = $request->all();
//        $validation = Validator::make($input, $rules);
//        if($validation->fails()){
//            return redirect('expenses'.'/'.$id)
//                ->with('flash_error','Operation failed')
//                ->withErrors($validation->Messages())
//                ->withInput();
//        } else {
//            $expense = Expense::findOrFail($id);
//            $expense->fill($input);
////            return convert_currency($expense->currency_code,"USD",$expense->amount, $expense->date_created);
//            $expense->amount_conv =convert_currency($expense->currency_code,"USD",$expense->amount, $expense->date_created);
//            $expense->save();
//            return redirect('expenses'.'/')
//                ->with('flash_success','Operation success');
//        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
//    need some work on this
    public function store(Request $request)
    {
//        return $request->all();
        $rules = [
//            'account_id' => 'required|integer|digits_between:1,6',
            'description' => 'required|between:1,5000',
            'currency_code' => 'alpha|between:3,3',
            'amount' => 'numeric|required|regex:/^\d*(\.\d{1,2})?$/',
            'transaction_reference' => 'nullable|string',

//            'order_id' => 'nullable|integer',
//            'purchase_id' => 'nullable|integer'
        ];


        $input  = $request->all();
        $validation = Validator::make($input, $rules);
        if($validation->fails()){
            return redirect('expenses'.'/')
                ->with('flash_error','Operation failed')
                ->withErrors($validation->Messages())
                ->withInput();
        } else {
            $expense = New Expense;
            $expense->fill($input);

//            modify transaction_reference and account id
            if($request->account_id === NULL or return_company_id() === 1) {
                $account_id = "";
            } else {
                $account_id = $request->account_id;
            }
            $expense->account_id = $account_id;

            if($request->transaction_reference === NULL) {
                $transaction_reference = '';
            }else{
                $transaction_reference = $request->transaction_reference;
            }
            $expense->transaction_reference = $transaction_reference;
            $expense->created_by = Auth::user()->id;
            $expense->updated_by = Auth::user()->id;
            $expense->company_id = return_company_id();
            $expense->user_id    = Auth::user()->id;
            $expense->amount_conv =convert_currency($expense->currency_code,"USD",$expense->amount, $expense->date_created);
            $expense->save();
            return redirect('expenses'.'/')
                ->with('flash_success','Operation success');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Expense  $expense
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {

        $expense = Expense::findOrFail($id);
        if($expense->company_id != return_company_id()){
            die("Access Violation!");
        }

        if(has_role('expenses_see_own')){
            if($expense->user_id != Auth::user()->id){
                die("Permission issue");
            }
        }
        $tree = ChartOfAccount::where('company_id',return_company_id())->get()->toHierarchy();
        $select_accounts = printSelect($tree,$expense->account_id,'account_id');
        $select_currency_codes = ValueList::where('uid','=','CURRENCY_CODES')->orderBy('name', 'asc')->pluck('name','name');
        $select_bank_accounts  = ValueList::where('uid','=','BANK_ACCOUNTS')->orderBy('name', 'asc')->pluck('name','name');
        return view('expenses.show',compact('expense','select_accounts','select_bank_accounts','select_currency_codes'));
    }

    public function edit(Expense $expense)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Expense  $expense
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request,$id)
    {
//        return $request;

        if(return_company_id()==1){
            $request->account_id = "Hongkong do not use this function";
        }
        $rules = [
            'id' => 'required|integer|digits_between:1,6',
            'account_id' => 'required|integer|digits_between:1,6',
            'description' => 'required|between:1,5000',
            'currency_code' => 'alpha|between:3,3',
            'amount' => 'numeric|required|regex:/^\d*(\.\d{1,2})?$/',
            'order_id' => 'nullable|integer',
            'purchase_id' => 'nullable|integer'
        ];
        $input = $request->all();
        $validation = Validator::make($input, $rules);
        if($validation->fails()){
            return redirect('expenses'.'/'.$id)
                ->with('flash_error','Operation failed')
                ->withErrors($validation->Messages())
                ->withInput();
        } else {
            $expense = Expense::findOrFail($id);
            $expense->fill($input);
//            return convert_currency($expense->currency_code,"USD",$expense->amount, $expense->date_created);
            $expense->amount_conv =convert_currency($expense->currency_code,"USD",$expense->amount, $expense->date_created);
            $expense->save();
            return redirect('expenses'.'/')
                ->with('flash_success','Operation success');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Expense  $expense
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $expense = Money::findOrFail($id);
        $expense->delete();
        return Redirect::to('moneys/')
            ->with('flash_success','Operation success');
    }



}
