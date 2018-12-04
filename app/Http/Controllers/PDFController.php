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
use Illuminate\Support\Facades\Storage;


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


    public function purchasePDF($id)
    {
        return $this->pdfService->getPurchaseOrderPdf($id)->inline();
    }

    // function for order confrimation
    public function order_confirmation($id){

        $this->pdfService->getSamplePDF('printouts.order_confirmation',$id);

    }
    public function commercial_invoice($id)
    {
        $this->pdfService->getSamplePDF('printouts.commercial_invoice', $id);
    }


    public function quotation($id){

         $this->pdfService->getSamplePDF('printouts.quotation',$id);

    }

    public function order_acknowledgement($id){

        $this->pdfService->getSamplePDF('printouts.acknowledgement',$id);


    }
    public function package_list($id){

        $this->pdfService->getSamplePDF('printouts.packing_list',$id);

    }
    public function proforma_invoice($id){

        $this->pdfService->getSamplePDF('printouts.proforma_invoice',$id);

    }

    public function downloadPdfFile($filename){
        return Storage::disk('public')->download('pdf_files/'.$filename);
    }


}
