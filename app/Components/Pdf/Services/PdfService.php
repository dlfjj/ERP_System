<?php
/**
 * Created by PhpStorm.
 * User: jiajiefan
 * Date: 2018/11/29
 * Time: 10:16 AM
 */

namespace App\Components\Pdf\Services;

use App\Components\Pdf\Repositories\PdfRepository;
use PDF;
use Dompdf\Dompdf;
use Validator;
use Auth;
use SnappyPdf;



class PdfService
{
    private $pdfRepository;

    public function __construct(PdfRepository $PdfRepository)
    {
        $this->pdfRepository = $PdfRepository;
    }

    public function getSamplePDF($view, int $id){

        $dompdf = new Dompdf();

        $dompdf->loadHtml(view($view,$this->pdfRepository->getSamplePdfData($id)));

// (Optional) Setup the paper size and orientation
        $dompdf->setPaper('A4', 'portrait');

// Render the HTML as PDF
        $dompdf->render();

// Output the generated PDF to Browser
        $dompdf->stream("dompdf_out.pdf", array("Attachment" => false));

        exit(0);


    }

    public function getPurchaseOrderPdf(int $id){


        //        return $this->pdfService->getPurchaseOrderPdf($id)['purchase']->company->po_footer;
        $pdf = SnappyPdf::loadHTML(view('printouts.purchases.po',$this->pdfRepository->getPurchaseOrderPdfData($id)))
            ->setOption('header-html', $this->pdfRepository->getPurchaseOrderPdfData($id)['headerHtml'])
            ->setOption('footer-html', $this->pdfRepository->getPurchaseOrderPdfData($id)['footerHtml'])
//            ->setOption('footer-center',"Page [page] of [toPage]")
//            ->setOption('footer-font-size','9')
//            ->setOption('footer-right','www.americandunnage.com')
//            ->setOption('footer-left', $purchase->company->bill_to)
            ->setOption('footer-line',true)
            ->setOption('footer-spacing',4)
            ->setOption('header-spacing', 3)
            ->setOption('header-line',true)
            ->setPaper('A4')
            ->setOrientation('portrait');
//        $pdf = SnappyPdf::loadHTML(view('printouts.purchases.po',compact('purchase','vendor')))->setPaper('a4')->setOrientation('portrait')->setOption('margin-bottom', 0);
        return $pdf;
//        return view('printouts.purchases.po');



    }
}