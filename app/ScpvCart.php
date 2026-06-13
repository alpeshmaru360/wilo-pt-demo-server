<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ScpvCart extends Model {

    public static function cartData() {
        $scpvCartData = ScpvCart::where('user_id', auth()->user()->id)
                ->whereNull('quotation_no')
                ->get();
        return $scpvCartData;
    }

    public static function cartDataByQuotation($ids) {
        $scpvCartData = ScpvCart::whereIn('id', $ids)
                ->get();
        return $scpvCartData;
    }

    public static function cartDataByUserId($id,$article_number = false) {
        if($article_number == false){
            $scpvCartData = ScpvCart::where('user_id', $id)->whereNotNull('article_number')->with('documents')
            ->get();
        }else{
            $scpvCartData = ScpvCart::where('user_id', $id)->where('article_number',$article_number)->whereNotNull('article_number')->with('documents')
            ->get();
        }
        return $scpvCartData;
    }

    public function documents() {
        return $this->hasMany('App\ArticleFile', 'article_number', 'article_number');
    }

    public  function scpvItems() {
        return $this->hasMany('App\ScpvItem', 'scpv_cart_id', 'id');
    }

    public static function getSCPV() {
        $scpvCartData = ScpvCart::get();
        return $scpvCartData;
    }

}
