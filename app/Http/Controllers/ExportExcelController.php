<?php

namespace App\Http\Controllers;

use App\Exports\KpiExport;
use App\Exports\UsersExport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Components\Report\Services\KpiService;


class ExportExcelController extends Controller
{
    private $kpiService;
    public function __construct(KpiService $KpiService){
//        $this->middleware('auth');
//        has_role('reports',1);

        $this->kpiService = $KpiService;
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

    }
}
