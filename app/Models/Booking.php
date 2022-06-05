<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'booking_uid',
        'user_id',
        'price',
        'location_id',
        'warehouses_used'
    ];

}
