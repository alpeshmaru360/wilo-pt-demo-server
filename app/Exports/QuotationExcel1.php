<?php

namespace App\Exports;

use App\Quotation;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromView;
use App\Helpers\Helper;
use App\User;
use App\Customer;
use App\ControlPanelCart;
use App\Item;
use App\AtmosCart;
use App\AtmosItem;
use App\ScpCart;
use App\Models\BoosterCart;
use App\QuotationCounter;

class QuotationExcel implements FromView
{
    protected $quotation_no;

    function __construct($quotation_no) {
        $this->quotation_no = $quotation_no;
    }
    /**
    * @return \Illuminate\Support\Collection
    */
    public function view():  View
    {
        $quotations = Quotation::where('quotation_number', $this->quotation_no)->get();
        $ids = [];
        $atmosIds = [];
        $scpIds = [];
         $boosterIds = [];

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
              if ($quotation->cart_model_name == 'booster') {
                $boosterIds[] = $quotation->cp_cart_id;
            }
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
		$quotations_revision_no = QuotationCounter::where('quotation_number',$quotations[0]->quotation_number)->first();
        if($quotations_revision_no){
            $quotations_revision_no = $quotations_revision_no->total_revision_number;
        }
        else
        {
            $quotations_revision_no = 0;
        }

        $atmosCartData = AtmosCart::cartDataByQuotation($atmosIds);
        $scpCartData = ScpCart::cartDataByQuotation($scpIds);
        $boosterCartData = BoosterCart::cartDataByQuotation($boosterIds);
        return view('frontend.pdf.quotation_excel', compact('quotations', 'quotations_revision_no','customer', 'controlPanelCartData', 'atmosCartData','scpCartData','boosterCartData'));
    }
    public function collection()
    {
    }
}
