<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ShippingAddress extends Model
{
    //
    protected $table = 'shipping_addresses';
    protected $fillable = ['title', 
                            'city', 
                            'province', 
                            'address',
                            'zip_code', 
                            'is_main_address'];
}
