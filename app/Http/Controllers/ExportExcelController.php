<?php

namespace App\Http\Controllers;

use App\Components\Report\Services\expensesByCategory;
use App\Components\Report\Services\stocklist;
use App\Components\Report\Services\topProduct;
use App\Exports\KpiExport;
use App\Exports\TopProductExport;
use App\Exports\UsersExport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Components\Report\Services\KpiService;


class ExportExcelController extends Controller
{
    private $kpiService;
    private $topProduct;
    private $expense;
    private $stockList;

    public function __construct(KpiService $KpiService, topProduct $TopProduct, expensesByCategory $ExpensesByCategory, stocklist $Stocklist){
        $this->middleware('auth');
        has_role('reports',1);

        $this->kpiService = $KpiService;
        $this->topProduct = $TopProduct;
        $this->expense = $ExpensesByCategory;
        $this->stockList = $Stocklist;
    }

//    public function export()
//    {
//        return Excel::download(new UsersExport, 'users.xlsx');
//    }

    public function downloadKpiExcel()
    {
        $kpi_data = $this->kpiService->getKpiByCompany();
        return Excel::download(new KpiExport($kpi_data), 'kpi-report.xlsx');
    }

    public function downloadTopProductExcel(){
        $topProduct = $this->topProduct->topProductByCompany();
        return Excel::download(new TopProductExport($topProduct), 'top-product-report.xlsx');

    }

    public function downloadExpensesByCategory(){
        $expense = $this->expense->getExpensesByCategory();
        return Excel::download(new \App\Exports\ExpensesByCategory($expense), 'Expense.xlsx');

    }

    public function downloadStockList(){
        $stock = $this->stockList->getStocklist();
        return Excel::download(new \App\Exports\StockList($stock), 'current-inventory.xlsx');
    }

}
