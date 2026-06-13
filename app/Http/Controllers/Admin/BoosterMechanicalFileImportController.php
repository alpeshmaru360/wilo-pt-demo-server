<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroyProductRequest;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Imports\BaseFrameCalculationImport;
use App\Imports\BoosterBareshaftMotorPriceImport;
use App\Imports\BoosterBareshaftPumpPriceImport;
use App\Imports\BoosterCableSelectionImport;
use App\Imports\BoosterFullPumpPriceImport;
use App\Imports\BoosterMasterSheetMechanicalComponentImport;
use App\Imports\PN16MechanicalComponentImport;
use App\Imports\PTPDistanceMechanicalComponentImport;
use App\Models\BaseFrameCalculation;
use App\Models\BoosterCableSelection;
use App\Models\BoosterMasterSheetMechanicalComponent;
use App\Models\PTPDistanceMechanicalComponent;
use App\Product;
use App\Range;
use Illuminate\Http\Request;
use File;
use Illuminate\Support\Facades\Storage;
use Excel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use App\Helpers\DynamicTableCreateHelper;
use DB;
use PhpParser\Node\Expr\Include_;
use Session;

class BoosterMechanicalFileImportController extends Controller {



    public function importMasterPriceSheet(Request $request) {
        return view('admin.booster.mechanical_component.master_price_sheet');
    }

    public function uploadMasterPriceSheet(Request $request) {
        // require_once("../app/spreadsheetReader/php-excel-reader/excel_reader2.php");
        require_once(base_path('app/spreadsheetReader/php-excel-reader/excel_reader2.php'));

        $validated = $request->validate([
            'file_import' => 'required',
        ]);
        $path = $request->file('file_import');
        if(!empty($path)){
            BoosterMasterSheetMechanicalComponent::truncate();
            Excel::import(new BoosterMasterSheetMechanicalComponentImport(), $path);
        }

        Session::flash('message', "Success! Your file has been imported ");

        return redirect('admin/booster/mechanical-component/master-sheet-price-import')->with('message',"Success! Your file has been imported ");
    }

    public function importBOMPN16(Request $request) {
        return view('admin.booster.mechanical_component.bom_pn16');
    }

    public function uploadBOMPN16(Request $request) {
        require_once("../app/spreadsheetReader/php-excel-reader/excel_reader2.php");

        $validated = $request->validate([
            'file_import' => 'required',
        ]);
        $path = $request->file('file_import');

        return redirect('admin/booster/mechanical-component/bom-pn16-import');
    }

    public function importPtpDistance(Request $request) {
        return view('admin.booster.mechanical_component.ptp_distance');
    }

    public function uploadPtpDistance(Request $request) {
        require_once("../app/spreadsheetReader/php-excel-reader/excel_reader2.php");

        $validated = $request->validate([
            'file_import' => 'required',
        ]);
        $path = $request->file('file_import');
        if(!empty($path)){
            PTPDistanceMechanicalComponent::truncate();
            Excel::import(new PTPDistanceMechanicalComponentImport(), $path);

        }

        Session::flash('message', "Success! Your file has been imported ");

        return redirect('admin/booster/mechanical-component/ptp-distance-import');
    }

    //base frame
    public function importBaseFrameCalculation(Request $request) {
        return view('admin.booster.mechanical_component.base_frame_calculation');
    }

    public function uploadBaseFrameCalculation(Request $request) {
        require_once("../app/spreadsheetReader/php-excel-reader/excel_reader2.php");

        $validated = $request->validate([
            'file_import' => 'required',
        ]);
        $path = $request->file('file_import');
        if(!empty($path)){
            BaseFrameCalculation::truncate();
            Excel::import(new BaseFrameCalculationImport(), $path);
        }

        Session::flash('message', "Success! Your file has been imported ");

        return redirect('admin/booster/mechanical-component/base-frame-calculation-import');
    }

    //cable sleection
    public function importCableSelection(Request $request) {
        return view('admin.booster.mechanical_component.cable_selection');
    }

    public function uploadCableSelection(Request $request) {
        require_once("../app/spreadsheetReader/php-excel-reader/excel_reader2.php");

        $validated = $request->validate([
            'file_import' => 'required',
        ]);
        $path = $request->file('file_import');
        if(!empty($path)){
            BoosterCableSelection::truncate();
            Excel::import(new BoosterCableSelectionImport(), $path);
        }

        Session::flash('message', "Success! Your file has been imported ");

        return redirect('admin/booster/mechanical-component/cable-selection-import');
    }


}
