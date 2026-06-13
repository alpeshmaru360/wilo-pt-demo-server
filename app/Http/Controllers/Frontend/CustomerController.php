<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use App\Http\Requests\GetDataRequest;
use App\Cart;
use App\ControlPanel;
use App\Traits\ControlPanelModelIdGet;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use App\Models\FireFighting\FireFightingCarts;
use DB;
use App\Customer;
use App\ControlPanelCart;
use App\Helpers\Helper;
use App\Quotation;
use App\Item;
use App\AtmosCart;
use App\ScpCart;
use App\ScpvCart; // A Code: 24-02-2026
use App\Models\BoosterCart;
use App\PrefixArticleNumber;

class CustomerController extends Controller{

    public function index(Request $request){
        $countries = DB::table('countries')->select('*')->get();
        return view('frontend.customer.index',compact('countries'));
    }

    public function save(Request $request){
        $customer = new Customer;

        if ($request->cp_ids) {
            $ids = explode(",", $request->cp_ids);
        } else {
            $ids = null;
        }
        if ($request->atmos_ids) {
            $atmosIds = explode(",", $request->atmos_ids);
        } else {
            $atmosIds = null;
        }

        if ($request->scp_ids) {
            $scpIds = explode(",", $request->scp_ids);
        } else {
            $scpIds = null;
        }
        // A Code: 24-02-2026 Start
        if ($request->scpv_ids) {
            $scpvIds = explode(",", $request->scpv_ids);
        } else {
            $scpvIds = null;
        }
        // A Code: 24-02-2026 End
        
        if ($request->booster_ids) {
            $boosterIds = explode(",", $request->booster_ids);
        } else {
            $boosterIds = null;
        }
		
         if ($request->firefighting_ids) {
            $firefightingIds = explode(",", $request->firefighting_ids);
        } else {
            $firefightingIds = null;
        }
        // A Code: 24-02-2026
        if ($ids || $atmosIds || $scpIds || $scpvIds || $boosterIds || $firefightingIds) {
            $customer->cp_cart_id = 0; //Dont need id's because id save in quotation
            $customer->name = $request->name;
            $customer->project_name = $request->project_name;
            $customer->country = $request->country;
            $customer->revision_number = $request->revision_number;
            $customer->segment_category = $request->segment_category;
            $customer->project_location = $request->project_location;
            $customer->email_id = $request->email;
            $customer->phone_no = $request->phone_no;
            $customer->address = $request->address;
            $customer->enquiry_form_number = isset($request->enquiry_form_number) ? $request->enquiry_form_number : '';
            $customer->consultant = $request->consultant;
            $customer->contractor = $request->contractor;
            $customer->notes = $request->notes;
            $customer->save();
        }

        if ($ids) {
            foreach ($ids as $id) {
                $controlPanelCart = ControlPanelCart::where('id', $id)->first();
                $prefix = PrefixArticleNumber::select('*')->orderBy('id','desc')->first();
                $prefix_plus_one = $prefix->auto_increment + 1;
                $prefix_company_code_with_AI =$prefix->prefix_company_code.''.sprintf('%04d',$prefix_plus_one);
              if (!$controlPanelCart->article_number && empty($controlPanelCart->article_number)) {
                    $controlPanelCart->article_number = rand(10000000,99999999);
                    $controlPanelCart->full_article_number =  $prefix_company_code_with_AI;
                    $add_prefix = new PrefixArticleNumber;
                    $add_prefix->prefix_company_code = '683';
                    $add_prefix->article_number = $controlPanelCart->article_number;
                    $add_prefix->auto_increment = sprintf('%04d',$prefix_plus_one);
                    $add_prefix->full_article_number = $prefix_company_code_with_AI;
                    $add_prefix->save();
                    $controlPanelCart->save();
                }
                else
                {
                    $controlPanelCart->full_article_number =  $controlPanelCart->full_article_number;
                    $AI = PrefixArticleNumber::where('article_number','=',$controlPanelCart->article_number)->first();
                        $controlPanelCart->save();
                }
            }
        }

        if ($atmosIds) {
            foreach ($atmosIds as $id) {
                $atmosCart = AtmosCart::where('id', $id)->first();
                $prefix = PrefixArticleNumber::select('*')->orderBy('id','desc')->first();
                $prefix_plus_one = $prefix->auto_increment + 1;
                $prefix_company_code_with_AI =$prefix->prefix_company_code.''.sprintf('%04d',$prefix_plus_one);

                if (!$atmosCart->article_number && empty($atmosCart->article_number)) {
                    $atmosCart->article_number = rand(10000000,99999999);
                    if($atmosCart->is_bareshaft_selection != "1"){
                        $atmosCart->full_article_number =  $prefix_company_code_with_AI;
                        $add_prefix = new PrefixArticleNumber;
                        $add_prefix->prefix_company_code = '683';
                        $add_prefix->article_number = $atmosCart->article_number;
                        $add_prefix->auto_increment =sprintf('%04d',$prefix_plus_one);
                        $add_prefix->full_article_number = $prefix_company_code_with_AI;
    					//add 339 article number if user is from KSA and country origin is KSA
                        $new_ksa_article_number = '';
                        if(auth()->user()->country_id == 6 && $atmosCart->country_origin == "ksa"){
                            $new_ksa_article_number = str_replace("683", "339", $atmosCart->full_article_number);
                            $atmosCart->ksa_full_article_number = $new_ksa_article_number;
                        }
                        $add_prefix->save();
                    }
                        $atmosCart->save();
                    $a_id = AtmosCart::where('id',$atmosCart->id)->first();
                    $p_id = new PrefixArticleNumber;
                    $p_id->prefix_company_code = '683';
                    $p_id->article_number = $atmosCart->article_number;

                    if($atmosCart->bare_shaft_article_number == null){
                        if($atmosCart->is_bareshaft_selection != "1"){
                        // full pump selection
                            $a_id->bare_shaft_article_number = $prefix_company_code_with_AI+1;
                            $p_id->auto_increment = $add_prefix->auto_increment + 1;
                            $p_id->full_article_number = $prefix_company_code_with_AI + 1;
                        }
                        else{
                            $a_id->bare_shaft_article_number = $prefix_company_code_with_AI;
                            $p_id->auto_increment = $add_prefix->auto_increment;
                            $p_id->full_article_number = $prefix_company_code_with_AI;
                        }
                        $p_id->save();
                        $a_id->save();
                    }
                    else{
                    }
                    
                    if($atmosCart->bare_shaft_article_number == null){
                    }
                    else{
                    }
                }
                else
                {
                    $atmosCart->full_article_number =  $atmosCart->full_article_number;
                    $atmosCart->bare_shaft_article_number =  $atmosCart->bare_shaft_article_number;
                    $AI = PrefixArticleNumber::where('article_number','=',$atmosCart->article_number)->first();
                    $atmosCart->save();
                }
            }
        }

        //Scp Cart
        if ($scpIds) {
            foreach ($scpIds as $id) {
                $scpCart = ScpCart::where('id', $id)->first();
                $prefix = PrefixArticleNumber::select('*')->orderBy('id','desc')->first();
                $prefix_plus_one = $prefix->auto_increment + 1;
                $prefix_company_code_with_AI =$prefix->prefix_company_code.''.sprintf('%04d',$prefix_plus_one);
                if (!$scpCart->article_number && empty($scpCart->article_number)){
                    $scpCart->article_number = rand(10000000,99999999);
                    $scpCart->full_article_number =  $prefix_company_code_with_AI;
                    $add_prefix = new PrefixArticleNumber;
                    $add_prefix->prefix_company_code = '683';
                    $add_prefix->article_number = $scpCart->article_number;
                    $add_prefix->auto_increment = sprintf('%04d',$prefix_plus_one);
                    $add_prefix->full_article_number = $prefix_company_code_with_AI;
					//add 339 article number if user is from KSA and country origin is KSA
                    $new_ksa_article_number = '';
                    if(auth()->user()->country_id == 6 && $scpCart->country_origin == "ksa"){
                        $new_ksa_article_number = str_replace("683", "339", $scpCart->full_article_number);
                        $scpCart->ksa_full_article_number = $new_ksa_article_number;
                    }
                    $add_prefix->save();
                    $scpCart->save();
                }
                else
                {
                    $scpCart->full_article_number =  $scpCart->full_article_number;
                    $AI = PrefixArticleNumber::where('article_number','=',$scpCart->article_number)->first();
                    $scpCart->save();
                }
            }
        }

        // A Code: 24-02-2026 Start
        //Scpv Cart
        if ($scpvIds) {
            foreach ($scpvIds as $id) {
                $scpvCart = ScpvCart::where('id', $id)->first();
                $prefix = PrefixArticleNumber::select('*')->orderBy('id','desc')->first();
                $prefix_plus_one = $prefix->auto_increment + 1;
                $prefix_company_code_with_AI =$prefix->prefix_company_code.''.sprintf('%04d',$prefix_plus_one);
                if (!$scpvCart->article_number && empty($scpvCart->article_number)){
                    $scpvCart->article_number = rand(10000000,99999999);
                    $scpvCart->full_article_number =  $prefix_company_code_with_AI;
                    $add_prefix = new PrefixArticleNumber;
                    $add_prefix->prefix_company_code = '683';
                    $add_prefix->article_number = $scpvCart->article_number;
                    $add_prefix->auto_increment = sprintf('%04d',$prefix_plus_one);
                    $add_prefix->full_article_number = $prefix_company_code_with_AI;
					//add 339 article number if user is from KSA and country origin is KSA
                    $new_ksa_article_number = '';
                    if(auth()->user()->country_id == 6 && $scpvCart->country_origin == "ksa"){
                        $new_ksa_article_number = str_replace("683", "339", $scpvCart->full_article_number);
                        $scpvCart->ksa_full_article_number = $new_ksa_article_number;
                    }
                    $add_prefix->save();
                    $scpvCart->save();
                }
                else
                {
                    $scpvCart->full_article_number =  $scpvCart->full_article_number;
                    $AI = PrefixArticleNumber::where('article_number','=',$scpvCart->article_number)->first();
                    $scpvCart->save();
                }
            }
        }    
        // A Code: 24-02-2026 End
        
        if($boosterIds){
            foreach($boosterIds as $id){
                $boosterCart = BoosterCart::where('id', $id)->first();
                $prefix = PrefixArticleNumber::select('*')->orderBy('id','desc')->first();
                $prefix_plus_one = $prefix->auto_increment + 1;
                 $prefix_company_code_with_AI =$prefix->prefix_company_code.''.sprintf('%04d',$prefix_plus_one);
                if(!$boosterCart->article_number && empty($boosterCart->article_number)){
                    $boosterCart->article_number = rand(10000000,99999999);
                    $boosterCart->full_article_number =  $prefix_company_code_with_AI;
                    $add_prefix = new PrefixArticleNumber;
                    $add_prefix->prefix_company_code = '683';
                    $add_prefix->article_number = $boosterCart->article_number;
                    $add_prefix->auto_increment = sprintf('%04d',$prefix_plus_one);
                    $add_prefix->full_article_number = $prefix_company_code_with_AI;
					//add 339 article number if user is from KSA and country origin is KSA
                    $new_ksa_article_number = '';
                    if(auth()->user()->country_id == 6 && $boosterCart->country_origin == "ksa"){
                        $new_ksa_article_number = str_replace("683", "339", $boosterCart->full_article_number);

                        $boosterCart->ksa_full_article_number = $new_ksa_article_number;
                        // dd($new_ksa_article_number,$boosterCart->full_article_number,$boosterCart->ksa_full_article_number);
                    }
                    $add_prefix->save();
                    $boosterCart->save();
                    
                    $b_id = BoosterCart::where('id',$boosterCart->id)->first();
                    $b_id->mechanical_article_number = $prefix_company_code_with_AI+1;
                    $b_id->save();
                    
                    $p_id = PrefixArticleNumber::where('id',$add_prefix->id)->first();
                    $p_id = new PrefixArticleNumber;
                    $p_id->prefix_company_code = '683';
                    $p_id->article_number = $boosterCart->article_number;
                    $p_id->auto_increment = $add_prefix->auto_increment + 1;
                    $p_id->full_article_number = $prefix_company_code_with_AI + 1;
                    $p_id->save();

                    $c_id = ControlPanelCart::where('control_panel_id',$b_id->cp_id)->first();
                    if($c_id)
                    {
                        $b_id = BoosterCart::where('id',$boosterCart->id)->first();
                        $b_id->electrical_article_number = $c_id->full_article_number;
                        $b_id->save();
                    }
                    else{
                        $p_c_id = new PrefixArticleNumber;
                        $p_c_id->prefix_company_code = '683';
                        $p_c_id->article_number = $boosterCart->article_number;
                        $p_c_id->auto_increment = $add_prefix->auto_increment + 2;
                        $p_c_id->full_article_number = $prefix_company_code_with_AI + 2;
                        $p_c_id->save();

                        $b_c_id = BoosterCart::where('id',$boosterCart->id)->first();
                        $b_c_id->electrical_article_number = $prefix_company_code_with_AI+2;
                        $b_c_id->save();
                    }
                }
               else
                {
                    $boosterCart->full_article_number =  $boosterCart->full_article_number;
                    $boosterCart->mechanical_article_number =  $boosterCart->mechanical_article_number;
                    $boosterCart->electrical_article_number =  $boosterCart->electrical_article_number;
                    $AI = PrefixArticleNumber::where('article_number','=',$boosterCart->article_number)->first();
                    $boosterCart->save();
                }
            }
        }
		// Fire Fighting Cart
        if ($firefightingIds) {
            foreach ($firefightingIds as $id) {
                $firefightingCart = FireFightingCarts::where('id', $id)->first();
                $prefix = PrefixArticleNumber::select('*')->orderBy('id','desc')->first();
                $prefix_plus_one = $prefix->auto_increment + 1;

                $prefix_company_code_with_AI =$prefix->prefix_company_code.''.sprintf('%04d',$prefix_plus_one);
                if (!$firefightingCart->article_number && empty($firefightingCart->article_number)){
                    $firefightingCart->article_number = rand(10000000,99999999);
                    $firefightingCart->full_article_number =  $prefix_company_code_with_AI;
                    $add_prefix = new PrefixArticleNumber;
                    $add_prefix->prefix_company_code = '683';
                    $add_prefix->article_number = $firefightingCart->article_number;
                    $add_prefix->auto_increment = sprintf('%04d',$prefix_plus_one);
                    $add_prefix->full_article_number = $prefix_company_code_with_AI;
                    $add_prefix->save();
                    $firefightingCart->save();
                }
                else
                {
                    $firefightingCart->full_article_number =  $firefightingCart->full_article_number;
                    $AI = PrefixArticleNumber::where('article_number','=',$firefightingCart->article_number)->first();
                    $firefightingCart->save();
                }
            }
        }
        $quotationNumber = random_int(100000, 999999);
        //Cotnrol Panel Quotation
        if ($ids) {
            foreach ($ids as $id) {
                $controlPanelCart = ControlPanelCart::where('id', $id)->first();
                $quotation = new Quotation;
                $quotation->quotation_number = $quotationNumber;
                $quotation->cp_cart_id = $id;
                $quotation->cart_model_name = 'controlpanel';
                $quotation->user_id = auth()->user()->id;
                $quotation->total_quotation_value =  $controlPanelCart->total_price;
                $quotation->customer_id = $customer->id;
                $quotation->status = 'Open';
                $quotation->reason = '';
                $quotation->modification = '';
                $quotation->save();
            }

            foreach ($ids as $id) {
                $controlPanelCart = ControlPanelCart::where('id', $id)->first();
                if (!$controlPanelCart->quotation_no && empty($controlPanelCart->quotation_number)) {
                    $controlPanelCart->quotation_no = $quotationNumber;
                }
                $controlPanelCart->save();
            }
        }
        //Atmos Quotation
        if ($atmosIds) {
            foreach ($atmosIds as $id) {
                $atmosCart = AtmosCart::where('id', $id)->first();
                $quotation = new Quotation;
                $quotation->quotation_number = $quotationNumber;
                $quotation->cp_cart_id = $id;
                $quotation->cart_model_name = 'atmos';
                $quotation->user_id = auth()->user()->id;
                $quotation->total_quotation_value =  $atmosCart->total_price;
                $quotation->customer_id = $customer->id;
                $quotation->status = 'Open';
                $quotation->reason = '';
                $quotation->modification = '';
                $quotation->save();
            }
            foreach ($atmosIds as $id) {
                $atmosCart = AtmosCart::where('id', $id)->first();

                if (!$atmosCart->quotation_no && empty($atmosCart->quotation_number)) {
                    $atmosCart->quotation_no = $quotationNumber;
                }
                $atmosCart->save();
            }
        }

        //Scp Quotation
        if ($scpIds) {
            foreach ($scpIds as $id) {
                $scpCart = ScpCart::where('id', $id)->first();
                $quotation = new Quotation;
                $quotation->quotation_number = $quotationNumber;
                $quotation->cp_cart_id = $id;
                $quotation->cart_model_name = 'scp';
                $quotation->user_id = auth()->user()->id;
                $quotation->customer_id = $customer->id;
                $quotation->total_quotation_value =  $scpCart->total_price;
                $quotation->status = 'Open';
                $quotation->reason = '';
                $quotation->modification = '';
                $quotation->save();
            }

            foreach ($scpIds as $id) {
                $scpCart = ScpCart::where('id', $id)->first();

                if (!$scpCart->quotation_no && empty($scpCart->quotation_number)) {
                    $scpCart->quotation_no = $quotationNumber;
                }

                $scpCart->save();
            }
        }

        // A Code: 24-02-2026 Start
        //Scpv Quotation
        if ($scpvIds) {
            foreach ($scpvIds as $id) {
                $scpvCart = ScpvCart::where('id', $id)->first();
                $quotation = new Quotation;
                $quotation->quotation_number = $quotationNumber;
                $quotation->cp_cart_id = $id;
                $quotation->cart_model_name = 'scpv';
                $quotation->user_id = auth()->user()->id;
                $quotation->customer_id = $customer->id;
                $quotation->total_quotation_value =  $scpvCart->total_price;
                $quotation->status = 'Open';
                $quotation->reason = '';
                $quotation->modification = '';
                $quotation->save();
            }

            foreach ($scpvIds as $id) {
                $scpvCart = ScpvCart::where('id', $id)->first();

                if (!$scpvCart->quotation_no && empty($scpvCart->quotation_number)) {
                    $scpvCart->quotation_no = $quotationNumber;
                }

                $scpvCart->save();
            }
        }
        // A Code: 24-02-2026 End
       
        //Booster Quotation
        if($boosterIds){
            foreach($boosterIds as $id){
                $boosterCart = BoosterCart::where('id', $id)->first();
                $quotation = new Quotation;
                $quotation->quotation_number = $quotationNumber;
                $quotation->cp_cart_id = $id;
                $quotation->cart_model_name = 'booster';
                $quotation->user_id = auth()->user()->id;
                $quotation->total_quotation_value =  $boosterCart->total_price;
                $quotation->customer_id = $customer->id;
                $quotation->status = 'Open';
                $quotation->reason = '';
                $quotation->modification = '';
                $quotation->save();
            }

            foreach($boosterIds as $id){
                $boosterCart = BoosterCart::where('id', $id)->first();
                if(!$boosterCart->quotation_no && empty($boosterCart->quotation_number)){
                    $boosterCart->quotation_no = $quotationNumber;
                }
                $boosterCart->save();
            }
        }
		// Fire Fighting Quotation
        if($firefightingIds){
            foreach($firefightingIds as $id){
                $fireFightingCarts = FireFightingCarts::where('id', $id)->first();
                $quotation = new Quotation;
                $quotation->quotation_number = $quotationNumber;
                $quotation->cp_cart_id = $id;
                $quotation->cart_model_name = 'firefighting';
                $quotation->user_id = auth()->user()->id;
                $quotation->total_quotation_value =  $fireFightingCarts->total_price;
                $quotation->customer_id = $customer->id;
                $quotation->status = 'Open';
                $quotation->reason = '';
                $quotation->modification = '';
                $quotation->save();
            }

            foreach($firefightingIds as $id){
                $fireFightingCarts = FireFightingCarts::where('id', $id)->first();

                if (!$fireFightingCarts->quotation_no && empty($fireFightingCarts->quotation_number)) {
                    $fireFightingCarts->quotation_no = $quotationNumber;
                }

                $fireFightingCarts->save();
            }
        }
        return redirect()->route('controlpanel.quotation', ['quotation_no' => $quotationNumber]);
    }
}
