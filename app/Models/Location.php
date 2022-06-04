<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    use HasFactory;

    public $timestamps = false;

    public function warehouses()
    {
        return $this->hasMany(Warehouse::class);
    }

    public function blocks()
    {
        return $this->hasMany(Block::class);
    }
}
