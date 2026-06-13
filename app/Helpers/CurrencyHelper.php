<?php

namespace App\Helpers;

Class CurrencyHelper {

    public static function withCurrency($price) {
        return  number_format(round($price),0) . '$';
//        return number_format($price, 2) . '$';
    }

}
