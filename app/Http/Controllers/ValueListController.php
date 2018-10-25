<?php

namespace App\Http\Controllers;

use App\Models\ValueList;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ValueListController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function __construct(){
        $this->middleware('auth');
        has_role('admin',1);
    }

    public function index()
    {
        $value_lists = DB::table('value_lists')
            ->groupBy('module')
            ->groupBy('uid')
            ->orderBy('uid')
            ->get();

        return view('settings.value_lists.index',compact('value_lists'));
//        $this->layout->module_title = "Value Lists";
//        $this->layout->module_sub_title = "Valuelists";
//        $this->layout->content = View::make('value_lists.index')
//            ->with('value_lists', $value_lists);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
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
    public function show($id)
    {
        $value_list = ValueList::findOrFail($id);

        $list_entries = DB::table('value_lists')
            ->orderBy('sort')
            ->where('module',$value_list->module)
            ->where('uid',$value_list->uid)
            ->get();

        return view('settings.value_lists.show',compact('value_list','list_entries'));
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
        $value_list = ValueList::findOrFail($id);
        $module = $value_list->module;
        $uid = $value_list->uid;

        $new_values = $request->name;

        if(count($new_values) == 0){
            return redirect('settings/value_lists/'.$id)
                ->with('flash_error','Need at least 1 entry');
        }

       ValueList::where('module',$module)->where('uid',$uid)->delete();

        foreach($new_values as $new_value){

            $new_value_list = new ValueList();
            $new_value_list->uid = $uid;
            $new_value_list->module = $module;
            $new_value_list->name = $new_value;
            $new_value_list->save();
        }

        return redirect('settings/value_lists/'.$new_value_list->id)
            ->with('flash_success','Operation success');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {

    }
}
