<?php

namespace App\Components\Product\Repositories;

use App\Models\Product;

class ProductRepository
{
    private $product;

    public function getAllProductsByCompanyId(int $companyId)
    {
        $this->product = Product::where('company_id',$companyId)->get();

        return $this->product;
    }
}
