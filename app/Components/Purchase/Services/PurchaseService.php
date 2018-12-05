<?php
/**
 * Created by PhpStorm.
 * User: jiajiefan
 * Date: 2018/11/30
 * Time: 8:48 PM
 */

namespace App\Components\Purchase\Services;
use App\Components\Purchase\Repositories\PurchaseRepository;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\PurchasePayment;
use Validator;
use Redirect;
use Auth;

class PurchaseService
{
    private $purchaseRepository;

    public function __construct(PurchaseRepository $PurchaseRepository)
    {
        $this->purchaseRepository = $PurchaseRepository;
    }
    public function getDuplicate ($purchase_id){

        $original = $this->purchaseRepository->getPurchaseDataById($purchase_id)->toArray();

        $duplicate = new Purchase();
        $duplicate->fill($original);
        $duplicate->created_by = Auth::user()->id;
        $duplicate->updated_by = Auth::user()->id;
        $duplicate->date_required  = "0000-00-00";
        $duplicate->date_confirmed = "0000-00-00";
        $duplicate->date_placed = date("Y-m-d");
        $duplicate->status = 'DRAFT';
        $duplicate->id = null;
        $duplicate->save();

        foreach($this->purchaseRepository->getPurchaseDataById($purchase_id)->items->toArray() as $original_line_item){
            $dupe = new PurchaseItem;
            $dupe->fill($original_line_item);
            $dupe->created_by = Auth::user()->id;
            $dupe->updated_by = Auth::user()->id;
            $dupe->purchase_id = $duplicate->id;
            $dupe->id = null;
            $dupe->save();
        }

        updatePurchaseStatus($duplicate->id);

        return $duplicate->id;
    }

    public function getPaymentDelete($id){

        $payment = $this->purchaseRepository->getPurchasePaymentById($id);
        $purchase = $this->purchaseRepository->getPurchaseDataById($payment->purchase_id);

        if($purchase->company_id != return_company_id()){
            die("Access violation!");
        }

        $payment->delete();

        updatePurchaseStatus($purchase->id);
        return $purchase->id;
    }
}