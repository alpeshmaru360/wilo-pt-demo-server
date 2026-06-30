<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Jobs\ControlPanelFileImportProcess;
use Carbon\Carbon;
use App\Traits\ControlPanelModelIdGet;
use Excel;
use App\Imports\UsersImport;
use DB;
use Session;
use App\ControlPanel;

class CPBasicController extends Controller {

    use ControlPanelModelIdGet;

    public function controlpanel_Basic(Request $request) 
    {
        dd($request->all());
        set_time_limit(0);
        $filename = DB::table('control_panel_file_upload')
                        ->select('filename')
                        ->where('id', 1)
                        ->get()->toArray();

        if (empty($filename)) {
            Session::flash('message', "File have not found");
            return redirect()->back();
        }
        $filename = $filename[0]->filename;
        $path = Storage::path('public/' . $filename);
        ControlPanel::truncate();
        $insertData = [];
        $data = Excel::import(new UsersImport, $path);
        unset($data[0][0]);
        $array = array_chunk($data[0], 10);
        foreach ($array as $chunk) {
            $insertData = [];
            foreach ($chunk as $Row) {
                $insertData[] = array('no_of_pump_id' => $this->getIdByValue('App\NumberOfPump', 'value', $Row[0]),
                    'power_id' => $this->getIdByValue('App\Power', 'value', $Row[1]),
                    'voltage_id' => $this->getIdByValue('App\Voltage', 'value', $Row[2]), //Power Supply
                    'application_id' => $this->getIdByValue('App\Application', 'value', $Row[3]),
                    'ambient_temp_id' => $this->getIdByValue('App\AmbientTemp', 'value', $Row[4]),
                    'stater_type_id' => $this->getIdByValue('App\StarterType', 'value', $Row[5]),
                    'communication_protocol_id' => $this->getIdByValue('App\ComunicationProtocol', 'value', $Row[6]),
                    'ip_rating_id' => $this->getIdByValue('App\IpRating', 'value', $Row[7]),
                    'components_id' => $this->getIdByValue('App\Component', 'value', $Row[8]),
                    'enclosure_id' => $this->getIdByValue('App\Enclousre', 'value', $Row[9]),
                    'range' => $this->getIdByValue('App\Range', 'value', $Row[10]),
                    'folder_name' => isset($Row[11]) ? $Row[11] : '',
                    'file_name_under_folder' => isset($Row[12]) ? $Row[12] : '',
                    'table_name' => isset($Row[13]) ? $Row[13] : '',
                    'starter_code' => isset($Row[14]) ? $Row[14] : '',
                    'price' => 0,
                    'user_id' => 1,
                    'created_at' => date("Y-m-d H:i:s"),
                    'updated_at' => date("Y-m-d H:i:s"),
                );
            }
            $job = (new ControlPanelFileImportProcess($insertData))
                    ->delay(Carbon::now()->addSeconds(15));
            dispatch($job);
        }

        Session::flash('message', "File imported into database");
        return redirect()->back();
    }

}
