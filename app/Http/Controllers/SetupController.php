<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\ValueList;
use App\Models\User;
use Illuminate\Http\Request;
use Validator;
use Auth;

class SetupController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
        $product = Product::findOrFail($id);
        $tree = Category::all()->toArray();
        $select_categories = printSelect($tree,$product->category_id);

        if($product->company_id != return_company_id()){
            die("Access violation. Click <a href='/'>here</a> to get back.");
        }
        $user_created =     User::where('created_at',$product->created_by)->pluck('username');
        $user_updated = User::where('updated_at',$product->updated_by)->pluck('username');
        $select_currency_codes = ValueList::where('uid','=','currency_codes')->orderBy('name', 'asc')->pluck('name','name');
        $select_uom = ValueList::where('uid','=','uom')->orderBy('name', 'asc')->pluck('name','name');
        $select_package = ValueList::where('uid','=','package')->orderBy('name', 'asc')->pluck('name','name');
        $select_origin = ValueList::where('uid','=','origin')->orderBy('name', 'asc')->pluck('name','name');
        $select_users = User::pluck('username','id');

        return view('products.setup.show',compact('user_created','user_updated','product','select_uom','select_package','select_origin','select_users','select_categories','select_currency_codes'));

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
            'id' => 'required|integer|digits_between:1,6',
        );
        $input = $request->all();
        $validation = Validator::make($input, $rules);
        if($validation->fails()){
            return redirect('products/setup/'.$id)
                ->with('flash_error','Operation failed')
                ->withErrors($validation->Messages())
                ->withInput();
        } else {
            $product = Product::findOrFail($id);
            $product->fill($input);
            $product->updated_by = Auth::user()->id;
            if($request->get('company_sync')){
                $product->company_sync = 1;
            } else {
                $product->company_sync = 0;
            }
            $product->save();

            return redirect()->back()
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
        //
    }
}
