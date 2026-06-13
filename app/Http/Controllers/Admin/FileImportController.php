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
use App\ControlPanel;

class FileImportController extends Controller {

    public function index() {
        abort_unless(\Gate::allows('product_access'), 403);

        $products = Product::all();

        return view('admin.products.index', compact('products'));
    }

    public function create() {
        abort_unless(\Gate::allows('product_create'), 403);

        return view('admin.products.create');
    }

    public function store(StoreProductRequest $request) {
        abort_unless(\Gate::allows('product_create'), 403);

        $product = Product::create($request->all());

        return redirect()->route('admin.products.index');
    }

    public function edit(Product $product) {
        abort_unless(\Gate::allows('product_edit'), 403);

        return view('admin.products.edit', compact('product'));
    }

    public function update(UpdateProductRequest $request, Product $product) {
        abort_unless(\Gate::allows('product_edit'), 403);

        $product->update($request->all());

        return redirect()->route('admin.products.index');
    }

    public function show(Product $product) {
        abort_unless(\Gate::allows('product_show'), 403);

        return view('admin.products.show', compact('product'));
    }

    public function destroy(Product $product) {
        abort_unless(\Gate::allows('product_delete'), 403);

        $product->delete();

        return back();
    }

    public function massDestroy(MassDestroyProductRequest $request) {
        Product::whereIn('id', request('ids'))->delete();

        return response(null, 204);
    }

    public function import(Request $request) {
//        dd('dd');
        $ranges = Range::all();
        $folderNames = ControlPanel::select('folder_name', 'file_name_under_folder')
                        ->groupBy('folder_name')->get();


//        dd($folderName);
        return view('admin.file-import.import', compact('ranges', 'folderNames'));
    }

    public function ajaxFileName(Request $request) {
//        dd('dd');
        $filenames = ControlPanel::select('folder_name', 'file_name_under_folder')
                        ->where('folder_name', $request->folderName)
                        ->groupBy('file_name_under_folder')->get();

        return response()->json(array('filenames' => $filenames), 200);
    }

    public function upload(Request $request) {
        set_time_limit(0);
        $rangeName = Range::find($request->range)->value;
        $folderName = $request->folder_name;
        $fileName = $request->file_name;
      
        $tableName = ControlPanel::select('table_name')
                        ->where('folder_name', $folderName)
                        ->where('file_name_under_folder',$fileName)
                        ->groupBy('file_name_under_folder')
                ->first()->table_name;
//        dd($tableName);
        $file = $request->file_import;
        if (!empty($file)) {
            $path = '/app/public/' . $rangeName;

            if (!File::exists($path)) {
                $this->make_directory(storage_path() . '/' . $path);
            }

            $filePath = storage_path() . $path . "/";
            $file_excel = $this->uploadFile($file, $filePath);

            $data = new \SpreadsheetReader($filePath . $file_excel);

            echo $tableName;
//            die;
            foreach ($data as $key => $d) {

                if ($key < 1) { //Only first row 
                    $createTableFieldArray = DynamicTableCreateHelper::createDynamic($tableName, $d); // $d equal to first row  
                    $createTableField = $createTableFieldArray[0];
                    $columnBreak = $createTableFieldArray[1];
                    $parentColumn = $createTableFieldArray[2];
                    
                    unset($createTableField[0]); //Remove Id column
                    unset($d[0]); // Remove S.No Rows
                } else if ($key > 0 && is_array($createTableField)) {
//                 echo "<pre>" . print_r($createTableField, 1);
                    $insertData = [];
                    for ($column = 1; $column <= count($createTableField); $column ++) {

                        $insertData[$createTableField[$column]['name']] = isset($d[$column]) ? $d[$column] : 0; // $d[$column] = $row  
                    }
//                    echo "<pre>" . print_r($insertData, 1);
                    DB::table($tableName)->insert(
                            array($insertData)
                    );
                }
            }

            foreach ($columnBreak as $val) {
                DB::connection()->disableQueryLog();
                $users = DB::table($tableName)->select($val, 'id')->get();

                foreach ($users as $user) {
                    DB::connection()->disableQueryLog();
                    echo "id " . $user->id . " updated-column " . $parentColumn[$val] . ' qty' . $user->$val . "***</br>";
                    $update = DB::table($tableName)
                            ->where('id', $user->id)
                            ->update([$parentColumn[$val] => $user->$val]);
                }
                DB::connection()->disableQueryLog();
            }
        }
        die;
        Session::flash('message', "Success! Your file has been imported ");
        return redirect()->back();

//        redirect()->back()
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

    public function removeNumberConvertToString($str) {

      
        $alpha = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X ', 'Y', 'Z');
        preg_match_all('!\d+!', $str, $matches);
       
        $a = $str;
        if (is_array($matches)) {
            foreach ($matches[0] as $val) {
                $a = str_replace($val, $alpha[$val], $a);
            }
            return $a;
        }
        return $a;
    }

}
