<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ChartOfAccount;
use Validator;
use Auth;

class ChartOfAccountController extends Controller
{
    public function __construct(){
        $this->middleware('auth');
        has_role('admin',1);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($id = NULL)
    {
        if($id == NULL){
            $account = NULL;
            $accounts = ChartOfAccount::roots()
//                for testing purpose
                ->where('company_id',return_company_id())
                ->get();
            $ancestors = NULL;
        } else {
            $account = ChartOfAccount::where('id', '=', $id)->first();
            $ancestors = $account->ancestorsAndSelf()->get();
            $accounts = $account->immediateDescendants()->get();
        }

        $select_account_type = array(
            "Bank" => "Bank",
            "Income" => "Income",
            "Expense" => "Expense"
        );
        $account_id = $id;
        return view('settings.chart_of_accounts.index',compact('account','account_id','ancestors','accounts','select_account_type'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $rules = array(
            'parent_id' => 'integer|digits_between:1,6|nullable',
            "name" => "required|between:1,100",
            'code' => 'required|between:1,100',
            'type' => 'required|between:1,100'
        );
        $input = $request->all();
        $validation = Validator::make($input, $rules);

        $parent_id = $request->parent_id;

        if($validation->fails()){
            return redirect('/settings/chart_of_accounts/'.$parent_id)
                ->with('flash_error','Operation failed')
                ->withErrors($validation->Messages())
                ->withInput();
        } else {
            if($parent_id){
                $parent_account = ChartOfAccount::where("id",$parent_id)->first();
                $parent_account->children()->create(array(
                    'code' => $request->code,
                    'type' => $request->type,
                    'name' => $request->name,
                    'company_id' => Auth::user()->company_id
                ));
            } else {
                ChartOfAccount::create(array(
                        'code' => $request->code,
                        'type' => $request->type,
                        'name' => $request->name,
                        'company_id' => Auth::user()->company_id
                    )
                );
            }
            //$root = ChartOfAccount::create(array('name' => 'Electronic Components'));
            //$root->children()->create(array('name' => 'PCBA'));
            //$root->children()->create(array('name' => 'Resistors'));

            if(is_numeric($parent_id)){
                return redirect('/settings/chart_of_accounts/lower-level/'.$parent_id)
                    ->with('flash_success','Operation success');
            } else {
                return redirect()->back()
                    ->with('flash_success','Operation success');
            }
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id = NULL)
    {
        if($id == NULL){
            $account = NULL;
            $accounts = ChartOfAccount::roots()
                ->where('company_id',return_company_id())
                ->get();
            $ancestors = NULL;
        } else {
            $account = ChartOfAccount::where('id', '=', $id)->first();
            $ancestors = $account->ancestorsAndSelf()->get();
            $accounts = $account->immediateDescendants()->get();
        }

        $select_account_type = array(
            "Bank" => "Bank",
            "Income" => "Income",
            "Expense" => "Expense"
        );
        $account_id = $id;

        return view('settings.chart_of_accounts.update', compact('account','accounts','account_id','ancestors','select_account_type'));
    }

    public function showDifferentLevel($id){
        if($id == NULL){
            $account = NULL;
            $accounts = ChartOfAccount::roots()
                ->where('company_id',return_company_id())
                ->get();
            $ancestors = NULL;
        } else {
            $account = ChartOfAccount::where('id', '=', $id)->first();
            $ancestors = $account->ancestorsAndSelf()->get();
            $accounts = $account->immediateDescendants()->get();
        }

        $select_account_type = array(
            "Bank" => "Bank",
            "Income" => "Income",
            "Expense" => "Expense"
        );
        $account_id = $id;

        $path = " » ";
        if($ancestors){
            foreach($ancestors as $ancestor){
                $path .= "<a href='/settings/chart_of_accounts/lower-level/$ancestor->id'>$ancestor->name</a>" . " &raquo; ";
            }
        }
        $path = substr_replace($path,"",-8);


        return view('settings.chart_of_accounts.lower_level', compact('account','accounts','account_id','ancestors','select_account_type','path'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $rules = array(
            'account_id' => 'required|integer|digits_between:1,6',
            "name" => "required|between:1,100",
            'code' => 'required|between:1,1000',
            'type' => 'required|between:1,1000'
        );
        $input = $request->all();
        $validation = Validator::make($input, $rules);

        if($validation->fails()){
            return redirect('/settings/chart_of_accounts/'.$id)
                ->with('flash_error','Operation failed')
                ->withErrors($validation->Messages())
                ->withInput();
        } else {
            $account_id = $id;
            $account = ChartOfAccount::findOrFail($account_id);

            $account->name = $request->name;
            $account->code = $request->code;
            $account->type = $request->type;
            $account->save();

            return redirect('/settings/chart_of_accounts/')
                ->with('flash_success','Operation success');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
//        $rules = array(
//            'account_id' => 'required|integer|digits_between:1,6',
//        );
//        $input = Input::get();
//        $validation = Validator::make($input, $rules);

        if(!is_numeric($id)){
            return redirect('/settings/chart_of_accounts/')
                ->with('flash_error','Operation failed')
                ->withInput();
        } else {
            $account = ChartOfAccount::findOrFail($id);
            $account->delete();
            //$account->save();
            return redirect('/settings/chart_of_accounts/')
                ->with('flash_success','Operation success');
        }
    }
}
