<?php

namespace App\Http\Controllers\Frontend;

use App\AtmosCart;
use App\ControlPanelCart;
use App\Customer;
use App\Http\Controllers\Controller;
use App\ManualFile;
use App\Models\BoosterCart;
use App\Quotation;
use App\ScpCart;
use App\ScpvCart; // A Code: 20-02-2026
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $maintance_mode_atmos = DB::table("setup_fields")->where('label','atmos_maintance_mode')->pluck('value')[0];
        $maintance_mode_sch = DB::table("setup_fields")->where('label','sch_maintance_mode')->pluck('value')[0];
        $maintance_mode_booster = DB::table("setup_fields")->where('label','maintance_mode_booster')->pluck('value')[0];
        $control_panel_maintance_mode = DB::table("setup_fields")->where('label','control_panel_maintance_mode')->pluck('value')[0];
        $maintance_mode_scp = DB::table("setup_fields")->where('label','scp_maintance_mode')->pluck('value')[0];
        $maintance_mode_scpv = DB::table("setup_fields")->where('label','scpv_maintance_mode')->pluck('value')[0]; // A Code: 20-02-2026
        $maintance_mode_fire_fighting = DB::table("setup_fields")->where('label','fire-fighting_maintance_mode')->pluck('value')[0];   
        
        // A Code: 20-02-2026
        return view('frontend.dashboard.index',compact('maintance_mode_atmos','maintance_mode_sch','maintance_mode_booster','control_panel_maintance_mode','maintance_mode_scp','maintance_mode_scpv','maintance_mode_fire_fighting'));
    }

    public function getDocuments(){
        $query_param = $_GET["article_number"] ?? "";
        $query_component = $_GET["component"] ?? "";
        $ids = [];
        $atmosIds = [];
        $scpIds = [];
        $scpvIds = []; // A Code: 20-02-2026
        $boosterIds = [];

        $user_id = Auth::user()->getAuthIdentifier();
		 $user = DB::table('role_user')->select('role_id')->where('user_id','=',$user_id)->get();
        if($user)
        {   
            if(isset($user[0]->role_id))
            {
                if($user[0]->role_id == "1")
                {
                    $role = "admin";
                }
                elseif(isset($user[1]->role_id))
                {   
                    if($user[1]->role_id == "1")
                    {
                        $role = "admin";
                    }
                    else{
                        $role = "user";
                    }
                }
                else{
                    $role = "user";
                }
            }
            else{
                $role = "user";
            }
        }
        else
        {
            $role = "user";
        }
        $customer = Customer::find($user_id);
       
        if($query_param == ""){
            $controlPanelCartData = ControlPanelCart::where('user_id', $user_id)->whereNotNull('article_number')->with('documents')
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
			->groupBy('article_number');
            //->get();

            $atmosCartData = AtmosCart::whereNotNull('article_number')->with('documents');
            $scpCartData = ScpCart::whereNotNull('article_number')->with('documents');
            $scpvCartData = ScpvCart::whereNotNull('article_number')->with('documents'); // A Code: 20-02-2026
            $boosterCartData = BoosterCart::whereNotNull('article_number')->with('documents');
            //for admin
            if($role == "admin")
            {
                $controlPanelCartData = $controlPanelCartData->groupBy('article_number')->get();
                $atmosCartData = $atmosCartData->groupBy('article_number')->get();
                $scpCartData = $scpCartData->groupBy('article_number')->get();
                $scpvCartData = $scpvCartData->groupBy('article_number')->get(); // A Code: 20-02-2026
                $boosterCartData = $boosterCartData->groupBy('article_number')->get();
            }
            //for user
            else
            {
                $controlPanelCartData = $controlPanelCartData->where("user_id",'=',$user_id)->groupBy('article_number')->get();
                $atmosCartData = AtmosCart::where('user_id', $user_id)->whereNotNull('article_number')->groupBy('article_number')->with('documents')->get();
                $scpCartData = ScpCart::where('user_id', $user_id)->whereNotNull('article_number')->groupBy('article_number')->with('documents')->get();
                $scpvCartData = ScpvCart::where('user_id', $user_id)->whereNotNull('article_number')->groupBy('article_number')->with('documents')->get(); // A Code: 20-02-2026
                $boosterCartData = BoosterCart::where('user_id', $user_id)->whereNotNull('article_number')->groupBy('article_number')->with('documents')->get();
            }
        }

        if($query_param != ""){
            if($query_component == "control_panel"){
            $controlPanelCartData = ControlPanelCart::where('user_id', $user_id)->where('article_number',$query_param)->with('documents')
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
            }

            if($query_component == "atmos"){
                $atmosCartData = AtmosCart::cartDataByUserId($user_id,$query_param);
            }

            if($query_component == "scp"){
                $scpCartData = ScpCart::cartDataByUserId($user_id,$query_param);
            }
            // A Code: 20-02-2026 Start
            if($query_component == "scpv"){
                $scpvCartData = ScpvCart::cartDataByUserId($user_id,$query_param);
            }
            // A Code: 20-02-2026 End
            
            if($query_component == "booster"){
                $boosterCartData = BoosterCart::cartDataByUserId($user_id,$query_param);
            }
            
        }
        
        return view('frontend.dashboard.documents', compact('customer','query_param'))
                    ->with('controlPanelCartData',isset($controlPanelCartData)?$controlPanelCartData:null)
                    ->with('atmosCartData',isset($atmosCartData)?$atmosCartData:null)
                    ->with('scpCartData',isset($scpCartData)?$scpCartData:null)
                    ->with('scpvCartData',isset($scpvCartData)?$scpvCartData:null) // A Code: 20-02-2026
                    ->with('boosterCartData',isset($boosterCartData)?$boosterCartData:null);
        
    }

    public function getManuals(){
        $user_id = Auth::user()->getAuthIdentifier();
        $customer = Customer::find($user_id);

        $booster['module_name'] = 'Booster Set';
        $booster['data'] = ManualFile::where('module_name','booster_set')->get();

        $cp['module_name'] = 'Control Panel';
        $cp['data'] = ManualFile::where('module_name','control_panel')->get();

        $scp['module_name'] = 'SCP Pump Assembly';
        $scp['data'] = ManualFile::where('module_name','scp_pump_assembly')->get();

        // A Code: 20-02-2026 Start
        $scpv['module_name'] = 'SCPV Pump Assembly';
        $scpv['data'] = ManualFile::where('module_name','scpv_pump_assembly')->get();
        // A Code: 20-02-2026 End

        $atmos['module_name'] = 'Atmos GIGA';
        $atmos['data'] = ManualFile::where('module_name','atmos_giga')->get();

        return view('frontend.dashboard.manual', compact('booster','cp','atmos','scp','scpv','customer')); // A Code: 20-02-2026
    }
}
