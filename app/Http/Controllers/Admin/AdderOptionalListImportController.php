<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Schema; // A Code: 05-03-2026
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use App\Helpers\DynamicTableCreateHelper;
use DB;
use Session;

class AdderOptionalListImportController extends Controller {   

    public function import(Request $request) {
        return view('admin.adder_optional_list_file_import.import');
    }

    public function upload(Request $request) {
        $file = $request->file_import;
        $adderName = $request->adder;
        $adderType = $request->adderType;
    
        $tableName = $adderName ."_" . $adderType;
        if (!empty($file)) {
            $path = '/app/public/';
            
            if (!File::exists($path)) {
                $this->make_directory(storage_path() . '/' . $path);
            }

            $filePath = storage_path() . $path . "/";
            $file_excel = $this->uploadFile($file, $filePath);

            $data = new \SpreadsheetReader($filePath . $file_excel);

            foreach ($data as $key => $d) {

                if ($key < 1) { //Only first row 
                    $createTableField = DynamicTableCreateHelper::createMasterSheetDynamic($tableName, $d); // $d equal to first row  
                    // unset($createTableField[0]); //Remove Id column
                    unset($d[0]); // Remove S.No Rows
                    // dd($createTableField);
                } else if ($key > 0 && is_array($createTableField)) {
                    // echo "<pre>" . print_r($createTableField, 1);
                    $insertData = [];
                    for ($column = 1; $column < count($createTableField); $column ++) {
                        $insertData[$createTableField[$column]['name']] = isset($d[$column]) ? $d[$column] : ''; // $d[$column] = $row  
                    }
                    DB::table($tableName)->insert(
                            array($insertData)
                    );
                }
            }
        }

        Session::flash('message', "Success! Your file has been imported ");
        return redirect()->back();

        // redirect()->back()
    }

    public function make_directory($path) {
        File::isDirectory($path) or File::makeDirectory($path, 0777, true, true);
    }

    public function uploadFile($file, $path, $type = '') {
        $is_uploaded = false;

        if (!empty($file)) {

            $fileName = $file->getClientOriginalName();
            //$extension = \File::extension($file);
            //$fileName = rand(11111111, 99999999) . '.' . $extension;

            if ($file->move($path, $fileName)) {
                $is_uploaded = $fileName;
            }
        }
        return $is_uploaded;
    }
    // A Code: 05-03-2026 Start
    public function import_cp_short(Request $request) {
        return view('admin.cp_short_file_selection_import.import');
    }

    // public function upload_cp_short(Request $request) {
    //     $file = $request->file_import;
        
    //     $tableName = "control_panels_master";
    //     if (!empty($file)) {
    //         $path = '/app/public/';
            
    //         if (!File::exists($path)) {
    //             $this->make_directory(storage_path() . '/' . $path);
    //         }

    //         $filePath = storage_path() . $path . "/";
    //         dd($filePath);
    //         $file_excel = $this->uploadFile($file, $filePath);

    //         $data = new \SpreadsheetReader($filePath . $file_excel);

    //         foreach ($data as $key => $d) {

    //             if ($key < 1) { //Only first row 
    //                 $createTableField = DynamicTableCreateHelper::createMasterSheetDynamic($tableName, $d); // $d equal to first row  
    //                 // unset($createTableField[0]); //Remove Id column
    //                 unset($d[0]); // Remove S.No Rows
    //                 // dd($createTableField);
    //             } else if ($key > 0 && is_array($createTableField)) {
    //                 // echo "<pre>" . print_r($createTableField, 1);
    //                 $insertData = [];
    //                 for ($column = 1; $column < count($createTableField); $column ++) {
    //                     $insertData[$createTableField[$column]['name']] = isset($d[$column]) ? $d[$column] : ''; // $d[$column] = $row  
    //                 }
    //                 DB::table($tableName)->insert(
    //                         array($insertData)
    //                 );
    //             }
    //         }
    //     }

    //     Session::flash('message', "Success! Your file has been imported ");
    //     return redirect()->back();
    // }

    public function upload_cp_short(Request $request)
    {
        $request->validate([
            'file_import' => 'required|mimes:xlsx,xls,csv'
        ]);

        $tableName = "control_panels_master";

        if (!$request->hasFile('file_import')) {
            return redirect()->back()->with('error', 'No file uploaded.');
        }

        try {

            $filePath = storage_path('app/public/uploads/');

            if (!File::exists($filePath)) {
                File::makeDirectory($filePath, 0755, true);
            }

            $file = $request->file('file_import');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $file->move($filePath, $fileName);

            $spreadsheet = new \SpreadsheetReader($filePath . $fileName);

            // Get actual DB columns
            $dbColumns = Schema::getColumnListing($tableName);

            $headerMap = [];
            $batchInsert = [];
            $headerProcessed = false;

            foreach ($spreadsheet as $key => $row) {

                if (empty(array_filter($row))) {
                    continue;
                }

                // First row = header
                if (!$headerProcessed) {

                    foreach ($row as $index => $columnName) {

                        // $formatted = strtolower(trim($columnName));
                        // $formatted = preg_replace('/[^a-z0-9]+/', '_', $formatted);
                        // $formatted = trim($formatted, '_');

                        $formatted = strtolower(trim($columnName));
                        // Replace special characters including dot properly
                        $formatted = str_replace(['.', '-', '/', '\\'], '', $formatted);
                        // Replace spaces with underscore
                        $formatted = preg_replace('/\s+/', '_', $formatted);
                        // Remove any remaining invalid chars
                        $formatted = preg_replace('/[^a-z0-9_]/', '', $formatted);
                        // Remove duplicate underscores
                        $formatted = preg_replace('/_+/', '_', $formatted);
                        $formatted = trim($formatted, '_');

                        // Map only if column exists in DB
                        if (in_array($formatted, $dbColumns)) {
                            $headerMap[$index] = $formatted;
                        }
                    }

                    $headerProcessed = true;
                    continue;
                }

                $insertData = [];

                foreach ($headerMap as $index => $columnName) {

                    if ($columnName == 'id') {
                        continue;
                    }

                    $insertData[$columnName] = isset($row[$index])
                        ? trim($row[$index])
                        : null;
                }

                if (!empty($insertData)) {
                    $batchInsert[] = $insertData;
                }

                if (count($batchInsert) >= 500) {
                    DB::table($tableName)->insert($batchInsert);
                    $batchInsert = [];
                }
            }

            if (!empty($batchInsert)) {
                DB::table($tableName)->insert($batchInsert);
            }

            return redirect()->back()->with('message', 'File imported successfully.');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
    // A Code: 05-03-2026 End


}
