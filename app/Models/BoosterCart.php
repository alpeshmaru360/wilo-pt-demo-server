<?php

namespace App\Models;

use App\ScpCart;
use Illuminate\Database\Eloquent\Model;

class BoosterCart extends Model {

    protected $table = 'booster_carts';

    public static function cartData() {
        $boosterCartData = BoosterCart::with('boosterCpData')->where('user_id', auth()->user()->id)
            ->whereNull('quotation_no')
            ->get();
        return $boosterCartData;
    }

    public static function cartDataByQuotation($ids) {
        $boosterCartData = BoosterCart::whereIn('id', $ids)
            ->get();
        return $boosterCartData;
    }

    public static function cartDataByUserId($id,$article_number = false) {

        if($article_number == false){
        $boosterCartData = BoosterCart::where('user_id', $id)->whereNotNull('article_number')->with('documents')
            ->get();
        }else{
            $boosterCartData = BoosterCart::where('user_id', $id)->where('article_number',$article_number)->whereNotNull('article_number')->with('documents')
            ->get();
        }
        return $boosterCartData;
    }

    public function boosterItems() {
        return $this->hasMany('App\Models\BoosterItem', 'booster_cart_id', 'id');
    }

    public function documents() {
        return $this->hasMany('App\ArticleFile', 'article_number', 'article_number');
    }

    public function boosterCpData() {
        return $this->hasMany('App\ControlPanel', 'id', 'cp_id');
    }
}
