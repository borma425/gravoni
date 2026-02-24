<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Governorate extends Model
{
    protected $fillable = [
        'name',
        'shipping_fee',
        'mylerz_zone_code',
    ];

    protected $casts = [
        'shipping_fee' => 'decimal:2',
    ];
}
