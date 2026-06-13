<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MassDestroyProductRequest;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Product;
use App\Range;
use Illuminate\Http\Request;
use File;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use App\Helpers\DynamicTableCreateHelper;
use DB;
use Session;
use App\MainMechanicalAdderList;

class MechanicalAdderOptionalImportController extends Controller {

    public function import(Request $request) {


        return view('admin.booster.mechanical_adder_import');
    }

    public function upload(Request $request) {
        $file = $request->file_import;


        $file = $request->file_import;
        
        if (!empty($file)) {
            MainMechanicalAdderList::truncate();
            $path = '/app/public/';

            if (!File::exists($path)) {
                $this->make_directory(storage_path() . '/' . $path);
            }

            $filePath = storage_path() . $path . "/";
            $file_excel = $this->uploadFile($file, $filePath);

            $data = new \SpreadsheetReader($filePath . $file_excel);

            $materialCode = [];
            foreach ($data as $key => $d) {
                unset($d[0]); // Remove S.No Rows
                
                if ($key > 0) {
                    $mainMechanicalAdderList = new MainMechanicalAdderList();
                    $mainMechanicalAdderList->adder_list = $d[1];
                     $mainMechanicalAdderList->code = $d[2];
                    $mainMechanicalAdderList->version = $d[3];
                    $mainMechanicalAdderList->save();
                }
            }
        }

        Session::flash('message', "Success! Your file has been imported ");
        return redirect()->back();
    }

    public function make_directory($path) {
        File::isDirectory($path) or File::makeDirectory($path, 0777, true, true);
    }

    public function uploadFile($file, $path, $type = '') {
        $is_uploaded = false;

        if (!empty($file)) {

            $fileName = $file->getClientOriginalName();
            //$extension = \File::extension($file);
//             $fileName = rand(11111111, 99999999) . '.' . $extension;

            if ($file->move($path, $fileName)) {
                $is_uploaded = $fileName;
            }
        }
        return $is_uploaded;
    }

}
