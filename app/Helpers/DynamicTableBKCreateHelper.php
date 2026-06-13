<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

Class DynamicTableBKCreateHelper {

    public static function createDynamic($table_name, $columns) {

        $fields = [];

        foreach ($columns as $key => $column) {
            if ($key == 0) {
                $fields[] = array('name' => 'id', 'type' => 'increments', 'size' => null, 'index' => null, 'nullable' => 0, 'unsigned' => 0, 'default' => null);
            } else {
                $column = str_replace(" ", "_", $column);
                $column = str_replace(".", "__", $column);
                if ($key <= 9) {
                    $fields[] = array('name' => strtolower($column),
                        'type' => 'string',
                        'size' => 100,
                        'index' => null,
                        'nullable' => 0,
                        'unsigned' => 0,
                        'default' => null);
                } else {
                    $fields[] = array('name' => strtolower($column),
                        'type' => 'tinyInteger',
                        'size' => 11,
                        'index' => null,
                        'nullable' => 0,
                        'unsigned' => 1,
                        'default' => null);
                }
            }
        }
        return DynamicTableCreateHelper::createTable($table_name, $fields);
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
                        'type' => 'tinyInteger',
                        'size' => 11,
                        'index' => null,
                        'nullable' => 0,
                        'unsigned' => 1,
                        'default' => null);
                }
            }
        }
        return DynamicTableCreateHelper::createTable($table_name, $fields);
    }
    public static function createTable($table_name, $fields = []) {
        // check if table is not already exists
        if (Schema::hasTable($table_name)) {
            Schema::dropIfExists($table_name);
        }
      
        
        Schema::create($table_name, function (Blueprint $table) use ($fields, $table_name) {
//            $table->increments('id');
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
