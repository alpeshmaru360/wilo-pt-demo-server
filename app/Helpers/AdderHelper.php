<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use App\Traits\ControlPanelModelIdGet;

Class AdderHelper {

    use ControlPanelModelIdGet;

    public static function getClosestAmpere($code, $search) {
        if ($code >= 45 && $code <= 52) {
            if ($search <= 40) {
                return 40;
            } else if ($search <= 63) {
                return 63;
            } else if ($search <= 100) {
                return 100;
            } else if ($search <= 160) {
                return 160;
            }
        }
        else if($code >= 31  && $code <= 36){

            if ($search <= 40) {
                return 40;
            } else if ($search <= 63) {
                return 63;
            } else if ($search <= 100) {
                return 100;
            } else if ($search <= 160) {
                return 160;
            } else if ($search <= 250) {
                return 250;
            } else if ($search <= 400) {
                return 400;
            }
        }else{
            if ($search <= 40) {
                return 40;
            } else if ($search <= 80) {
                return 80;
            } else if ($search <= 125) {
                return 125;
            } else if ($search <= 200) {
                return 200;
            } else if ($search <= 400) {
                return 400;
            } else if ($search <= 630) {
                return 630;
            } else if ($search <= 800) {
                return 800;
            } else if ($search <= 1000) {
                return 1000;
            } else if ($search <= 1250) {
                return 1250;
            } else if ($search <= 1600) {
                return 1600;
            }
        }
    }

    public static function enclosureAreaExist($qty,$range, $totalEnclousreArea) {
        $nextSize = false;
        $height = 0;
        $width = 0;
        if (strlen($range) == 3) {
            $splitNumber = str_split($range, 1);
            $height = $splitNumber[0];
            $width = $splitNumber[1];
        } else if (strlen($range) == 4) {
            $splitNumber = str_split($range, 1);
            $height = $splitNumber[0] . $splitNumber[1];
            $width = $splitNumber[2];
        } else if (strlen($range) == 5) {
            $splitNumber = str_split($range, 2);
            $height = $splitNumber[0];
            $width = $splitNumber[1];
        }
        $encloureAreaRange = ($height * 100) * ($width * 100); 
        
        if($qty == "0")
        {
            $encloureAreaPercentage = ($encloureAreaRange) * (10 / 100);
        }
        else
        {
            $encloureAreaPercentage = ($encloureAreaRange * $qty) * (10 / 100); //30 % Enclosure area = e.g 600000 * 30% = 
        }
        // $totalEnclousreArea = 539113.64
        // dd( $encloureAreaPercentage);144000.0

        if($totalEnclousreArea < $encloureAreaPercentage) {
            $nextSize = true;
            return $nextSize;
        }
        return $nextSize;
    }  
}


