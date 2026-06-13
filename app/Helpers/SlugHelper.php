<?php

namespace App\Helpers;

Class SlugHelper {

    public static function strToEng($str, $delimiter = '-') {
        $slug = strtolower(trim(preg_replace('/[\s-]+/', $delimiter, preg_replace('/[^A-Za-z0-9-]+/', $delimiter, preg_replace('/[&]/', 'and', preg_replace('/[\']/', '', iconv('UTF-8', 'ASCII//TRANSLIT', $str))))), $delimiter));
        return $slug;
    }

    public static function strToAr($str, $delimiter = '-') {
        $str = trim($str);

        // Lower case everything
        // using mb_strtolower() function is important for non-Latin UTF-8 string | more info: https://www.php.net/manual/en/function.mb-strtolower.php
        $str = mb_strtolower($str, "UTF-8");


        // Make alphanumeric (removes all other characters)
        // this makes the string safe especially when used as a part of a URL
        // this keeps latin characters and arabic charactrs as well
        $str = preg_replace("/[^a-z0-9_\s\-ءاأإآؤئبتثجحخدذرزسشصضطظعغفقكلمنهويةى]#u/", "", $str);

        // Remove multiple dashes or whitespaces
        $str = preg_replace("/[\s-]+/", " ", $str);

        // Convert whitespaces and underscore to the given separator
        $slug = preg_replace("/[\s_]/",$delimiter, $str);

        return $slug;
    }

}
