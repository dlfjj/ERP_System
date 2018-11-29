<?php
/**
 * Created by PhpStorm.
 * User: jiajiefan
 * Date: 2018/11/28
 * Time: 12:05 PM
 */

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class TopProductExport implements FromView
{
    public $topProduct;

    public function __construct($TopProduct)
    {
        $this->topProduct = $TopProduct;
    }

    public function view(): View
    {
        return view('reports.data_ranking_export_template', $this->topProduct);
    }
}
