<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\ProductAttribute;

class AttributeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct(){
        $this->middleware('auth');
        // has_role('expenses',1);
    }
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

        $product_id = $request->id;

        $product = Product::findOrFail($product_id);
        $action = $request->get('action');

        if($action == "mass_update"){
            $groups = $request->get('group');
            $names   = $request->get('name');
            $values = $request->get('value');

            if(!is_array($groups)){ die("Data error"); }
            foreach($groups as $attribute_id => $group){
                $attribute = ProductAttribute::findOrFail($attribute_id);
                $attribute->group = $group;
                $attribute->name  = $names[$attribute_id];
                $attribute->value = $values[$attribute_id];
                $attribute->save();
            }
        } else {
            $attribute = ProductAttribute::where('product_id',$product->id)
                ->where('group',$request->get('group'))
                ->where('name',$request->get('name'))
                ->first()
            ;
            if(!$attribute){
                $attribute = new ProductAttribute();
                $attribute->product_id = $product->id;
            }
            $attribute->group = $request->get('group');
            $attribute->name  = $request->get('name');
            $attribute->value = $request->get('value');
            $attribute->save();
        }

        return redirect('/products/attributes/'.$product->id)
            ->with('flash_success','Operation success');

    }

//    public function postAttributes(Request $request, $id){
//
//        return $id;
//
//        $product = Product::findOrFail($id);
//        $action = Input::get('action');
//
//        if($action == "mass_update"){
//            $groups = Input::get('group');
//            $names   = Input::get('name');
//            $values = Input::get('value');
//
//            if(!is_array($groups)){ die("Data error"); }
//            foreach($groups as $attribute_id => $group){
//                $attribute = ProductAttribute::findOrFail($attribute_id);
//                $attribute->group = $group;
//                $attribute->name  = $names[$attribute_id];
//                $attribute->value = $values[$attribute_id];
//                $attribute->save();
//            }
//        } else {
//            $attribute = ProductAttribute::where('product_id',$product->id)
//                ->where('group',Input::get('group'))
//                ->where('name',Input::get('name'))
//                ->first()
//            ;
//            if(!$attribute){
//                $attribute = new ProductAttribute();
//                $attribute->product_id = $product->id;
//            }
//            $attribute->group = Input::get('group');
//            $attribute->name  = Input::get('name');
//            $attribute->value = Input::get('value');
//            $attribute->save();
//        }
//
//        return Redirect::to('/products/attributes/'.$product->id)
//            ->with('flash_success','Operation success');
//
//    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $product = Product::findOrFail($id);
        $attributes = ProductAttribute::where('product_id',$product->id)->orderBy('group','DESC')->orderBy('name','ASC')->get();

        return view('products.attributes.show',compact('product','attributes'));
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
    public function update(Request $request, $attribute_id)
    {
        $attribute = ProductAttribute::findOrFail($attribute_id);
        $group = $request->group;
        $names   = $request->name;
        $values = $request->value;


        $attribute->group = $group;
        $attribute->name  = $names;
        $attribute->value = $values;
        $attribute->save();

        return redirect('/products/attributes/'.$request->product_id)
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
        $attribute = ProductAttribute::findOrFail($id);
        $product   = Product::findOrFail($attribute->product_id);

        $attribute->delete();
//        redirect('/products/attributes/'.$product->id)->back();
//            ->with('flash_success','Operation success');
    }
}
