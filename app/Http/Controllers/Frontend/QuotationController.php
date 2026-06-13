<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use App\Http\Requests\GetDataRequest;
use App\Cart;
use Excel;
use App\ControlPanel;
use App\Traits\ControlPanelModelIdGet;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use DB;
use App\Models\FireFighting\FireFightingCarts;
use App\Customer;
use App\ControlPanelCart;
use App\Helpers\Helper;
use App\Quotation;
use App\Item;
use App\AtmosCart;
use App\AtmosItem;
use App\ScpCart;
use App\ScpvCart; // A Code: 24-02-2026
use App\QuotationCounter;
use App\Models\BoosterCart;

class QuotationController extends Controller
{
    public function index($quotation_no)
    {
        $quotations = Quotation::where('quotation_number', $quotation_no)->get();
        $ids = [];
        $atmosIds = [];
        $scpIds = [];
        $scpvIds = []; // A Code: 24-02-2026
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
            // A Code: 24-02-2026 Start
            if ($quotation->cart_model_name == 'scpv') {
                $scpvIds[] = $quotation->cp_cart_id;
            }
            // A Code: 24-02-2026 End
            if ($quotation->cart_model_name == 'booster') {
                $boosterIds[] = $quotation->cp_cart_id;
            }
            if ($quotation->cart_model_name == 'firefighting') {
                $firefightingIds[] = $quotation->cp_cart_id;
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

        $atmosCartData = AtmosCart::cartDataByQuotation($atmosIds);
        $scpCartData = ScpCart::cartDataByQuotation($scpIds);
        $scpvCartData = ScpvCart::cartDataByQuotation($scpvIds); // A Code: 24-02-2026
        $boosterCartData = BoosterCart::cartDataByQuotation($boosterIds);
        $firefightingCartData = FireFightingCarts::cartDataByQuotation($firefightingIds);
        return view('frontend.quotation.index', compact('quotation', 'customer', 'controlPanelCartData', 'atmosCartData', 'scpCartData', 
        'scpvCartData', 'boosterCartData', 'firefightingCartData')); // A Code: 24-02-2026
    }

    // public function userList() 
    // {
    //     $quotations = Quotation::where('user_id', auth()->id())->paginate(100);
    //     $quotationsData = [];
    //     foreach ($quotations as $quotation) {
    //         $c_price = 0;
    //         $s_price = 0;
    //         $b_price = 0;
    //         $a_price = 0;
    //         $price = 0;
    // 		$f_price = 0;

    //         $f_price = FireFightingCarts::where('quotation_no','=',$quotation->quotation_number)->sum('total_price');
    //         $control_panel_price = ControlPanelCart::where('quotation_no','=',$quotation->quotation_number)->get();

    //         if($control_panel_price)
    //         {
    //             foreach($control_panel_price as $c_value)
    //             {
    //                 $c_price += $c_value['total_price'];
    //             }
    //         }
    //         else
    //         {
    //             $c_price = 0;
    //         }
    //         $booster_price = BoosterCart::where('quotation_no','=',$quotation->quotation_number)->get();
    //         if($booster_price)
    //         {
    //             foreach($booster_price as $b_value)
    //             {
    //                 $b_price += $b_value['total_price'];
    //             }
    //         }
    //         else
    //         {
    //             $b_price = 0;
    //         }

    //         $scp_price = ScpCart::where('quotation_no','=',$quotation->quotation_number)->get();
    //         if($scp_price)
    //         {
    //             foreach($scp_price as $s_value)
    //             {
    //                 $s_price += $s_value['total_price'];
    //             }
    //         }
    //         else
    //         {
    //             $s_price = 0;
    //         }

    //         $atmos_price = AtmosCart::where('quotation_no','=',$quotation->quotation_number)->get();
    //         if($atmos_price)
    //         {
    //             foreach($atmos_price as $a_value)
    //             {
    //                 $a_price += $a_value['total_price'];
    //             }
    //         }
    //         else
    //         {
    //             $a_price = 0;
    //         }
    //         $price_data = $c_price+$b_price+$s_price+$a_price+$f_price;

    //         $customer = Customer::find($quotation->customer_id);
    //         // dd($quotation->customer_id);
    //         $quotationsData[$quotation->quotation_number][] = array(
    //             'project_name' => $customer->project_name,
    //             'country' => $customer->country,
    //             'status' => $quotation->status,
    //             'reason' => $quotation->reason,
    //             'modification' => $customer->modification,
    //             'price_data'=>$price_data,
    //         );
    //     }
    //     return view('frontend.quotation.user_list', compact('quotationsData','quotations'));
    // }

    public function userList(Request $request)
    {
        $search = $request->input('search');

        $quotationsQuery = \App\Quotation::query()
            ->where('user_id', auth()->id());

        if (!empty($search)) {
            $quotationsQuery->where(function ($q) use ($search) {
                $q->where('quotation_number', 'like', "%{$search}%")
                    ->orWhereHas('customer', function ($q2) use ($search) {
                        $q2->where('project_name', 'like', "%{$search}%")
                            ->orWhere('country', 'like', "%{$search}%");
                    })
                    ->orWhere('status', 'like', "%{$search}%")
                    ->orWhere('reason', 'like', "%{$search}%");
            });
        }

        // Group before paginate for 100% accurate results
        $paginated = $quotationsQuery
            ->selectRaw('MAX(id) as id, quotation_number')
            ->groupBy('quotation_number')
            ->orderByDesc('id')
            ->paginate(50)
            ->withQueryString(); // Keeps ?search in all links


        // Fetch actual Quotation rows (by grouped id) for details
        $quotationRows = \App\Quotation::whereIn('id', $paginated->pluck('id'))->get()->keyBy('quotation_number');

        $quotationsData = [];
        foreach ($quotationRows as $quotation) {
            $f_price = \App\Models\FireFighting\FireFightingCarts::where('quotation_no', $quotation->quotation_number)->sum('total_price');
            $c_price = \App\ControlPanelCart::where('quotation_no', $quotation->quotation_number)->sum('total_price');
            $b_price = \App\Models\BoosterCart::where('quotation_no', $quotation->quotation_number)->sum('total_price');
            $s_price = \App\ScpCart::where('quotation_no', $quotation->quotation_number)->sum('total_price');
            $sv_price = \App\ScpvCart::where('quotation_no', $quotation->quotation_number)->sum('total_price'); // A Code: 24-02-2026
            $a_price = \App\AtmosCart::where('quotation_no', $quotation->quotation_number)->sum('total_price');
            $price_data = $c_price + $b_price + $s_price + $sv_price + $a_price + $f_price; // A Code: 24-02-2026

            $customer = \App\Customer::find($quotation->customer_id);

            $quotationsData[$quotation->quotation_number] = [
                'project_name' => $customer->project_name ?? '',
                'country' => $customer->country ?? '',
                'status' => $quotation->status,
                'reason' => $quotation->reason,
                'modification' => $customer->modification ?? '',
                'price_data' => $price_data,
            ];
        }

        return view('frontend.quotation.user_list', [
            'quotations' => $paginated,
            'quotationsData' => $quotationsData,
            'search' => $search
        ]);
    }

    public function ajaxStatusUpdate(Request $request)
    {

        $quotationNumber = $request->quotation_no;
        $quotationStatus = $request->status;


        $quotations = Quotation::where('quotation_number', $quotationNumber)
            ->update(['status' => $quotationStatus]);
        $msg = 'Status has been updated.';
        return response()->json(array('success' => true, 'msg' => $msg));
    }

    public function ajaxReasonUpdate(Request $request)
    {

        $quotationNumber = $request->quotation_no;
        $quotationReason = $request->reason;

        $quotations = Quotation::where('quotation_number', $quotationNumber)
            ->update(['reason' => $quotationReason]);

        $msg = 'Reason has been updated.';
        return response()->json(array('success' => true, 'msg' => $msg));
    }

    public function edit($quotation_no)
    {
        $quotations = Quotation::where('quotation_number', $quotation_no)->get();
        $quotations_revision_counter = QuotationCounter::where('quotation_number', $quotation_no)->first();

        if ($quotations_revision_counter) {
            $quotations_revision_counter->total_revision_number = $quotations_revision_counter->total_revision_number + 1;
            $quotations_revision_counter->save();
        } else {
            $quotations_revision_counter = new QuotationCounter;
            $quotations_revision_counter->quotation_number = $quotation_no;
            $quotations_revision_counter->total_revision_number =  $quotations_revision_counter->total_revision_number + 1;
            $quotations_revision_counter->save();
        }

        if ($quotations->isNotEmpty()) {
            $ids = [];
            $atmosIds = [];
            $scpIds = [];
            $scpvIds = []; // A Code: 24-02-2026
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
                // A Code: 24-02-2026 Start
                if ($quotation->cart_model_name == 'scpv') {
                    $scpvIds[] = $quotation->cp_cart_id;
                }
                // A Code: 24-02-2026 End
                if ($quotation->cart_model_name == 'booster') {
                    $boosterIds[] = $quotation->cp_cart_id;
                }
                if ($quotation->cart_model_name == 'firefighting') {
                    $firefightingIds[] = $quotation->cp_cart_id;
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
            // dd($controlPanelCartData);
            $atmosCartData = AtmosCart::cartDataByQuotation($atmosIds);
            $scpCartData = ScpCart::cartDataByQuotation($scpIds);
            $scpvCartData = ScpvCart::cartDataByQuotation($scpvIds); // A Code: 24-02-2026
            $boosterCartData = BoosterCart::cartDataByQuotation($boosterIds);
            $firefightingCartData = FireFightingCarts::cartDataByQuotation($firefightingIds);
            return view('frontend.quotation.edit', compact('quotation', 'customer', 'controlPanelCartData', 'atmosCartData', 'scpCartData', 
                        'scpvCartData', 'boosterCartData', 'quotations_revision_counter', 'firefightingCartData')); // A Code: 24-02-2026
        } else {
            $quotation = "";
            return view('frontend.quotation.edit', ['quotation' => $quotation, 'quotations_revision_counter' => $quotations_revision_counter]);
        }
    }

    public function updatedTotalPrice(Request $request)
    {
        $quotations = Quotation::where('quotation_number', $request->quotation_number)->get();
        $ids = [];
        $atmosIds = [];
        $scpIds = [];
        $scpvIds = []; // A Code: 24-02-2026
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
            // A Code: 24-02-2026 Start
            if ($quotation->cart_model_name == 'scpv') {
                $scpvIds[] = $quotation->cp_cart_id;
            }
            // A Code: 24-02-2026 End
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

        $atmosCartData = AtmosCart::cartDataByQuotation($atmosIds);
        $scpCartData = ScpCart::cartDataByQuotation($scpIds);
        $scpvCartData = ScpvCart::cartDataByQuotation($scpvIds); // A Code: 24-02-2026
        $boosterCartData = BoosterCart::cartDataByQuotation($boosterIds);

        $returnHTML = view('frontend.quotation.qty_updated_total_price')->with('controlPanelCartData', $controlPanelCartData)
            ->with('atmosCartData', $atmosCartData)
            ->with('scpCartData', $scpCartData)
            ->with('scpvCartData', $scpvCartData) // A Code: 24-02-2026
            ->with('boosterCartData', $boosterCartData)
            ->render();
        //
        $data['total_price_updated'] = $returnHTML;

        return response()->json(array('success' => true, 'data' => $data));
    }

    public function deleteCPItemFromEditQuotation(Request $request)
    {
        $data['quotation_item'] = Quotation::where('quotation_number', $request->quotation_number)
            ->where('cp_cart_id', $request->cp_controlpanel_cart_id)
            ->where('cart_model_name', 'controlpanel')
            ->delete();
        $data['controlpanelItem'] = ControlPanelCart::where('quotation_no', $request->quotation_number)
            ->where('id', $request->cp_controlpanel_cart_id)
            ->delete();
        return response()->json(array('success' => true, 'data' => $data, 'message' => 'Control Panel Item Deleted..!!', 'status' => 'success'));
        return response()->json(array('success' => false, 'data' => $data, 'message' => 'Failed to delete..!!', 'status' => 'Failed'));
    }

    public function deleteBoosterItemFromEditQuotation(Request $request)
    {
        $data['quotation_item'] = Quotation::where('quotation_number', $request->quotation_number)
            ->where('cp_cart_id', $request->cp_booster_cart_id)
            ->where('cart_model_name', 'booster')
            ->delete();
        $data['booster_cart'] = BoosterCart::where('quotation_no', $request->quotation_number)
            ->where('id', $request->cp_booster_cart_id)
            ->delete();
        return response()->json(array('success' => true, 'data' => $data, 'message' => 'Booster Item Deleted..!!', 'status' => 'success'));
        return response()->json(array('success' => false, 'data' => $data, 'message' => 'Failed to delete..!!', 'status' => 'Failed'));
    }

    public function deleteAtmosItemFromEditQuotation(Request $request)
    {
        $data['quotation_item'] = Quotation::where('quotation_number', $request->quotation_number)
            ->where('cp_cart_id', $request->cp_atmos_cart_id)
            ->where('cart_model_name', 'atmos')
            ->delete();
        $data['atmos'] = AtmosCart::where('quotation_no', $request->quotation_number)
            ->where('id', $request->cp_atmos_cart_id)
            ->delete();
        return response()->json(array('success' => true, 'data' => $data, 'message' => 'Atmos Item Deleted..!!', 'status' => 'success'));
        return response()->json(array('success' => false, 'data' => $data, 'message' => 'Failed to delete..!!', 'status' => 'Failed'));
    }

    public function deleteSCPItemFromEditQuotation(Request $request)
    {
        $data['quotation_item'] = Quotation::where('quotation_number', $request->quotation_number)
            ->where('cp_cart_id', $request->cp_scp_cart_id)
            ->where('cart_model_name', 'scp')
            ->delete();
        $data['controlpanelItem'] = ScpCart::where('quotation_no', $request->quotation_number)
            ->where('id', $request->cp_scp_cart_id)
            ->delete();
        return response()->json(array('success' => true, 'data' => $data, 'message' => 'SCP Item Deleted..!!', 'status' => 'success'));
        return response()->json(array('success' => false, 'data' => $data, 'message' => 'Failed to delete..!!', 'status' => 'Failed'));
    }

    // A Code: 24-02-2026 Start
    public function deleteSCPVItemFromEditQuotation(Request $request)
    {
        $data['quotation_item'] = Quotation::where('quotation_number', $request->quotation_number)
            ->where('cp_cart_id', $request->cp_scpv_cart_id)
            ->where('cart_model_name', 'scpv')
            ->delete();
        $data['controlpanelItem'] = ScpvCart::where('quotation_no', $request->quotation_number)
            ->where('id', $request->cp_scpv_cart_id)
            ->delete();
        return response()->json(array('success' => true, 'data' => $data, 'message' => 'SCPV Item Deleted..!!', 'status' => 'success'));
        return response()->json(array('success' => false, 'data' => $data, 'message' => 'Failed to delete..!!', 'status' => 'Failed'));
    }
    // A Code: 24-02-2026 End

    // A Code: 26-05-2026 Start
    public function deleteFFItemFromEditQuotation(Request $request)
    {
        $data['quotation_item'] = Quotation::where('quotation_number', $request->quotation_number)
            ->where('cp_cart_id', $request->cp_ff_cart_id)
            ->where('cart_model_name', 'firefighting')
            ->delete();
        $data['firefightingItem'] = FireFightingCarts::where('quotation_no', $request->quotation_number)
            ->where('id', $request->cp_ff_cart_id)
            ->delete();        
        return response()->json(array('success' => true, 'data' => $data, 'message' => 'FireFighting Item Deleted..!!', 'status' => 'success'));
        return response()->json(array('success' => false, 'data' => $data, 'message' => 'Failed to delete..!!', 'status' => 'Failed'));
    }
    // A Code: 26-05-2026 End

    public function home_page($quotation_no)
    {
        $maintance_mode_atmos = DB::table("setup_fields")->where('label', 'atmos_maintance_mode')->pluck('value')[0];
        $maintance_mode_booster = DB::table("setup_fields")->where('label', 'maintance_mode_booster')->pluck('value')[0];
        $control_panel_maintance_mode = DB::table("setup_fields")->where('label', 'control_panel_maintance_mode')->pluck('value')[0];
        $maintance_mode_scp = DB::table("setup_fields")->where('label', 'scp_maintance_mode')->pluck('value')[0];
        $maintance_mode_scpv = DB::table("setup_fields")->where('label', 'scpv_maintance_mode')->pluck('value')[0]; // A Code: 24-02-2026
        $maintance_mode_fire_fighting = DB::table("setup_fields")->where('label', 'fire-fighting_maintance_mode')->pluck('value')[0];
        $maintance_mode_sch = DB::table("setup_fields")->where('label', 'sch_maintance_mode')->pluck('value')[0];
        return view('frontend.dashboard.index', compact('quotation_no', 'maintance_mode_atmos', 'maintance_mode_sch', 'maintance_mode_booster', 'control_panel_maintance_mode', 
                    'maintance_mode_scp', 'maintance_mode_scpv', 'maintance_mode_fire_fighting')); // A Code: 24-02-2026
    }

    public function AddQuotationWithBooster($quotation_no, $total_price, $booster_cart_id)
    {
        $quotation_data = Quotation::where("quotation_number", "=", $quotation_no)->first();
        if ($quotation_data) {
            $quotation = new Quotation;
            $quotation->quotation_number = $quotation_no;
            $quotation->cp_cart_id = $booster_cart_id;
            $quotation->cart_model_name = "booster";
            $quotation->user_id = auth()->user()->id;
            $quotation->customer_id = $quotation_data->customer_id;
            $quotation->total_quotation_value = $total_price;
            $quotation->status = "open";
            $quotation->save();
        }
    }

    public function AddQuotationWithControlPanel($quotation_no, $total_price, $control_panel_cart_id)
    {
        $quotation_data = Quotation::where("quotation_number", "=", $quotation_no)->first();
        if ($quotation_data) {
            $quotation = new Quotation;
            $quotation->quotation_number = $quotation_no;
            $quotation->cp_cart_id = $control_panel_cart_id;
            $quotation->cart_model_name = "controlpanel";
            $quotation->user_id = auth()->user()->id;
            $quotation->customer_id = $quotation_data->customer_id;
            $quotation->total_quotation_value = $total_price;
            $quotation->status = "open";
            $quotation->save();
        }
    }

    public function AddQuotationWithSCP($quotation_no, $total_price, $scp_cart_id)
    {
        $quotation_data = Quotation::where("quotation_number", "=", $quotation_no)->first();
        if ($quotation_data) {
            $quotation = new Quotation;
            $quotation->quotation_number = $quotation_no;
            $quotation->cp_cart_id = $scp_cart_id;
            $quotation->cart_model_name = "scp";
            $quotation->user_id = auth()->user()->id;
            $quotation->customer_id = $quotation_data->customer_id;
            $quotation->total_quotation_value = $total_price;
            $quotation->status = "open";
            $quotation->save();
        }
    }

    // A Code: 24-02-2026 Start
    public function AddQuotationWithSCPV($quotation_no, $total_price, $scpv_cart_id)
    {
        $quotation_data = Quotation::where("quotation_number", "=", $quotation_no)->first();
        if ($quotation_data) {
            $quotation = new Quotation;
            $quotation->quotation_number = $quotation_no;
            $quotation->cp_cart_id = $scpv_cart_id;
            $quotation->cart_model_name = "scpv";
            $quotation->user_id = auth()->user()->id;
            $quotation->customer_id = $quotation_data->customer_id;
            $quotation->total_quotation_value = $total_price;
            $quotation->status = "open";
            $quotation->save();
        }
    }
    // A Code: 24-02-2026 End

    public function AddQuotationWithAtmos($quotation_no, $total_price, $atmos_cart_id)
    {
        $quotation_data = Quotation::where("quotation_number", "=", $quotation_no)->first();
        if ($quotation_data) {
            $quotation = new Quotation;
            $quotation->quotation_number = $quotation_no;
            $quotation->cp_cart_id = $atmos_cart_id;
            $quotation->cart_model_name = "atmos";
            $quotation->user_id = auth()->user()->id;
            $quotation->customer_id = $quotation_data->customer_id;
            $quotation->total_quotation_value = $total_price;
            $quotation->status = "open";
            $quotation->save();
        }
    }

    public function country_name()
    {
        $country = Helper::country_name();
        return response()->json(['country_name' => $country]);
    }
}
