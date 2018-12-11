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

    public function getOrderPdf($view, int $id){

        $pdf_data = $this->pdfRepository->getSamplePdfData($id);

        if(stristr($view,"quotation")){

            $headerHtml = view()->make('printouts.quotations.header')
                ->render();

            $footerHtml = view()->make('printouts.quotations.footer')
                ->with('order',  $pdf_data['order'])
                ->render();
            $pdf_view = 'printouts.quotations.quotation';

        }elseif(stristr($view,"acknowledgement")){

            $headerHtml = view()->make('printouts.acknowledgements.header')
                ->render();

            $footerHtml = view()->make('printouts.acknowledgements.footer')
                ->with('order',  $pdf_data['order'])
                ->render();
            $pdf_view = 'printouts.acknowledgements.acknowledgement';
        }elseif(stristr($view,"confirmation")){

            $headerHtml = view()->make('printouts.confirmations.header')
                ->render();

            $footerHtml = view()->make('printouts.confirmations.footer')
                ->with('order',  $pdf_data['order'])
                ->render();
            $pdf_view = 'printouts.confirmations.confirmation';
        }elseif(stristr($view,"proforma_invoice")){

            $headerHtml = view()->make('printouts.proforma_invoices.header')
                ->render();

            $footerHtml = view()->make('printouts.proforma_invoices.footer')
                ->with('order',  $pdf_data['order'])
                ->render();
            $pdf_view = 'printouts.proforma_invoices.proforma_invoice';
        }elseif(stristr($view,"invoice")) {

            $headerHtml = view()->make('printouts.invoices.header')
                ->render();

            $footerHtml = view()->make('printouts.invoices.footer')
                ->with('order', $pdf_data['order'])
                ->render();
            $pdf_view = 'printouts.invoices.invoice';
        }elseif(stristr($view,"packing_list")) {

            $headerHtml = view()->make('printouts.packing_lists.header')
                ->render();

            $footerHtml = view()->make('printouts.packing_lists.footer')
                ->with('order', $pdf_data['order'])
                ->render();
            $pdf_view = 'printouts.packing_lists.packing_list';
        }else{
            return 'no';
        }



//        view()->share('quotations',$quotations);
        $pdf = SnappyPdf::loadHTML(view($pdf_view,$pdf_data))
            ->setOption('header-html', $headerHtml)
            ->setOption('footer-html', $footerHtml)
            ->setOption('footer-line',true)
            ->setOption('footer-spacing',4)
            ->setOption('header-spacing', 3)
            ->setOption('header-line',true)
            ->setPaper('A4')
            ->setOrientation('portrait')
        ;
        return $pdf;
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