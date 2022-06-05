<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Block extends Model
{
    use HasFactory;

    public $timestamps = false;

    public function size()
    {
        return $this->width * $this->height * $this->length;
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }
}
