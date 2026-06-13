<?php

namespace App\Http\Controllers\api\Project_order_from_WIPTracker;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use App\AtmosCart;
use App\Models\BoosterCart;
use App\Cart;
use App\ControlPanel;
use App\ControlPanelCart;
use App\Customer;
use App\Models\FireFighting\FireFightingCarts;
use App\Quotation;
use App\ScpCart;
use App\Http\Controllers\Frontend\AtmosGigaController;
use App\Http\Controllers\Frontend\ScpController;
use App\Http\Controllers\Frontend\BoosterSetController;
use App\Http\Controllers\Frontend\ControlpanelController;
use App\Http\Controllers\Frontend\CPCartController;
use App\Http\Controllers\Frontend\FireFighting\FireFightingPumpController;

class ApiGetWIPTrackerDataController extends Controller
{
    public function complete_order_from_wiptracker(Request $request){
        $witrack_no = $request->witrack_no;
        if($witrack_no){
            return response()->json(['status' => '1', 'message' => 'WITrack NO. get successfully..!!', 'data' => $witrack_no], 200); 
        }
    }
}
