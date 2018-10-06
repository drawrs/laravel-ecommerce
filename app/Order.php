<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    //
    protected $fillable = ['user_id', 'order_code', 'payment_proof', 'order_status', 'shipping_address'];

    public function products(){
        return $this->hasMany('App\ProductOrder', 'order_id', 'id');
    }
}
