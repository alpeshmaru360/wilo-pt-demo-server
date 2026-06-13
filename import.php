<?php
ini_set('memory_limit','-1');
set_time_limit(0);

$pdo = new PDO("mysql:host=localhost;dbname=wilo_pt_internal_server","wilo_server","RTSDGDFHDFHFGH$YTREGDFG");

$f = fopen("File_Selection_Final.csv","r");
fgetcsv($f);

$batch=[];
while(($r=fgetcsv($f))!==false){
  $batch[]="('{$r[0]}','{$r[1]}','{$r[2]}','{$r[3]}',0,1,now(),now())";
  if(count($batch)==3000){
    $pdo->exec("INSERT INTO control_panels (no_of_pump_id,power_id,voltage_id,application_id,price,user_id,created_at,updated_at) VALUES ".implode(",",$batch));
    $batch=[];
  }
}

if($batch){
  $pdo->exec("INSERT INTO control_panels (no_of_pump_id,power_id,voltage_id,application_id,price,user_id,created_at,updated_at) VALUES ".implode(",",$batch));
}

echo "DONE";

