<?php

namespace App\Jobs;

use Maatwebsite\Excel\Facades\Excel;
use App\Imports\UsersImport;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Log;

class ControlPanelFileImportProcess implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $fileName;

    public function __construct($fileName)
    {
        $this->fileName = $fileName;
    }

    // public function handle()
    // {
    //     ini_set('memory_limit', '1024M');
    //     set_time_limit(0);
    //     $path = storage_path('app/public/'.$this->fileName);
    //     Excel::import(new UsersImport, $path, null, \Maatwebsite\Excel\Excel::CSV);
    // }

    // public function handle()
    // {
    //     ini_set('memory_limit', '1024M');
    //     ini_set('max_execution_time', 0);
    //     set_time_limit(0);

    //     $path = storage_path('app/public/'.$this->fileName);

    //     \Maatwebsite\Excel\Facades\Excel::import(
    //         new \App\Imports\UsersImport,
    //         $path,
    //         null,
    //         \Maatwebsite\Excel\Excel::CSV
    //     );
    // }

    public function handle()
    {
        ini_set('memory_limit', '-1');
        set_time_limit(0);
        $path = storage_path('app/public/'.$this->fileName);
        $handle = fopen($path, "r");
        $batch = [];
        $count = 0;
        while (($row = fgetcsv($handle, 2000, ",")) !== FALSE) {
            $batch[] = [
                'no_of_pump_id' => $row[0] ?? null,
                'power_id' => $row[1] ?? null,
                'voltage_id' => $row[2] ?? null,
                'application_id' => $row[3] ?? null,
                'ambient_temp_id' => $row[4] ?? null,
                'price' => 0,
                'user_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ];
            if(count($batch) == 500){
                DB::table('control_panels')->insert($batch);
                $batch = [];
            }
            $count++;
        }
        if(!empty($batch)){
            DB::table('control_panels')->insert($batch);
        }
        fclose($handle);
    }

}