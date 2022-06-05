<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Warehouse extends Model
{
    use HasFactory;

    public $timestamps = false;

    public function location()
    {
        return $this->belongsTo(Location::class, 'location_id');
    }

    public function blocks()
    {
        return $this->hasMany(Block::class);
    }

    public function availableBlocks()
    {
        return $this->blocks()->where('filled', '=', false)->get();
    }
}
