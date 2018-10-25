<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\CategoryAttribute;
use Illuminate\Http\Request;
use App\Models\CategoryImage;
use App\Models\CategoryDownload;
use Redirect;
use Validator;


class ProductCategoryController extends Controller
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
            $category = NULL;
            $categories = Category::roots()->orderBy('sort_by','ASC')->get();
            $ancestors = NULL;
        } else {
            $category = Category::where('id', '=', $id)->first();
            $ancestors = $category->ancestorsAndSelf()->get();
            $categories = $category->immediateDescendants()->get();
        }
        $category_id = $id;
        return view('settings.product_categories.index',compact('category','category_id','ancestors','categories'));
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
        $rules = array(
            'parent_id' => 'integer|digits_between:1,6',
            "name" => "required|between:1,100",
            'description' => 'between:1,1000'
        );
        $input = $request->all();
        $validation = Validator::make($input, $rules);

        $parent_id = $request->parent_id;

        if($validation->fails()){
            return Redirect::to('product_categories/show/'.$parent_id)
                ->with('flash_error','Operation failed')
                ->withErrors($validation->Messages())
                ->withInput();
        } else {
            if($parent_id){
                $parent_category = Category::where("id",$parent_id)->first();
                $parent_category->children()->create(array(
                    'name' => Input::get('name'),
                    'description' => Input::get('description')
                ));
            } else {
                Category::create(array(
                        'name' => Input::get('name'),
                        'description' => Input::get('description'),
                        'sort_by' => Input::get('sort_by')
                    )
                );
            }
            //$root = Category::create(array('name' => 'Electronic Components'));
            //$root->children()->create(array('name' => 'PCBA'));
            //$root->children()->create(array('name' => 'Resistors'));

            if(is_numeric($parent_id)){
                return Redirect::to('product_categories/show/'.$parent_id)
                    ->with('flash_success','Operation success');
            } else {
                return Redirect::to('product_categories')
                    ->with('flash_success','Operation success');
            }

        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if($id == NULL){
            $category = NULL;
            $categories = Category::roots()->get();
            $ancestors = NULL;
        } else {
            $category = Category::where('id', '=', $id)->first();
            $ancestors = $category->ancestorsAndSelf()->get();
            $categories = $category->immediateDescendants()->get();
        }
        $category_id = $id;
        return view('settings.product_categories.index',compact('category','category_id','ancestors','categories'));
    }

    public function getUpdate($id = NULL) {

        if($id == NULL){
            $category = NULL;
            $categories = Category::roots()->get();
            $ancestors = NULL;
        } else {
            $category = Category::where('id', '=', $id)->first();
            $ancestors = $category->ancestorsAndSelf()->get();
            $categories = $category->immediateDescendants()->get();
        }

//        return $category->images->count();

        $category_id = $id;
//        $images = CategoryImage::where('category_id',$category->id)->get();
//        $images = CategoryImage::all();

        return view('settings.product_categories.update',compact('category','category_id','ancestors','categories'));

//        $this->layout->module_title = "";
//        $this->layout->module_sub_title = "";
//        $this->layout->content = View::make('product_categories.update')
//            ->with('category',$category)
//            ->with('category_id',$id)
//            ->with('ancestors',$ancestors)
//            ->with('categories', $categories);
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
            'category_id' => 'required|integer|digits_between:1,6',
            'name' => "required|between:1,100",
            'description' => 'between:1,1000',
            'sort_by' => 'integer'
        );

        $input = $request->all();

        $validation = Validator::make($input, $rules);

        if($validation->fails()){
            return Redirect::to('product_categories/update/'.$id)
                ->with('flash_error','Operation failed')
                ->withErrors($validation->Messages())
                ->withInput();
        } else {
            $category_id = $id;
            $category = Category::findOrFail($category_id);
            $category->name = $request->name;
            $category->sort_by = $request->sort_by;
            $category->visible = $request->visible;
            $category->description = $request->description;
            $category->description_localized = $request->description_localized;
            $category->name_localized = $request->name_localized;

            $public_folder 	= config('app.public_folder') . "categories/";

            if($file = $request->file('banner')){
                $file_extension 	= $file->getClientOriginalExtension();
//                $file_original_name = $file->getClientOriginalName();
                $file_name 			= $category->id . "_banner." . $file_extension;
                $file->move($public_folder, $file_name);

                $category->banner = $file_name;
            }

            if($file = $request->file('picture')){
                $file_extension 	= $file->getClientOriginalExtension();
//                $file_original_name = $file->getClientOriginalName();
                $file_name 			= $category->id . "_picture." . $file_extension;
                $file->move($public_folder, $file_name);

                $category->picture = $file_name;
            }

            if($request->has('delete_banner')){
                if($category->banner != ""){
                    if(file_exists($public_folder . $category->banner)){
                        unlink($public_folder . $category->banner);
                        $category->banner = "";
                    }
                }
            }

            if($request->has('delete_picture')){
                if($category->picture != ""){
                    if(file_exists($public_folder . $category->picture)){
                        unlink($public_folder . $category->picture);
                        $category->picture = "";
                    }
                }
            }

            $category->save();

            return Redirect::to('/settings/product_categories/update/'.$id)
                ->with('flash_success','Operation success');
        }
    }

    public function updateAddThumbnail(Request $request,$id){

        if(!$request->file('picture')){
            return Redirect::to('/settings/product_categories/update/'.$id)
                ->with('flash_error','Operation failed');
        }

        $category = Category::findOrFail($id);

        $public_folder 	= config('app.public_folder') . "categories/";

        if($file = $request->file('picture')){
            $file_extension 	= $file->getClientOriginalExtension();
//            $file_original_name = $file->getClientOriginalName();
            $file_name 			= $category->id . "_thumbnail_" . uniqid() . "." . $file_extension;
            $file->move($public_folder, $file_name);
        }

        $record = new CategoryImage();
        $record->category_id = $id;
        $record->date_added  = date("Y-m-d");
        $record->picture     = $file_name;
        $record->save();

        return Redirect::to('/settings/product_categories/update/'.$id)
            ->with('flash_success','Operation success');
    }


    public function updateDownloadableFile(Request $request,$id){

        $category 	= Category::findOrFail($id);

        $public_folder 	= config('app.public_folder') . "categories/";

        $downloads  = $request->downloads;

        if(!is_array($downloads)){
            die("Invalid data");
        }
        foreach($downloads as $download_id => $data){

            if($download_id == 0){ continue; }
            $category_download = CategoryDownload::findOrFail($download_id);
            $category_download->sort_no 	= $data['sort_no'];
            $category_download->description = $data['description'];
            $category_download->login_required = $data['login_required'];
            $category_download->save();

        }

        if(isset($downloads[0])){
            if(isset($downloads[0]['description']) && strlen($downloads[0]['description']) > 0){
                if($file = $request->file('file')){

                    $new = new CategoryDownload();
                    $new->date_added = date("Y-m-d");
                    $new->category_id = $category->id;
                    $new->sort_no 	  = $downloads[0]['sort_no'];
                    $new->description = $downloads[0]['description'];
                    $new->login_required = $downloads[0]['login_required'];

                    $file_extension 	= $file->getClientOriginalExtension();
                    $file_original_name = $file->getClientOriginalName();
                    $file_name 			= $category->id . "_file_" . uniqid() . "." . $file_extension;
                    $file_size 			= $file->getSize();
                    $file_mime 			= $file->getMimeType();
                    $file->move($public_folder, $file_name);

                    $new->file_name = $file_name;
                    $new->original_file_name = $file_original_name;
                    $new->file_size 	= $file_size;
                    $new->mime_type 	= $file_mime;
                    $new->save();
                }
            }
        }

        return Redirect::to('/settings/product_categories/update/'.$category->id)
            ->with('flash_success','Operation success');
    }

    public function updateAttributes(Request $request,$id){

        $category 	= Category::findOrFail($id);

        $attributes = $request->attributes;

        if(!is_array($attributes)){
            die("Invalid data");
        }

        foreach($attributes as $attribute_id => $data){
            if($attribute_id == 0){ continue; }
            $category_attribute = CategoryAttribute::findOrFail($attribute_id);
            $category_attribute->sort_no 	= $data['sort_no'];
            $category_attribute->group  	= $data['group'];
            $category_attribute->name 		= $data['name'];
            $category_attribute->value 		= $data['value'];
            $category_attribute->save();
        }

        if(isset($attributes[0])){

            if(strlen($attributes[0]['name']) > 0 &&
                strlen($attributes[0]['group']) > 0 &&
                strlen($attributes[0]['value']) > 0
            ){
                $new = new CategoryAttribute();
                $new->category_id = $category->id;
                $new->sort_no 	  = $attributes[0]['sort_no'];
                $new->name 		  = $attributes[0]['name'];
                $new->group 	  = $attributes[0]['group'];
                $new->value 	  = $attributes[0]['value'];
                $new->save();
            }

        }

        return Redirect::to('/settings/product_categories/update/'.$category->id)
            ->with('flash_success','Operation success');
    }



    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */


    public function deleteImage($id){
        $category_image = CategoryImage::findOrFail($id);
        $category 		= Category::findOrFail($category_image->category_id);

        $public_folder 	= config('app.public_folder') . "categories/";

        if($category_image->picture != ""){
            if(file_exists($public_folder . $category_image->picture)){
                unlink($public_folder . $category_image->picture);
            }
        }

        $category_image->delete();

        return Redirect::to('/settings/product_categories/update/'.$category->id)
            ->with('flash_success','Operation success');
    }


    public function destroy($id)
    {
        //
    }
}
