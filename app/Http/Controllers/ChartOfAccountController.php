<?php

namespace App\Http\Controllers;

use App\Models\ChartOfAccount;

class ChartOfAccountController extends Controller
{
    public function __construct(){
        // $this->beforeFilter('auth');
        // has_role('company_admin',1);
    }
    public $layout = 'layouts.default';
    public function getIndex($id = NULL) {
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
        $this->layout->module_title = "";
        $this->layout->module_sub_title = "";
        $this->layout->content = View::make('chart_of_accounts.index')
            ->with('account',$account)
            ->with('account_id',$id)
            ->with('ancestors',$ancestors)
            ->with('accounts', $accounts)
            ->with('select_account_type',$select_account_type);
    }
    public function getShow($id) {
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
        $this->layout->module_title = "";
        $this->layout->module_sub_title = "";
        $this->layout->content = View::make('chart_of_accounts.index')
            ->with('account',$account)
            ->with('account_id',$id)
            ->with('ancestors',$ancestors)
            ->with('accounts', $accounts)
            ->with('select_account_type',$select_account_type);
    }
    public function getUpdate($id = NULL) {
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
        $this->layout->module_title = "";
        $this->layout->module_sub_title = "";
        $this->layout->content = View::make('chart_of_accounts.update')
            ->with('account',$account)
            ->with('account_id',$id)
            ->with('ancestors',$ancestors)
            ->with('accounts', $accounts)
            ->with('select_account_type',$select_account_type);
    }
    public function getDelete($id = NULL) {
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
        $this->layout->module_title = "";
        $this->layout->module_sub_title = "";
        $this->layout->content = View::make('chart_of_accounts.delete')
            ->with('account',$account)
            ->with('account_id',$id)
            ->with('ancestors',$ancestors)
            ->with('accounts', $accounts);
    }
    public function postCreate() {
        $rules = array(
            'parent_id' => 'integer|digits_between:1,6',
            "name" => "required|between:1,100",
            'code' => 'required|between:1,100',
            'type' => 'required|between:1,100'
        );
        $input = Input::get();
        $validation = Validator::make($input, $rules);
        $parent_id = Input::get('parent_id');
        if($validation->fails()){
            return Redirect::to('chart_of_accounts/show/'.$parent_id)
                ->with('flash_error','Operation failed')
                ->withErrors($validation->Messages())
                ->withInput();
        } else {
            if($parent_id){
                $parent_account = ChartOfAccount::where("id",$parent_id)->first();
                $parent_account->children()->create(array(
                    'code' => Input::get('code'),
                    'type' => Input::get('type'),
                    'name' => Input::get('name'),
                    'company_id' => Auth::user()->company_id
                ));
            } else {
                ChartOfAccount::create(array(
                        'code' => Input::get('code'),
                        'type' => Input::get('type'),
                        'name' => Input::get('name'),
                        'company_id' => Auth::user()->company_id
                    )
                );
            }
            //$root = ChartOfAccount::create(array('name' => 'Electronic Components'));
            //$root->children()->create(array('name' => 'PCBA'));
            //$root->children()->create(array('name' => 'Resistors'));
            if(is_numeric($parent_id)){
                return Redirect::to('chart_of_accounts/show/'.$parent_id)
                    ->with('flash_success','Operation success');
            } else {
                return Redirect::to('chart_of_accounts')
                    ->with('flash_success','Operation success');
            }
        }
    }
    public function postUpdate($id) {
        $rules = array(
            'account_id' => 'required|integer|digits_between:1,6',
            "name" => "required|between:1,100",
            'code' => 'required|between:1,1000',
            'type' => 'required|between:1,1000'
        );
        $input = Input::get();
        $validation = Validator::make($input, $rules);
        if($validation->fails()){
            return Redirect::to('chart_of_accounts/show/'.$id)
                ->with('flash_error','Operation failed')
                ->withErrors($validation->Messages())
                ->withInput();
        } else {
            $account_id = $id;
            $account = ChartOfAccount::findOrFail($account_id);
            $account->name = Input::get('name');
            $account->code = Input::get('code');
            $account->type = Input::get('type');
            $account->save();
            return Redirect::to('chart_of_accounts/show/'.$id)
                ->with('flash_success','Operation success');
        }
    }
    public function postDelete($id) {
        $rules = array(
            'account_id' => 'required|integer|digits_between:1,6',
        );
        $input = Input::get();
        $validation = Validator::make($input, $rules);
        if($validation->fails()){
            return Redirect::to('chart_of_accounts/')
                ->with('flash_error','Operation failed')
                ->withErrors($validation->Messages())
                ->withInput();
        } else {
            $account_id = Input::get('account_id');
            $account = ChartOfAccount::findOrFail($account_id);
            $account->delete();
            //$account->save();
            return Redirect::to('chart_of_accounts/')
                ->with('flash_success','Operation success');
        }
    }
    public function printTree($root){
        $html = '';
        $html .= "<ul>";
        foreach($root as $r){
            $html .= "<li>". $r->name;
            if(count($r->children) > 0) {
                $html .= $this->printTree($r->children);
            }
            $html .= "</li>";
        }
        $html .= "</ul>";
        return $html;
    }
    public function printSelect($root,$account_id){
        $html = '';
        $html .= "<select class='form-control' name='account_id'>";
        foreach($root as $r){
            $html .= $this->printOption($r,$account_id);
        }
        $html .= "</select>";
        return $html;
    }
    public function printOption($node,$account_id=NULL){
        $level = $node->getLevel();
        $indent = "";
        for($i = 0; $i < $level; $i++){
            $indent .= "&nbsp;|-&nbsp;";
        }
        if($account_id == $node->id){
            $checked = 'selected="selected"';
        } else {
            $checked = "";
        }
        $html = '<option ' . $checked . ' value="'.$node->id . '">'.$indent . $node->name . '</option>';
        foreach($node->children as $child){
            $html .= $this->printOption($child,$account_id);
        }
        return $html;
    }
}
