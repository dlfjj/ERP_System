<?php

namespace App\Http\Controllers;

use App\Components\Pdf\Repositories\PdfRepository;
use App\Components\Pdf\Services\PdfService;
use App\Models\Setting;
use Illuminate\Http\Request;

use App\Http\Requests;
use PDF;
use Dompdf\Dompdf;
use App\Models\PurchaseItem;
use App\Models\Purchase;
use App\Models\PurchaseDelivery;
use App\Models\Vendor;
use App\Models\User;
use App\Models\ValueList;
use App\Models\Taxcode;
use App\Models\ChartOfAccount;
use App\Models\Product;
use App\Models\Customer;
use Yajra\Datatables\Datatables;
use Validator;
use Auth;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderStatus;
use App\Models\PaymentTerm;
use App\Models\Company;
use DB;
use SnappyPdf;
use Illuminate\Support\Facades\Storage;


class PDFController extends Controller
{
    /* function for purchase details pdf*/
    private $pdfService;
    private $pdfRepository;

    public function __construct(PdfService $PdfService, PdfRepository $PdfRepository){

        $this->middleware('auth');

        $this->pdfService = $PdfService;
        $this->pdfRepository = $PdfRepository;
    }


    //for testing and future learning, you have to create your own route for this
//    public function pdfview($id)
//    {
////        $users = DB::table("users")->get();
//
//        $quotation = $this->pdfRepository->getSamplePdfData($id);
//
//        $headerHtml = view()->make('printouts.header')
//            ->render();
//
//        $footerHtml = view()->make('printouts.footer')
//            ->with('order',  $quotation['order'])
//            ->render();
//
////        view()->share('quotations',$quotations);
//        $pdf = SnappyPdf::loadHTML(view('printouts.quotation_testing',$quotation))
//            ->setOption('header-html', $headerHtml)
//            ->setOption('footer-html', $footerHtml)
//            ->setOption('footer-line',true)
//            ->setOption('footer-spacing',4)
//            ->setOption('header-spacing', 3)
//            ->setOption('header-line',true)
//            ->setPaper('A4')
//            ->setOrientation('portrait')
//            ;
//        return $pdf->inline();
//    }




    public function purchasePDF($id)
    {
        return $this->pdfService->getPurchaseOrderPdf($id)->inline();
    }

    // function for order confrimation
    public function order_confirmation($id){

//        $this->pdfService->getSamplePDF('printouts.order_confirmation',$id);
        return $this->pdfService->getOrderPdf('confirmation',$id)->inline();

    }
    public function commercial_invoice($id)
    {
//        $this->pdfService->getSamplePDF('printouts.commercial_invoice', $id);
        return $this->pdfService->getOrderPdf('invoice',$id)->inline();
    }


    public function quotations($id){

//         $this->pdfService->getSamplePDF('printouts.quotations',$id);
        return $this->pdfService->getOrderPdf('quotations',$id)->inline();

    }

    public function order_acknowledgement($id){

        return $this->pdfService->getOrderPdf('acknowledgement',$id)->inline();
//        $this->pdfService->getSamplePDF('printouts.acknowledgement',$id);


    }
    public function package_list($id){

//        $this->pdfService->getSamplePDF('printouts.packing_list',$id);

        return $this->pdfService->getOrderPdf('packing_list',$id)->inline();

    }
    public function proforma_invoice($id){

        return $this->pdfService->getOrderPdf('proforma_invoice',$id)->inline();
//        $this->pdfService->getSamplePDF('printouts.proforma_invoice',$id);

    }

    public function downloadPdfFile($filename){
        try {
            return Storage::disk('public')->download('pdf_files/'.urldecode($filename));
        }
        catch (\Exception $e){
            return redirect()->back()->with('flash_error','PDF File is too old, did not store at the database anymore');
        }
//        return Storage::disk('public')->download('pdf_files/'.urldecode($filename));
    }


}
