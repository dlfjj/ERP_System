<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\DownloadUpdateRequest;
use App\Models\Product;
use App\Models\ProductDownload;
use Validator;
use Auth;


class DownloadController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

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
    public function store(DownloadUpdateRequest $request)
    {
            $id = $request->product_id;
            $product = Product::findOrFail($id);
            if($file = $request->file('file_id')){

                    $private_folder = config('app.private_folder') . return_company_id() . "/products/" . $product->id;
                    if (!file_exists($private_folder)) {
                        @mkdir($private_folder);
                    }
                    if (!file_exists($private_folder)) {
                        die("Fatal error ERR_MKDIR_DENIED");
                    }

                    $file_extension = $file->getClientOriginalExtension();
                    $file_name = uniqid() . "." . $file_extension;
                    $file_original_name = $file->getClientOriginalName();
                    $file_original_name = preg_replace('/\s+/', '_', $file_original_name);
                    $file_size = $file->getSize();
                    $mime_type = $file->getMimeType();
                    $file->move($private_folder, $file_name);
                    $new_product_attachment = New ProductDownload();
                    $new_product_attachment->description = $request->get('description');
                    $new_product_attachment->product_id = $product->id;
                    if ($file) {
                        $new_product_attachment->file_name = $file_name;
                        $new_product_attachment->original_file_name = $file_original_name;
                        $new_product_attachment->file_size = $file_size;
                        $new_product_attachment->login_required = $request->get('login_required');
                        $new_product_attachment->mime_type = $mime_type;
                    }
                    $new_product_attachment->created_by = Auth::user()->id;
                    $new_product_attachment->updated_by = Auth::user()->id;
                    $new_product_attachment->date_added = date("Y-m-d");
                    $new_product_attachment->save();
            } else {
                    return redirect('/products/downloads/'.$id)
                        ->with('flash_error','No File Attach')
                        ->withInput();
            }
                return redirect()->back()
                    ->with('flash_success','Operation success');
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
        $select_yesno = ["No" => 'No', "Yes" => 'Yes'];

        $downloads = ProductDownload::where('product_id',$product->id)->get();

        return view('products.downloads.show', compact('product','downloads','select_yesno'));
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $download = ProductDownload::findOrFail($id);
        $product_id = $download->product_id;
        $product = Product::findOrFail($product_id);

        $private_folder = config('app.private_folder') . return_company_id() . "/products/" . $product->id . "/";
        $full_path = $private_folder . $download->file_name;

        if(file_exists($full_path)){
            unlink($full_path);
        }

        $download->delete();

        return redirect()->back()
            ->with('flash_success','Operation success');
    }

    public function downloadFile($id){

        $attachment = ProductDownload::findOrFail($id);
        $product    = Product::findOrFail($attachment->product_id);

        $private_folder = config('app.private_folder') . return_company_id() . "/products/" . $product->id . "/";

        $full_path = $private_folder . $attachment->file_name;

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
            return redirect()->back()
                ->with('flash_error','File not found');
        }
    }
}
