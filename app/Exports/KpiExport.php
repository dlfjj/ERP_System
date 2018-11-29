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

class KpiExport implements FromView
{
    public $kpi_data;

    public function __construct($kpi_data)
    {
        $this->kpi_data = $kpi_data;
    }

    public function view(): View
    {

        return view('reports.kpis.export_template', $this->kpi_data);
    }
}
