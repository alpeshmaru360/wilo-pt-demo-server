<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Schema; // A Code: 05-03-2026
use App\Jobs\ControlPanelFileImportProcess;
use Carbon\Carbon;
use App\Traits\ControlPanelModelIdGet;
use Excel;
use App\Imports\UsersImport;
use File;
use App\ControlPanel;
use Artisan;
use DB;
use Session;

class CPBasicController extends Controller {
    use ControlPanelModelIdGet;
    public function import(Request $request) {
        return view('admin.main_cp_file_import.import');
    }

    // public function upload(Request $request)
    // {
    //     $file = $request->file('file_import');
    //     if (!$file) {
    //         return back()->with('error','No file');
    //     }
    //     $path = storage_path('app/public/');
    //     if (!File::exists($path)) {
    //         File::makeDirectory($path,0777,true,true);
    //     }
    //     $fileName = time().'_'.$file->getClientOriginalName();
    //     $file->move($path,$fileName);
    //     dispatch(new ControlPanelFileImportProcess($fileName));
    //     return back()->with('success','File uploaded. Import running in background.');
    // }

    public function upload(Request $request)
    {
        $request->validate([
            'file_import' => 'required|file'
        ]);
        $file = $request->file('file_import');
        $path = storage_path('app/public/');
        if (!\File::exists($path)) {
            \File::makeDirectory($path, 0777, true, true);
        }
        $fileName = time().'_'.$file->getClientOriginalName();
        $file->move($path, $fileName);
        \App\Jobs\ControlPanelFileImportProcess::dispatch($fileName);
        return back()->with('success', 'File uploaded. Import background is working on.');
    }

    public function uploadFile($file, $path, $type = '') {
        $is_uploaded = false;
        if (!empty($file)) {
            $fileName = $file->getClientOriginalName();
            if ($file->move($path, $fileName)) {
                $is_uploaded = $fileName;
            }
        }
        return $is_uploaded;
    }

    public function make_directory($path) {
        File::isDirectory($path) or File::makeDirectory($path, 0777, true, true);
    }

    // A Code: 05-03-2026 Start
    public function import_short(Request $request) {
        return view('admin.short_cp_file_import.import');
    }

    public function upload_short(Request $request)
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

            // Get DB columns
            $dbColumns = Schema::getColumnListing($tableName);

            // Truncate BEFORE inserting (No transaction here)
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            DB::table($tableName)->truncate();
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');

            $headerMap = [];
            $batchInsert = [];
            $headerProcessed = false;

