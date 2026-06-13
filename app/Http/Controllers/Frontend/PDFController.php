<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use PDF;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Helpers\Helper;
use App\User;
use App\Customer;
use App\ControlPanelCart;
use App\Quotation;
use App\Item;
use DB;
use App\Models\FireFighting\FireFightingCarts;
use App\AtmosCart;
use App\AtmosItem;
use App\ScpCart;
use App\ScpvCart; // A Code: 26-02-2026
use App\Models\BoosterCart;
use App\Exports\QuotationExcel;
use Excel;
use App\QuotationCounter;

class PDFController extends Controller {

    public function controlPanelQuotation($quotation_no) {
        $quotations = Quotation::where('quotation_number', $quotation_no)->get();
        $ids = [];
        $atmosIds = [];
        $scpIds = [];
        $scpvIds = []; // A Code: 26-02-2026
        $boosterIds = [];
		$firefightingIds = [];

        $customer = Customer::find($quotations[0]->customer_id);
        foreach ($quotations as $quotation) {
            if ($quotation->cart_model_name == 'controlpanel') {
                $ids[] = $quotation->cp_cart_id;
            }
            if ($quotation->cart_model_name == 'atmos') {
                $atmosIds[] = $quotation->cp_cart_id;
            }
            if ($quotation->cart_model_name == 'scp') {
                $scpIds[] = $quotation->cp_cart_id;
            }
            // A Code: 26-02-2026 Start
            if ($quotation->cart_model_name == 'scpv') {
                $scpvIds[] = $quotation->cp_cart_id;
            }
            // A Code: 26-02-2026 End
            if ($quotation->cart_model_name == 'booster') {
                $boosterIds[] = $quotation->cp_cart_id;
            }
			if ($quotation->cart_model_name == 'firefighting') {
                $firefightingIds[] = $quotation->cp_cart_id;
            }
        }
        
		$quotations_revision_no = QuotationCounter::where('quotation_number',$quotation_no)->first();
        if($quotations_revision_no){
            $quotations_revision_no = $quotations_revision_no->total_revision_number;
        }
        else
        {
            $quotations_revision_no = 0;
        }
        $controlPanelCartData = ControlPanelCart::whereIn('id', $ids)
                ->with('powers')
                ->with('voltages')
                ->with('applications')
                ->with('ambienttemps')
                ->with('startertypes')
                ->with('components')
                ->with('ranges')
                ->with('enclousres')
                ->with('comunicationprotocols')
                ->with('ipratings')
                ->get();
        $atmosCartData = AtmosCart::cartDataByQuotation($atmosIds);
        $scpCartData = ScpCart::cartDataByQuotation($scpIds);
        $scpvCartData = ScpvCart::cartDataByQuotation($scpvIds); // A Code: 26-02-2026
        $boosterCartData = BoosterCart::cartDataByQuotation($boosterIds);
        $firefightingCartData = FireFightingCarts::cartDataByQuotation($firefightingIds);
        
        $isPDF = true;
        $pdf = PDF::loadView('frontend.pdf.quotation_pdf', compact('quotations', 'quotations_revision_no', 'customer', 'controlPanelCartData', 'atmosCartData', 
        'scpCartData', 'scpvCartData', 'boosterCartData', 'firefightingCartData')); // A Code: 26-02-2026
        $pdf->getDomPDF()->set_option("enable_php", true);
        $pdfName = 'quotation_' . Carbon::now()->format('m/d/Y h:i:s') . '.pdf';
        return $pdf->stream($pdfName);
    }

    public function controlPanelQuotationExcel($quotation_no) {
        $excel_file = 'quotation_' . Carbon::now()->format('m-d-Y h:i:s') . '.xlsx';
        return Excel::download(new QuotationExcel($quotation_no),$excel_file);
    }
}
