<?php

namespace App\Http\Controllers\Admin\FireFighting;

use App\Http\Controllers\Controller;
use App\Imports\ExcelImportData;
use App\Models\FireFighting\BatteryMaster;
use App\Models\FireFighting\ControlPanelMaster;
use App\Models\FireFighting\DieselPump;
use App\Models\FireFighting\DieselTankMaster;
use App\Models\FireFighting\ElectricalPump;
use App\Models\FireFighting\FireFightingAdders;
use App\Models\FireFighting\FireFightingFlowMeter;
use App\Models\FireFighting\FireFightingMotor;
use App\Models\FireFighting\FireFightingPressureReliefValve;
use App\Models\FireFighting\FireFightingWasteCone;
use App\Models\FireFighting\JockeyPump;
use App\Models\FireFighting\OptionalMaster;
use DB;
use File;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Session;

class FireFightingPumpController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
       
            return $next($request);
        });
    }
    
    public function dieselPumpImport()
    {
        return view('admin.fire-fighting.diesel-pump-import');
    }

    public function dieselPumpImportStore(Request $request)
    {
        $this->validate($request, [
            'file_import' => 'required'
        ]);
        if ($request->hasFile('file_import')) {
            ini_set('memory_limit', '-1');
            $csvfile = $request->file('file_import');
            $import = new ExcelImportData();
            $excel = Excel::Import($import, $csvfile);

            $save_data = $import->getDataList();
            DieselPump::truncate();
            $new_data = new DieselPump();
            $data_keys = $new_data->getFillable();
            // dd($save_data);
            array_map(function ($val) use ($data_keys)
            {
                $val_data = array_slice($val, 0, count($data_keys), true);
                $val_data1 = array_filter($val_data);
                if (count($val_data1) > 0) {
                    DieselPump::create(array_combine($data_keys, $val_data));
                }
            }, $save_data);

            Session::flash('message', "Success! Your file has been imported");
        }
        return redirect()->back();
    }

    public function electricalPumpImport()
    {
        return view('admin.fire-fighting.electrical-pump-import');
    }

    public function electricalPumpImportStore(Request $request)
    {
        $this->validate($request, [
            'file_import' => 'required'
        ]);
        if ($request->hasFile('file_import')) {
            ini_set('memory_limit', '-1');
            $csvfile = $request->file('file_import');
            $import = new ExcelImportData();
            $excel = Excel::Import($import, $csvfile);
            
            $save_data = $import->getDataList();
            ElectricalPump::truncate();
            $new_data = new ElectricalPump();
            $data_keys = $new_data->getFillable();

            // array_map(function ($val) use ($data_keys)
            // {
            //     $val_data = array_slice($val, 0, count($data_keys), true);
            //     ElectricalPump::create(array_combine($data_keys, $val_data));
            // }, $save_data);

            array_map(function ($val) use ($data_keys) {
                $val_data = array_slice($val, 0, count($data_keys), true);
                $val_data1 = array_filter($val_data);

                // Only process if there's data AND column count matches
                if (count($val_data1) > 0 && count($val_data) === count($data_keys)) {
                    ElectricalPump::create(array_combine($data_keys, $val_data));
                }
            }, $save_data);


            Session::flash('message', "Success! Your file has been imported");
        }
        return redirect()->back();
    }

    public function jockeyPumpImport()
    {
        return view('admin.fire-fighting.jockey-pump-import');
    }

    public function jockeyPumpImportStore(Request $request)
    {
        
        $this->validate($request, [
            'file_import' => 'required'
        ]);
        if ($request->hasFile('file_import')) {
            ini_set('memory_limit', '-1');
            $csvfile = $request->file('file_import');
            $import = new ExcelImportData();
            $excel = Excel::Import($import, $csvfile);

            $save_data = $import->getDataList();
            JockeyPump::truncate();
            $new_data = new JockeyPump();
            $data_keys = $new_data->getFillable();
            array_map(function ($val) use ($data_keys)
            {
                $val_data = array_slice($val, 0, count($data_keys), true);
                JockeyPump::create(array_combine($data_keys, $val_data));
            }, $save_data);

            Session::flash('message', "Success! Your file has been imported");
        }
        return redirect()->back();
    }

    public function batteryMasterImport()
    {
        return view('admin.fire-fighting.battery-master-import');
    }

    public function batteryMasterImportStore(Request $request)
    {
        
        $this->validate($request, [
            'file_import' => 'required'
        ]);
        if ($request->hasFile('file_import')) {
            ini_set('memory_limit', '-1');
            $csvfile = $request->file('file_import');
            $import = new ExcelImportData();
            $excel = Excel::Import($import, $csvfile);

            $save_data = $import->getDataList();
            BatteryMaster::truncate();
            $new_data = new BatteryMaster();
            $data_keys = $new_data->getFillable();

            array_map(function ($val) use ($data_keys)
            {
                $val_data = array_slice($val, 0, count($data_keys), true);
                BatteryMaster::create(array_combine($data_keys, $val_data));
            }, $save_data);

            Session::flash('message', "Success! Your file has been imported");
        }
        return redirect()->back();
    }

    public function dieselTankMasterImport()
    {
        return view('admin.fire-fighting.diesel-tank-master-import');
    }

    public function dieselTankMasterImportStore(Request $request)
    {
        
        $this->validate($request, [
            'file_import' => 'required'
        ]);
        if ($request->hasFile('file_import')) {
            ini_set('memory_limit', '-1');
            $csvfile = $request->file('file_import');
            $import = new ExcelImportData();
            $excel = Excel::Import($import, $csvfile);

            $save_data = $import->getDataList();
            DieselTankMaster::truncate();
            $new_data = new DieselTankMaster();
            $data_keys = $new_data->getFillable();

            array_map(function ($val) use ($data_keys)
            {
                $val_data = array_slice($val, 0, count($data_keys), true);
                DieselTankMaster::create(array_combine($data_keys, $val_data));
            }, $save_data);

            Session::flash('message', "Success! Your file has been imported");
        }
        return redirect()->back();
    }

    public function optionalMasterImport()
    {
        return view('admin.fire-fighting.optional-master-import');
    }

    public function optionalMasterImportStore(Request $request)
    {
        $this->validate($request, [
            'file_import' => 'required'
        ]);
        if ($request->hasFile('file_import')) {
            ini_set('memory_limit', '-1');
            $csvfile = $request->file('file_import');
            $import = new ExcelImportData();
            $excel = Excel::Import($import, $csvfile);

            $save_data = $import->getDataList();
            OptionalMaster::truncate();
            $new_data = new OptionalMaster();
            $data_keys = $new_data->getFillable();
            array_shift($save_data);
            // dd($data_keys, $save_data);
            array_map(function ($val) use ($data_keys)
            {
                $val_data = array_slice($val, 0, count($data_keys), true);
                OptionalMaster::create(array_combine($data_keys, $val_data));
            }, $save_data);

            Session::flash('message', "Success! Your file has been imported");
        }
        return redirect()->back();
    }

    public function addersImport()
    {
        return view('admin.fire-fighting.adders');
    }

    public function addersImportStore(Request $request)
    {
        $this->validate($request, [
            'file_import' => 'required'
        ]);
        if ($request->hasFile('file_import')) {
            ini_set('memory_limit', '-1');
            $csvfile = $request->file('file_import');
            $import = new ExcelImportData();
            $excel = Excel::Import($import, $csvfile);

            $save_data = $import->getDataList();
            FireFightingAdders::truncate();
            $new_data = new FireFightingAdders();
            $data_keys = $new_data->getFillable();

            array_map(function ($val) use ($data_keys)
            {
                $val_data = array_slice($val, 0, count($data_keys), true);
                FireFightingAdders::create(array_combine($data_keys, $val_data));
            }, $save_data);

            Session::flash('message', "Success! Your file has been imported");
        }
        return redirect()->back();
    }

    public function controlePanelMasterImport()
    {
        return view('admin.fire-fighting.controlePanelMaster');
    }

    public function controlePanelMasterImportStore(Request $request)
    {
        $this->validate($request, [
            'file_import' => 'required'
        ]);
        if ($request->hasFile('file_import')) {
            ini_set('memory_limit', '-1');
            $csvfile = $request->file('file_import');
            $import = new ExcelImportData();
            $excel = Excel::Import($import, $csvfile);

            $save_data = $import->getDataList();
            ControlPanelMaster::truncate();
            $new_data = new ControlPanelMaster();
            $data_keys = $new_data->getFillable();

            array_map(function ($val) use ($data_keys)
            {
                $val_data = array_slice($val, 0, count($data_keys), true);
                ControlPanelMaster::create(array_combine($data_keys, $val_data));
            }, $save_data);

            Session::flash('message', "Success! Your file has been imported");
        }
        return redirect()->back();
    }

    public function motorMasterImport()
    {
        return view('admin.fire-fighting.motorMaster');
    }

    public function motorMasterImportStore(Request $request)
    {
        $this->validate($request, [
            'file_import' => 'required'
        ]);
        if ($request->hasFile('file_import')) {
            ini_set('memory_limit', '-1');
            $csvfile = $request->file('file_import');
            $import = new ExcelImportData();
            $excel = Excel::Import($import, $csvfile);

            $save_data = $import->getDataList();
            FireFightingMotor::truncate();
            $new_data = new FireFightingMotor();
            $data_keys = $new_data->getFillable();

            array_map(function ($val) use ($data_keys)
            {
                $val_data = array_slice($val, 0, count($data_keys), true);
                FireFightingMotor::create(array_combine($data_keys, $val_data));
            }, $save_data);

            Session::flash('message', "Success! Your file has been imported");
        }
        return redirect()->back();
    }

    public function flowMeterMasterImport()
    {
        return view('admin.fire-fighting.flowMeterMaster');
    }

    public function flowMeterMasterImportStore(Request $request)
    {
        $this->validate($request, [
            'file_import' => 'required'
        ]);
        if ($request->hasFile('file_import')) {
            ini_set('memory_limit', '-1');
            $csvfile = $request->file('file_import');
            $import = new ExcelImportData();
            $excel = Excel::Import($import, $csvfile);

            $save_data = $import->getDataList();
            FireFightingFlowMeter::truncate();
            $new_data = new FireFightingFlowMeter();
            $data_keys = $new_data->getFillable();

            array_map(function ($val) use ($data_keys)
            {
                $val_data = array_slice($val, 0, count($data_keys), true);
                FireFightingFlowMeter::create(array_combine($data_keys, $val_data));
            }, $save_data);

            Session::flash('message', "Success! Your file has been imported");
        }
        return redirect()->back();
    }

    public function pressureReliefValveMasterImport()
    {
        return view('admin.fire-fighting.pressureReliefValve');
    }

    public function pressureReliefValveMasterImportStore(Request $request)
    {
        $this->validate($request, [
            'file_import' => 'required'
        ]);
        if ($request->hasFile('file_import')) {
            ini_set('memory_limit', '-1');
            $csvfile = $request->file('file_import');
            $import = new ExcelImportData();
            $excel = Excel::Import($import, $csvfile);

            $save_data = $import->getDataList();
            FireFightingPressureReliefValve::truncate();
            $new_data = new FireFightingPressureReliefValve();
            $data_keys = $new_data->getFillable();

            array_map(function ($val) use ($data_keys)
            {
                $val_data = array_slice($val, 0, count($data_keys), true);
                FireFightingPressureReliefValve::create(array_combine($data_keys, $val_data));
            }, $save_data);

            Session::flash('message', "Success! Your file has been imported");
        }
        return redirect()->back();
    }

    public function wasteConeMasterImport()
    {
        return view('admin.fire-fighting.wasteCone');
    }

    public function wasteConeMasterImportStore(Request $request)
    {
        $this->validate($request, [
            'file_import' => 'required'
        ]);
        if ($request->hasFile('file_import')) {
            ini_set('memory_limit', '-1');
            $csvfile = $request->file('file_import');
            $import = new ExcelImportData();
            $excel = Excel::Import($import, $csvfile);

            $save_data = $import->getDataList();
            FireFightingWasteCone::truncate();
            $new_data = new FireFightingWasteCone();
            $data_keys = $new_data->getFillable();

            array_map(function ($val) use ($data_keys)
            {
                $val_data = array_slice($val, 0, count($data_keys), true);
                FireFightingWasteCone::create(array_combine($data_keys, $val_data));
            }, $save_data);

            Session::flash('message', "Success! Your file has been imported");
        }
        return redirect()->back();
    }
}