            foreach ($spreadsheet as $row) {

                if (empty(array_filter($row))) {
                    continue;
                }

                if (!$headerProcessed) {

                    foreach ($row as $index => $columnName) {

                        $formatted = strtolower(trim($columnName));
                        $formatted = str_replace(['.', '-', '/', '\\'], '', $formatted);
                        $formatted = preg_replace('/\s+/', '_', $formatted);
                        $formatted = preg_replace('/[^a-z0-9_]/', '', $formatted);
                        $formatted = preg_replace('/_+/', '_', $formatted);
                        $formatted = trim($formatted, '_');

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

    // A Code: 11-04-2026 Start
    public function import_warehouse_pump(Request $request){
        return view('admin.warehouse-pump-details.import');
    }

    // public function upload_warehouse_pump(Request $request)
    // {
    //     $request->validate([
    //         'file_import' => 'required|mimes:xlsx,xls,csv'
    //     ]);

    //     $tableName = "warehouse_pump_details";

    //     if (!$request->hasFile('file_import')) {
    //         return back()->with('error', 'No file uploaded.');
    //     }

    //     DB::beginTransaction();

    //     try {

    //         // ===== FILE UPLOAD =====
    //         $filePath = storage_path('app/public/uploads/');

    //         if (!File::exists($filePath)) {
    //             File::makeDirectory($filePath, 0755, true);
    //         }

    //         $file = $request->file('file_import');
    //         $fileName = time() . '_' . $file->getClientOriginalName();
    //         $file->move($filePath, $fileName);

    //         $fullPath = $filePath . $fileName;

    //         // ===== LOAD EXCEL =====
    //         $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($fullPath);
    //         $sheetData = $spreadsheet->getActiveSheet()->toArray();

    //         if (empty($sheetData)) {
    //             throw new \Exception("File is empty.");
    //         }

    //         // ===== GET DB COLUMNS =====
    //         $dbColumns = array_map(function ($col) {
    //             return $col->Field;
    //         }, DB::select("SHOW COLUMNS FROM {$tableName}"));

    //         if (empty($dbColumns)) {
    //             throw new \Exception("Database table not found.");
    //         }

    //         // ===== HEADER PROCESS =====
    //         $rawHeader = array_shift($sheetData);

    //         $validIndexes = [];
    //         $headerMap = [];

    //         // Custom mapping (important for your Excel)
    //         $customMap = [
    //             'total_q' => 'total_qty',
    //             'picking_q' => 'picking_qty',
    //             'balance_q' => 'balance_qty',
    //         ];

    //         foreach ($rawHeader as $index => $columnName) {

    //             if (is_null($columnName) || trim($columnName) === '') {
    //                 continue;
    //             }

    //             $formatted = strtolower(trim($columnName));
    //             $formatted = preg_replace('/[^a-z0-9]/', '_', $formatted);
    //             $formatted = preg_replace('/_+/', '_', $formatted);
    //             $formatted = trim($formatted, '_');

    //             // Apply custom mapping
    //             $dbColumn = $customMap[$formatted] ?? $formatted;

    //             if (in_array($dbColumn, $dbColumns)) {
    //                 $validIndexes[] = $index;
    //                 $headerMap[$index] = $dbColumn;
    //             }
    //         }

    //         if (count($headerMap) < 3) {
    //             throw new \Exception("Header does not match database columns.");
    //         }

    //         // ===== TRUNCATE TABLE =====
    //         DB::statement('SET FOREIGN_KEY_CHECKS=0;');
    //         DB::table($tableName)->truncate();
    //         DB::statement('SET FOREIGN_KEY_CHECKS=1;');

    //         // ===== INSERT DATA =====
    //         $batchInsert = [];
    //         $lastNoOfPallets = null; // 🔥 carry forward variable

    //         foreach ($sheetData as $row) {

    //             if (empty(array_filter($row))) {
    //                 continue;
    //             }

    //             $insertData = [];

    //             foreach ($validIndexes as $index) {

    //                 $columnName = $headerMap[$index];

    //                 if ($columnName === 'id') continue;

    //                 $value = isset($row[$index]) ? trim((string)$row[$index]) : null;

    //                 // APPLY ONLY FOR NO. OF PALLETS
    //                 if ($columnName === 'no_of_pallets') {

    //                     if ($value === '' || $value === null) {
    //                         $value = $lastNoOfPallets;
    //                     } else {
    //                         $lastNoOfPallets = $value;
    //                     }

    //                 } else {
    //                     // Normal null handling
    //                     $value = ($value === '') ? null : $value;
    //                 }

    //                 $insertData[$columnName] = $value;
    //             }

    //             if (!empty($insertData)) {
    //                 $batchInsert[] = $insertData;
    //             }

    //             // Batch insert
    //             if (count($batchInsert) >= 500) {
    //                 DB::table($tableName)->insert($batchInsert);
    //                 $batchInsert = [];
    //             }
    //         }

    //         // Insert remaining
    //         if (!empty($batchInsert)) {
    //             DB::table($tableName)->insert($batchInsert);
    //         }

    //         DB::commit();

    //         // ===== DELETE FILE =====
    //         if (File::exists($fullPath)) {
    //             File::delete($fullPath);
    //         }

    //         return back()->with('message', 'File imported successfully.');

    //     } catch (\Exception $e) {

    //         DB::rollBack();

    //         return back()->with('error', $e->getMessage());
    //     }
    // }

    public function upload_warehouse_pump(Request $request)
    {
        $request->validate([
            'file_import' => 'required|mimes:xlsx,xls,csv'
        ]);

        $tableName = "warehouse_pump_details";

        if (!$request->hasFile('file_import')) {
            return back()->with('error', 'No file uploaded.');
        }

        DB::beginTransaction();

        try {

            // ===== FILE UPLOAD =====
            $filePath = storage_path('app/public/uploads/');

            if (!File::exists($filePath)) {
                File::makeDirectory($filePath, 0755, true);
            }

            $file = $request->file('file_import');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $file->move($filePath, $fileName);

            $fullPath = $filePath . $fileName;

            // ===== LOAD EXCEL =====
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($fullPath);
            $sheetData = $spreadsheet->getActiveSheet()->toArray();

            if (empty($sheetData)) {
                throw new \Exception("File is empty.");
            }

            // ===== GET DB COLUMNS =====
            $dbColumns = array_map(function ($col) {
                return $col->Field;
            }, DB::select("SHOW COLUMNS FROM {$tableName}"));

            if (empty($dbColumns)) {
                throw new \Exception("Database table not found.");
            }

            // ===== HEADER PROCESS =====
            $rawHeader = array_shift($sheetData);

            $validIndexes = [];
            $headerMap = [];

            // Custom mapping
            $customMap = [
                'total_q'   => 'total_qty',
                'picking_q' => 'picking_qty',
                'balance_q' => 'balance_qty',
            ];

            foreach ($rawHeader as $index => $columnName) {

                if (is_null($columnName) || trim($columnName) === '') {
                    continue;
                }

                $formatted = strtolower(trim($columnName));
                $formatted = preg_replace('/[^a-z0-9]/', '_', $formatted);
                $formatted = preg_replace('/_+/', '_', $formatted);
                $formatted = trim($formatted, '_');

                // Apply custom mapping
                $dbColumn = $customMap[$formatted] ?? $formatted;

                if (in_array($dbColumn, $dbColumns)) {
                    $validIndexes[] = $index;
                    $headerMap[$index] = $dbColumn;
                }
            }

            if (count($headerMap) < 3) {
                throw new \Exception("Header does not match database columns.");
            }

            // ===== DELETE OLD DATA =====
            // Using delete() instead of truncate()
            // because truncate breaks transaction in MySQL
            DB::table($tableName)->delete();

            // ===== INSERT DATA =====
            $batchInsert = [];
            $lastNoOfPallets = null;

            foreach ($sheetData as $row) {

                // Skip empty rows
                if (empty(array_filter($row))) {
                    continue;
                }

                $insertData = [];

                foreach ($validIndexes as $index) {

                    $columnName = $headerMap[$index];

                    // Skip ID
                    if ($columnName === 'id') {
                        continue;
                    }

                    $value = isset($row[$index])
                        ? trim((string)$row[$index])
                        : null;

                    // Carry forward no_of_pallets value
                    if ($columnName === 'no_of_pallets') {

                        if ($value === '' || $value === null) {
                            $value = $lastNoOfPallets;
                        } else {
                            $lastNoOfPallets = $value;
                        }

                    } else {

                        $value = ($value === '') ? null : $value;
                    }

                    $insertData[$columnName] = $value;
                }

                if (!empty($insertData)) {
                    $batchInsert[] = $insertData;
                }

                // Batch insert every 500 rows
                if (count($batchInsert) >= 500) {
                    DB::table($tableName)->insert($batchInsert);
                    $batchInsert = [];
                }
            }

            // Insert remaining rows
            if (!empty($batchInsert)) {
                DB::table($tableName)->insert($batchInsert);
            }

            DB::commit();

            // ===== DELETE FILE =====
            if (File::exists($fullPath)) {
                File::delete($fullPath);
            }

            return back()->with('message', 'File imported successfully.');

        } catch (\Exception $e) {

            DB::rollBack();

            // Delete uploaded file if exists
            if (isset($fullPath) && File::exists($fullPath)) {
                File::delete($fullPath);
            }

            return back()->with('error', $e->getMessage());
        }
    }
    // A Code: 11-04-2026 End 

}
