<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ValueList;

class ValueListController extends Controller
{
    public function __construct(){
        $this->beforeFilter('auth');
        has_role('admin',1);
    }

    public $layout = 'layouts.default';

    public function getIndex() {
        $value_lists = DB::table('value_lists')
            ->groupBy('module')
            ->groupBy('uid')
            ->orderBy('uid')
            ->get();
        $this->layout->module_title = "Value Lists";
        $this->layout->module_sub_title = "Valuelists";
        $this->layout->content = View::make('value_lists.index')
            ->with('value_lists', $value_lists);
    }

    public function getShow($id) {
        $value_list = ValueList::findOrFail($id);

        $list_entries = DB::table('value_lists')
            ->orderBy('sort')
            ->where('module',$value_list->module)
            ->where('uid',$value_list->uid)
            ->get();
        $this->layout->module_title = "Value Lists";
        $this->layout->module_sub_title = "Valuelists";
        $this->layout->content = View::make('value_lists.show')
            ->with('value_list', $value_list)
            ->with('list_entries', $list_entries);
    }

    public function postUpdate($id){
        $value_list = ValueList::findOrFail($id);
        $module = $value_list->module;
        $uid = $value_list->uid;

        $new_values = Input::get('name');

        if(count($new_values) == 0){
            return Redirect::to('value_lists/show/'.$id)
                ->with('flash_error','Need at least 1 entry');
        }

        $value_list = ValueList::where('module',$module)->where('uid',$uid)->delete();

        foreach($new_values as $new_value){
            $new_value_list = new ValueList();
            $new_value_list->uid = $uid;
            $new_value_list->module = $module;
            $new_value_list->name = $new_value;
            $new_value_list->save();
        }

        return Redirect::to('value_lists/show/'.$new_value_list->id)
            ->with('flash_success','Operation success');
    }

}
