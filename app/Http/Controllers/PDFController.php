<?php

namespace App\Http\Controllers;

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

class PDFController extends Controller
{
    /* function for purchase details pdf*/
    private $pdfService;

    public function __construct(PdfService $PdfService){
        $this->middleware('auth');

        $this->pdfService = $PdfService;
    }


    //for testing and future learning
//    public function pdfview(Request $request)
//    {
//        $users = DB::table("users")->get();
//        view()->share('users',$users);
//
//        if($request->has('download')) {
//            // pass view file
//            $pdf = PDF::loadView('pdfview');
//            // download pdf
//            return $pdf->download('userlist.pdf');
//        }
//        return view('pdfview');
//    }


//    public function pdfview($id)
//    {
//        $purchase = Purchase::findOrFail($id);
//
//        $vendor = Vendor::findOrFail($purchase->vendor_id);
//
////        foreach($purchase->items as $oi){
////            return $oi->product->product_name;
////        }
//
////        view()->share(compact('purchase','vendor'));
//
//        $headerHtml = view()->make('printouts.purchases.header')
//            ->render();
//
//        $footerHtml = view()->make('printouts.purchases.footer')
//            ->with('purchase',  $purchase)
//            ->render();
//
//        $pdf = SnappyPdf::loadHTML(view('printouts.purchases.po',compact('purchase','vendor')))
//            ->setOption('header-html', $headerHtml)
//            ->setOption('footer-html', $footerHtml)
//            ->setOption('footer-center',"Page [page] of [toPage]")
//            ->setPaper('A4')
//            ->setOrientation('portrait');
////        $pdf = SnappyPdf::loadHTML(view('printouts.purchases.po',compact('purchase','vendor')))->setPaper('a4')->setOrientation('portrait')->setOption('margin-bottom', 0);
//        return $pdf->inline();
////        return view('printouts.purchases.po');
//
//    }

    public function purchasePDF($id)
    {

        $purchase = Purchase::findOrFail($id);

        $vendor = Vendor::findOrFail($purchase->vendor_id);

        $headerHtml = view()->make('printouts.purchases.header')
            ->render();

        $footerHtml = view()->make('printouts.purchases.footer')
            ->with('purchase',  $purchase)
            ->render();


        $pdf = SnappyPdf::loadHTML(view('printouts.purchases.po',compact('purchase','vendor')))
            ->setOption('header-html', $headerHtml)
            ->setOption('footer-html', $footerHtml)
//            ->setOption('footer-center',"Page [page] of [toPage]")
//            ->setOption('footer-font-size','9')
//            ->setOption('footer-right','www.americandunnage.com')
//            ->setOption('footer-left', $purchase->company->bill_to)
            ->setOption('footer-line',true)
//            ->setOption('footer-spacing',0)
            ->setPaper('A4')
            ->setOrientation('portrait');
//        return dd($pdf);
//        $pdf = SnappyPdf::loadHTML(view('printouts.purchases.po',compact('purchase','vendor')))->setPaper('a4')->setOrientation('portrait')->setOption('margin-bottom', 0);
        return $pdf->inline();
//        return view('printouts.purchases.po');
    }
    // function for order confrimation
    public function order_confirmation($id){

        $dompdf = new Dompdf();
        $dompdf->loadHtml(view('printouts.order_confirmation',$this->pdfService->getSamplePDF($id)));
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        $dompdf->stream("dompdf_out.pdf", array("Attachment" => false));

    }
    public function commercial_invoice($id){

        $dompdf = new Dompdf();
        $dompdf->loadHtml(view('printouts.commercial_invoice',$this->pdfService->getSamplePDF($id)));
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        $dompdf->stream("dompdf_out.pdf", array("Attachment" => false));

        exit(0);}

    /**
     * @param $id
     */
    public function quotation($id){


        $dompdf = new Dompdf();

        $dompdf->loadHtml(view('printouts.quotation',$this->pdfService->getSamplePDF($id)));

// (Optional) Setup the paper size and orientation
        $dompdf->setPaper('A4', 'portrait');

// Render the HTML as PDF
        $dompdf->render();

// Output the generated PDF to Browser
        $dompdf->stream("dompdf_out.pdf", array("Attachment" => false));
        exit(0);

    }

    public function order_acknowledgement($id){

        $dompdf = new Dompdf();
        $dompdf->loadHtml(view('printouts.acknowledgement',$this->pdfService->getSamplePDF($id)));
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        $dompdf->stream("dompdf_out.pdf", array("Attachment" => false));
        exit(0);

    }
    public function package_list($id){

        $dompdf = new Dompdf();
        $dompdf->loadHtml(view('printouts.packing_list',$this->pdfService->getSamplePDF($id)));
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        $dompdf->stream("dompdf_out.pdf", array("Attachment" => false));
        exit(0);
    }
    public function proforma_invoice($id){

        $dompdf = new Dompdf();
        $dompdf->loadHtml(view('printouts.proforma_invoice',$this->pdfService->getSamplePDF($id)));
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        $dompdf->stream("dompdf_out.pdf", array("Attachment" => false));
        exit(0);
    }



}
