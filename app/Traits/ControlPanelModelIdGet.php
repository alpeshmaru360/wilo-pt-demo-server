<?php

namespace App\Traits;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

trait ControlPanelModelIdGet {

    public function getIdByValue($model, $columnName, $value) {
        $getData = $model::where($columnName, $value)->first();
        if ($getData) {
            return $getData->id;
        }
        return false;
    }
    
     public function getValueById($model, $columnName, $id) {
        $getData = $model::where($columnName, $id)->first();
        if ($getData) {
            return $getData->value;
        }
        return false;
    }

}
