<?php

namespace App\Helpers;

Class MapLatLongHelper {

    public static function getLatLong($url) {

        $pattern = '/@([\d]+.[\d]+),([\d]+.[\d]+)/';
        preg_match_all($pattern, $url, $latLong);

        $getLatLong = null;

        if (is_array($latLong)) {
            $getLatLong['lat'] = $latLong[1];
            $getLatLong['long'] = $latLong[2];
            return call_user_func_array('array_merge', $getLatLong);
        }

        return null;
    }

}
