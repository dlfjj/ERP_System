<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Http\Request;
use App\Http\Requests\ImageUpdateRequest;
use Illuminate\Support\Facades\Config;
use Auth;
use Validator;

class ImageController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct(){
        $this->middleware('auth');
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
    public function store(ImageUpdateRequest $request)
    {

        $product = Product::findOrFail($request->product_id);
//        $rules = array(
//            'id' => 'integer|digits_between:1,6',
//            'file' => 'max:50000|required'
//        );
//        $validation = Validator::make(Input::all(), $rules);

        if($file = $request->file('image_id')){

            $input = $request->all();
            $private_folder =  config('app.private_folder') . return_company_id() . "/products/" . $product->id;

            if(!file_exists($private_folder)){
                @mkdir($private_folder);
            }
            if(!file_exists($private_folder)){
                die("Fatal error ERR_MKDIR_DENIED");
            }

            $file_extension = $file->getClientOriginalExtension();
            $file_name = uniqid() . ".".$file_extension;
            $file_original_name = $file->getClientOriginalName();
            $file_original_name = preg_replace('/\s+/', '_', $file_original_name);
            $file_size = $file->getSize();
            $mime_type = $file->getMimeType();
            $file->move($private_folder, $file_name);


            $new_product_attachment = New ProductImage();
            $new_product_attachment->product_id = $product->id;
            if($file){
                $new_product_attachment->picture = $file_name;
                $new_product_attachment->original_file_name = $file_original_name;
                $new_product_attachment->file_size= $file_size;
                $new_product_attachment->login_required = "No";
                $new_product_attachment->mime_type = $mime_type;
                $new_product_attachment->seo_keyword = $request->get('seo_keyword');
            }
            $new_product_attachment->created_by = Auth::user()->id;
            $new_product_attachment->updated_by = Auth::user()->id;
            $new_product_attachment->save();

//            return redirect('products/images/'.$request->product_id)
            return redirect()->back()->with('flash_success','Operation success');
        } else {
            return redirect('/products/images/'.$request->product_id)
                ->with('flash_error',"No file selected");
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
        $product = Product::findOrFail($id);
        $select_yesno = [0 => 'No', 1 => 'Yes'];
        $images = ProductImage::where('product_id',$product->id)->get();

        return view('products.images.show',compact('product','images','select_yesno'));

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $attachment = ProductImage::findOrFail($id);
        $product    = Product::findOrFail($attachment->product_id);
        $private_folder = Config::get('app.private_folder') . return_company_id() . "/products/" . $product->id . "/";
        $full_path = $private_folder . $attachment->picture;

        $company_id 	= return_company_id();
        if($company_id != $product->company_id){
            die("Permission issue");
        }

        return view('products.images.update_image', compact('product','attachment','company_id'));

//        $this->layout->content = View::make('products.update_image')
//            ->with('product',$product)
//            ->with('attachment',$attachment)
//            ->with('company_id',$company_id)
//        ;
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

        $attachment = ProductImage::findOrFail($id);
        $product    = Product::findOrFail($attachment->product_id);

        $company_id 	= return_company_id();
        if($company_id != $product->company_id){
            die("Permission issue");
        }

        $rules = array(
            'id' => 'integer|digits_between:1,6',
        );
        $input = $request->all();
        $validation = Validator::make($input, $rules);

        $private_folder = Config::get('app.private_folder') . return_company_id() . "/products/" . $product->id . "/";

        if(!file_exists($private_folder)){
            @mkdir($private_folder);
        }
        if(!file_exists($private_folder)){
            die("Fatal error ERR_MKDIR_DENIED");
        }


        if($validation->fails()){
            return redirect()
                ->back()
                ->with('flash_error','Operation failed')
                ->withErrors($validation->Messages())
                ->withInput();
        } else {
            if($file = $request->file('image_id')){
                $file_extension = $file->getClientOriginalExtension();
                $file_name = uniqid() . ".".$file_extension;
                $file_original_name = $file->getClientOriginalName();
                $file_original_name = preg_replace('/\s+/', '_', $file_original_name);
                $file_size = $file->getSize();
                $mime_type = $file->getMimeType();
                $file->move($private_folder, $file_name);
                if($file){

                    if(file_exists($private_folder . $attachment->picture) && $attachment->picture != ""){
                        @unlink($private_folder . $attachment->picture);
                    }

                    $attachment->picture = $file_name;
                    $attachment->original_file_name = $file_original_name;
                    $attachment->file_size= $file_size;
                    $attachment->mime_type = $mime_type;
                }
            }
            $attachment->seo_keyword = $request->get('seo_keyword');
            $attachment->updated_by = Auth::user()->id;
            $attachment->save();


            return redirect('products/images/'.$product->id)
                ->with('flash_success','Operation success')
                ;
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
        $download = ProductImage::findOrFail($id);
        $product_id = $download->product_id;
        $product = Product::findOrFail($product_id);

        if($download->picture == $product->picture){
            $product->picture = "";
        }

        $private_folder = Config::get('app.private_folder') . return_company_id() . "/products/" . $product->id . "/";
        $full_path = $private_folder . $download->picture;

        if(file_exists($full_path)){
            unlink($full_path);
        }

        $product->save();
        $download->delete();

        return redirect()->back()->with('flash_success','Operation success');
    }

    public function downloadImage($id){
        $attachment = ProductImage::findOrFail($id);
        $product    = Product::findOrFail($attachment->product_id);

        $private_folder = Config::get('app.private_folder') . return_company_id() . "/products/" . $product->id . "/";

        $full_path = $private_folder . $attachment->picture;

        if(file_exists($full_path)){
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename='.basename($full_path));
            header('Content-Transfer-Encoding: binary');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($full_path));
            ob_clean();
            flush();
            readfile($full_path);
            exit;
        } else {
            return redirect('products/images/'.$product->id)
                ->with('flash_error','File not found');
        }
    }


//    use ajax request to call the route
    public function getMarkAsMainImage($image_id){

        $image   = ProductImage::where('id',$image_id)->first();
        $product = Product::findOrFail($image->product_id);

        $product->picture = $image->picture;
        $product->save();

//        return redirect('products/images/'.$product->id)
//            ->with('flash_success','Operation success');
    }

    public function getUnmarkAsMainImage($image_id){

        $image   = ProductImage::where('id',$image_id)->first();
        $product = Product::findOrFail($image->product_id);

        $product->picture = "";
        $product->save();

    }
}
