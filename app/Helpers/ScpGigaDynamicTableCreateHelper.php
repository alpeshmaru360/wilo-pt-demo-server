<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

Class ScpGigaDynamicTableCreateHelper {

    public static function createDynamic($table_name, $columns1,$columns3) {

        $fields = [];
        $columnBreak = [];
        $addNewColumnBreak = [];
        $addNewParent = [];
        $fields[] = array('name' => 'id', 'type' => 'increments', 'size' => null, 'index' => null, 'nullable' => 0, 'unsigned' => 0, 'default' => null);
         foreach ($columns3 as $key => $column) {
             $fields[] = array('name' => strtolower($column),
                        'type' => 'string',
                        'size' => 100,
                        'index' => null,
                        'nullable' => 0,
                        'unsigned' => 0,
                        'default' => null); 
         }
         
          foreach ($columns1 as $key => $column) {
             $fields[] = array('name' => strtolower($column),
                                'type' => 'float',
                                'size' => 11,
                                'index' => null,
                                'nullable' => 0,
                                'unsigned' => 1,
                                'default' => null);
         }
     
        return AtmosGigaDynamicTableCreateHelper::createNormalTable($table_name, $fields);
    }
    
     public static function createOnlyMasterSheetDynamic($table_name, $columns) {

        $fields = [];

        foreach ($columns as $key => $column) {
            if ($key == 0) {
                $fields[] = array('name' => 'id', 'type' => 'increments', 'size' => null, 'index' => null, 'nullable' => 0, 'unsigned' => 0, 'default' => null);
            } else {
                $column = str_replace(" ", "_", $column);
                $column = str_replace(".", "__", $column);
                if ($key <= 11) {
                    $fields[] = array('name' => strtolower($column),
                        'type' => 'string',
                        'size' => 100,
                        'index' => null,
                        'nullable' => 0,
                        'unsigned' => 0,
                        'default' => null);
                } else {
                    $fields[] = array('name' => strtolower($column),
                        'type' => 'float',
                        'size' => 11,
                        'index' => null,
                        'nullable' => 0,
                        'unsigned' => 1,
                        'default' => null);
                }
            }
        }
        return DynamicTableCreateHelper::createNormalTable($table_name, $fields);
    }

    public static function createMasterSheetDynamic($table_name, $columns) {
        $fields = [];
        foreach ($columns as $key => $column) {
            if ($key == 0) {
                $fields[] = array('name' => 'id', 'type' => 'increments', 'size' => null, 'index' => null, 'nullable' => 0, 'unsigned' => 0, 'default' => null);
            } else {
                $column = str_replace(" ", "_", $column);
                $column = str_replace(".", "__", $column);
                if ($key <= 11) {
                    $fields[] = array('name' => strtolower($column),
                        'type' => 'string',
                        'size' => 100,
                        'index' => null,
                        'nullable' => 0,
                        'unsigned' => 0,
                        'default' => null);
                } else {
                    $fields[] = array('name' => strtolower($column),
                        'type' => 'float',
                        'size' => 11,
                        'index' => null,
                        'nullable' => 0,
                        'unsigned' => 1,
                        'default' => null);
                }
            }
        }
        return DynamicTableCreateHelper::createNormalTable($table_name, $fields);
    }

    public static function createTable($table_name, $fields = [], $columnBreak = [], $addNewColumnBreak = []) {
        if (Schema::hasTable($table_name)) {
            Schema::dropIfExists($table_name);
        }

        Schema::create($table_name, function (Blueprint $table) use ($fields, $table_name) {
            if (count($fields) > 0) {
                foreach ($fields as $field) {
                    $table->{$field['type']}($field['name']);
                }
            }
            $table->timestamps();
        });

        $newColumnFields = [];
        foreach ($fields as $key => $val) {


            if (in_array(trim($val['name']), $addNewColumnBreak, true)) {

            } else {
                $newColumnFields[] = $fields[$key];
            }
        }
        $parentColumn = [];
        foreach ($columnBreak as $key => $val) {
            $parentColumn[$val] = $addNewColumnBreak[$key];
        }
        return array($newColumnFields, $columnBreak, $parentColumn);
    }

    public static function createNormalTable($table_name, $fields = []) {
        // check if table is not already exists
        if (Schema::hasTable($table_name)) {
            Schema::dropIfExists($table_name);
        }

        Schema::create($table_name, function (Blueprint $table) use ($fields, $table_name) {
            if (count($fields) > 0) {
                foreach ($fields as $field) {
                    $table->{$field['type']}($field['name']);
                }
            }
            $table->timestamps();
        });

        return $fields;
    }

}
