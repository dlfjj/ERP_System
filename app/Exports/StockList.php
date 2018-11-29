<?php
/**
 * Created by PhpStorm.
 * User: jiajiefan
 * Date: 2018/11/28
 * Time: 6:01 PM
 */

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;


class StockList implements FromView
{
    public $stock;

    public function __construct($stocklist)
    {
        $this->stock = $stocklist;
    }

    public function view(): View
    {
        return view('reports.inventory.export_template', $this->stock);
    }
}