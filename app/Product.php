<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    //
    public function productPhotos(){
        return $this->hasMany('App\ProductPhoto', 'product_id', 'id');
    }
}
