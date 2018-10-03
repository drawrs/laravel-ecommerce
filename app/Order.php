<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    //
    protected $fillable = ['user_id', 'payment_proof', 'order_status'];

    public function products(){
        return $this->hasMany('App\ProductOrder', 'order_id', 'id');
    }
}
