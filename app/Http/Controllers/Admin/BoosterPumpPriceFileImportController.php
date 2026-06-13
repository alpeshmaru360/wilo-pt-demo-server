<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroyProductRequest;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Imports\BoosterBareshaftMotorPriceImport;
use App\Imports\BoosterBareshaftPumpPriceImport;
use App\Imports\BoosterFullPumpPriceImport;
use App\Models\BoosterBareshaftMotorPrice;
use App\Models\BoosterBareshaftPumpPrice;
use App\Models\BoosterFullPumpPrice;
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

class BoosterPumpPriceFileImportController extends Controller {



    public function importFullPumpPrice(Request $request) {


        return view('admin.booster.pump_price.full_pump_import');
    }

    public function uploadFullPumpPrice(Request $request) {
        require_once("../app/spreadsheetReader/php-excel-reader/excel_reader2.php");

        $validated = $request->validate([
            'file_import' => 'required',
        ]);
        $path = $request->file('file_import');
        if(!empty($path)){
            BoosterFullPumpPrice::truncate();
            Excel::import(new BoosterFullPumpPriceImport(), $path);
        }

        Session::flash('message', "Success! Your file has been imported ");
        return view('admin.booster.pump_price.full_pump_import')->with('message',"Success! Your file has been imported ");

    }

    public function importBareshaftPumpMotorPrice(Request $request) {


        return view('admin.booster.pump_price.bareshaft_motor_price_import');
    }

    public function uploadBareshaftPumpMotorPrice(Request $request) {
        // require_once("app/spreadsheetReader/php-excel-reader/excel_reader2.php");
        $validated = $request->validate([
            'file_import' => 'required',
            'file_type' => 'required',
        ]);
        $path = $request->file('file_import');
        $fileType =  $request->input('file_type');
        if(!empty($path))
            if ($fileType == 'pump'){
                BoosterBareshaftPumpPrice::truncate();
                Excel::import(new BoosterBareshaftPumpPriceImport(), $path);
            }
            else{
                BoosterBareshaftMotorPrice::truncate();
                Excel::import(new BoosterBareshaftMotorPriceImport(), $path);
            }

        Session::flash('message', "Success! Your file has been imported ");

        return view('admin.booster.pump_price.bareshaft_motor_price_import')->with('message',"Success! Your file has been imported ");
    }

}
