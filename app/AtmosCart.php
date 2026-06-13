<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AtmosCart extends Model {

    public static function cartData() {
        $atmosCartData = AtmosCart::where('user_id', auth()->user()->id)
                ->whereNull('quotation_no')
                ->get();
        return $atmosCartData;
    }

    public static function cartDataByQuotation($ids) {
        $atmosCartData = AtmosCart::whereIn('id', $ids)
                ->get();
        return $atmosCartData;
    }

    public static function cartDataByUserId($id,$article_number = false) {

        if($article_number == false){
        $atmosCartData = AtmosCart::where('user_id', $id)->whereNotNull('article_number')->with('documents')
            ->get();
        }else{
            $atmosCartData = AtmosCart::where('user_id', $id)->where('article_number',$article_number)->whereNotNull('article_number')->with('documents')
            ->get();
        }
        return $atmosCartData;
    }

    public function atmos_materials_code(){
        return $this->hasMany('App\AtmosMaterial', 'code', 'material_id');
    }

    public function atmosItems() {
        return $this->hasMany('App\AtmosItem', 'atmos_cart_id', 'id');
    }

    public function documents() {
        return $this->hasMany('App\ArticleFile', 'article_number', 'article_number');
    }
}
