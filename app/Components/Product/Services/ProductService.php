<?php

namespace App\Components\Product\Services;

use App\Components\Exceptions\StatusChangeDeniedException;
use App\Components\Product\Exceptions\MPNAlreadyExistExceptions;
use App\Components\Product\Repositories\ProductRepository;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductPrice;
use App\Models\User;
use App\Models\ValueList;
use Illuminate\Http\Request;
use Config;

class ProductService
{
    private $productRepository;

    public function __construct(ProductRepository $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    public function getAllProductsByCompanyId(int $companyId)
    {
        return $this->productRepository->getAllProductsByCompanyId($companyId);
    }

    public function getProductById(int $productId)
    {
        $product = Product::findOrFail($productId);


        if($product->company_id != return_company_id()){
            die("Access violation. Click <a href='/'>here</a> to get back.");
        }

        $company_id = return_company_id();
        $tree = Category::all()->toHierarchy();
        $select_categories = printSelect($tree,$product->category_id);
        $select_currency_codes = ValueList::where('uid','=','currency_codes')->orderBy('name', 'asc')->pluck('name','name');
        $select_uom = ValueList::where('uid','=','uom')->orderBy('name', 'asc')->pluck('name','name');
        $select_manufacturer = ValueList::where('uid','=','manufacturer')->orderBy('name', 'asc')->pluck('name','name');
        $select_package = ValueList::where('uid','=','package')->orderBy('name', 'asc')->pluck('name','name');
        $select_origin = ValueList::where('uid','=','origin')->orderBy('name', 'asc')->pluck('name','name');
        $select_users = User::pluck('username','id');

        $group_prices = ProductPrice::where('product_id',$product->id)->where('company_id',return_company_id())->orderBy('customer_group_id','DESC')->get();

        $created_by_user = User::find($product->created_by)->username;
        $updated_by_user = User::find($product->updated_by)->username;

        return [
            'product' => $product,
            'select_uom' => $select_uom,
            'select_manufacturer' => $select_manufacturer,
            'select_package' => $select_package,
            'select_origin' => $select_origin,
            'select_users' => $select_users,
            'select_categories' => $select_categories,
            'select_currency_codes' => $select_currency_codes,
            'company_id' => $company_id,
            'group_prices' => $group_prices,
            'created_by_user' => $created_by_user,
            'updated_by_user' => $updated_by_user,
        ];
    }

    public function update(int $id, array $data, Request $request)
    {
        $product = Product::findOrFail($id);

        if ($product->status != $request->get('status') && !has_role('products_change_status')) {
            throw new StatusChangeDeniedException();
        }

        // Check for duplicate MPN
        if ($data['mpn'] != "") {
            $dupe = Product::where('mpn', $data['mpn'])
                ->where('company_id', return_company_id())
                ->where('id', '!=', $product->id)
                ->first();
            if ($dupe) {
                throw new MPNAlreadyExistExceptions();
            }
        }

        if ($product->parent_id != null) {
            $product->product_name_local = $request->get('product_name_local');
            $product->description_local = $request->get('description_local');
        } else {
            $product->fill($data);
        }
        $product->updated_by = currentUserId();

        if ($request->hasFile('picture')) {
            $picture = $request->file('picture');
            $public_folder = Config::get('app.public_folder') . "products/";
            $picture_extension = $picture->getClientOriginalExtension();
            $picture->move($public_folder, md5($id) . "." . $picture_extension);
            $product->picture = md5($id) . "." . $picture_extension;
        }

        $product->save();

        return true;
    }
}
