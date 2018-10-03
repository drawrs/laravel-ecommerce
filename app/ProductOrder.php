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
                            'rating'];
}
