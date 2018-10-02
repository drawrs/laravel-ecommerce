<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ShoppingCart extends Model
{
    //
    protected $fillable = ['user_id', 'product_id', 'qty'];
    public function product(){
        return $this->belongsTo('App\Product', 'product_id', 'id');
    }
}
