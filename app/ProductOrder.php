<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProductOrder extends Model
{
    //
    protected $fillable = ['category_id', 
                            'user_id', 
                            'order_id', 
                            'product_code', 
                            'title', 
                            'price', 
                            'description', 
                            'qty_order', 
                            'cover', 
                            'rating'];

    public function productPhotos(){
        return $this->hasMany('App\ProductPhoto', 'product_id', 'id');
    }
    public function category(){
        return $this->belongsTo('App\Category', 'category_id', 'id');
    }
}
