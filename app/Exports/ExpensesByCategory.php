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

class ExpensesByCategory implements FromView
{
    public $expense;

    public function __construct($ExpensesByCategory)
    {
        $this->expense = $ExpensesByCategory;
    }

    public function view(): View
    {
        return view('reports.expenses.export_template', $this->expense);
    }
}
