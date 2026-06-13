<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\ControlPanel;
use App\Traits\ControlPanelModelIdGet;
use DB;
use Illuminate\Support\Facades\Storage;

class ControlPanelFileImportProcess implements ShouldQueue {

    use Dispatchable,
        InteractsWithQueue,
        Queueable,
        SerializesModels,
        ControlPanelModelIdGet;

    public $data;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($data) {
        $this->data = $data;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle() {
//        $path = Storage::path('public/test.xlsx'); // url come from db
//        $data = new \SpreadsheetReader($path);
        try {
                    set_time_limit(0);
              DB::connection()->disableQueryLog();
//        foreach ($data as $Row) {
            DB::table('control_panels')->insert($this->data);
//        }
        } catch (Exception $e) {
            // bird is clearly not the word
            $this->failed($e);
        }

//        ControlPanel::query()->delete();
//        exit;
//        set_time_limit(0);
//        foreach ($data as $Row) {
//
//
//            $delta[$i] = $Row[1];
//
//            $i++;
//            if ($i > 1) {
//
//                DB::connection()->disableQueryLog();
//                $controlPanel = new ControlPanel;
//                $controlPanel->no_of_pump_id = $this->getIdByValue('App\NumberOfPump', 'value', $Row[0]);
//                $controlPanel->power_id = $this->getIdByValue('App\Power', 'value', $Row[1]); //Power Rating
//                $controlPanel->voltage_id = $this->getIdByValue('App\Voltage', 'value', $Row[2]); //Power Supply
//                $controlPanel->application_id = $this->getIdByValue('App\Application', 'value', $Row[3]);
//                $controlPanel->ambient_temp_id = $this->getIdByValue('App\AmbientTemp', 'value', $Row[4]);
//                $controlPanel->stater_type_id = $this->getIdByValue('App\StarterType', 'value', $Row[5]);
//                $controlPanel->communication_protocol_id = $this->getIdByValue('App\ComunicationProtocol', 'value', $Row[6]);
//                $controlPanel->ip_rating_id = $this->getIdByValue('App\IpRating', 'value', $Row[7]);
//                $controlPanel->components_id = $this->getIdByValue('App\Component', 'value', $Row[8]);
//                $controlPanel->enclosure_id = $this->getIdByValue('App\Enclousre', 'value', $Row[9]);
//                $controlPanel->range = $this->getIdByValue('App\Range', 'value', $Row[10]);
//                $controlPanel->folder_name = isset($Row[11]) ? $Row[11] : '';
//                $controlPanel->file_name_under_folder = isset($Row[12]) ? $Row[12] : '';
//                $controlPanel->table_name = isset($Row[13]) ? $Row[13] : '';
//                $controlPanel->starter_code = isset($Row[14]) ? $Row[14] : '';
//                $controlPanel->price = 0;
//                $controlPanel->user_id = 1;
//                $controlPanel->created_at = date("Y-m-d H:i:s");
//                $controlPanel->updated_at = date("Y-m-d H:i:s");
//                $controlPanel->save();
//
//                DB::connection()->disableQueryLog();
//            }
//        }
    }

    public function failed($exception) {
        return $exception->getMessage();
        // etc...
    }

}
