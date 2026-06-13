<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ScpCart extends Model {

    public static function cartData() {
        $scpCartData = ScpCart::where('user_id', auth()->user()->id)
                ->whereNull('quotation_no')
                ->get();
        return $scpCartData;
    }

    public static function cartDataByQuotation($ids) {
        $scpCartData = ScpCart::whereIn('id', $ids)
                ->get();
        return $scpCartData;
    }

    public static function cartDataByUserId($id,$article_number = false) {
        if($article_number == false){
        $scpCartData = ScpCart::where('user_id', $id)->whereNotNull('article_number')->with('documents')
            ->get();
        }else{
            $scpCartData = ScpCart::where('user_id', $id)->where('article_number',$article_number)->whereNotNull('article_number')->with('documents')
            ->get();
        }
        return $scpCartData;
    }

    public function documents() {
        return $this->hasMany('App\ArticleFile', 'article_number', 'article_number');
    }

    public  function scpItems() {
        return $this->hasMany('App\ScpItem', 'scp_cart_id', 'id');
    }

    public static function getSCP() {
        $scpCartData = ScpCart::get();
        return $scpCartData;
    }

}
