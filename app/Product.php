<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    //
    //protected $appends = ['product_cover'];
    public function productPhotos(){
        return $this->hasMany('App\ProductPhoto', 'product_id', 'id');
    }
    public function category(){
        return $this->belongsTo('App\Category', 'category_id', 'id');
    }
}
