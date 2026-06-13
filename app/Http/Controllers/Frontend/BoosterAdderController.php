<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use App\Voltage;
use App\AmbientTemp;
use App\Application;
use App\Component;
use App\NumberOfPump;
use App\IpRating;
use App\StarterType;
use App\Enclousre;
use App\ComunicationProtocol;
use App\Power;
use App\Range;
use App\ControlPanel;
use DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use App\Tax;
use App\Traits\ControlPanelModelIdGet;
use App\Helpers\AdderHelper;

class BoosterAdderController extends Controller {


    public function ajaxOptionalModal(Request $request) {

        $electricalLists = DB::table('main_electrical_list')->get();
//        dd($electricalLists);
        $electricalListsData = [];
        $rangeAndCode = $this->getControlPanelRangeAndCode($request);
//        dd($rangeAndCode);
        foreach ($electricalLists as $electricalList) {
            if (($electricalList->code >= 1 && $electricalList->code <= 13 && $rangeAndCode['starter_code'] != 'Xtreme' ) || ($electricalList->code >= 16 && $electricalList->code <= 18 && $rangeAndCode['starter_code'] != 'Xtreme') || ($electricalList->code >= 37 && $electricalList->code <= 40 && $rangeAndCode['starter_code'] != 'Xtreme') || (($electricalList->code == 46 || $electricalList->code == 48 || $electricalList->code == 50) && $rangeAndCode['starter_code'] != 'Xtreme' && $rangeAndCode['voltage_id'] != 1 ) || ($electricalList->code == 51 && $rangeAndCode['starter_code'] != 'Xtreme' )) {
                $electricalListsData[] = $electricalList;
            }
            if (($electricalList->code == 45 || $electricalList->code == 47 || $electricalList->code == 49) && $rangeAndCode['voltage_id'] == 1 && $rangeAndCode['stater_type_id'] == 2) { //voltage_id = 230 V, Only starter constant speed Dol
                $electricalListsData[] = $electricalList;
            }

            if (($electricalList->code >= 14 && $electricalList->code <= 15 && $rangeAndCode['range'] == 3) || ($electricalList->code == 29 && $rangeAndCode['range'] == 3) || ($electricalList->code >= 41 && $electricalList->code <= 43 && $rangeAndCode['range'] == 3) || ($electricalList->code == 52 && $rangeAndCode['range'] == 3)) { // 3 = Premium
                $electricalListsData[] = $electricalList;
            }

//            Code no – 19,20 – Basic version - 01 Basic version\04 Single pump configuration Multi VFD and Multi VFD+bypass

            if ($electricalList->code >= 19 && $electricalList->code <= 20 && ($rangeAndCode['starter_code'] == 'VFD' || $rangeAndCode['starter_code'] == 'VFD+Bypass')) {
                $electricalListsData[] = $electricalList;
            }

            if ($electricalList->code >= 21 && $electricalList->code <= 24 && $rangeAndCode['starter_code'] == 'Xtreme') {
                $electricalListsData[] = $electricalList;
            }

            //if (($electricalList->code >= 27 && $electricalList->code <= 28 && $rangeAndCode['starter_code'] != 'Xtreme') || ($electricalList->code >= 30 && $electricalList->code <= 36 && $rangeAndCode['starter_code'] != 'Xtreme')) {
                if (($electricalList->code >= 27 && $electricalList->code <= 28 && $rangeAndCode['starter_code'] != 'Xtreme') || 
                ($electricalList->code ==30 && $rangeAndCode['starter_code'] != 'Xtreme'))
            {
				$electricalListsData[] = $electricalList;
            }
        }

    //  dd($electricalListsData);
        $data = view('frontend.booster.electrical_optional')->with('electricalListsData', $electricalListsData)
                ->render();


        return response()->json(array('success' => true, 'data' => $data));
    }

    public function getControlPanelRangeAndCode($request) {
        $returnRangeAndCode = [];
        $controlPanelData = ControlPanel::where('id', $request->cp_id)->get();
//       dd($controlPanelData);

        return $returnRangeAndCode = array(
            'id' => $controlPanelData[0]->id,
            'range' => $controlPanelData[0]->range,
            'starter_code' => $controlPanelData[0]->starter_code,
            'voltage_id' => $controlPanelData[0]->voltage_id,
            'stater_type_id' => $controlPanelData[0]->stater_type_id
        );
    }



}
