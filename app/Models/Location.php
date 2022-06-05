<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    use HasFactory;

    public $timestamps = false;

    public function getWarehousesInfo()
    {
        $data = [];
        foreach ($this->warehouses as $warehouse) {
            array_push($data, [
                'warehouse_id' => $warehouse->id,
                'temperature' => $warehouse->temperature,
                'available_blocks' => count($warehouse->availableBlocks())
            ]);
        }
        return $data;
    }

    public function warehouses()
    {
        return $this->hasMany(Warehouse::class);
    }

    public function blocks()
    {
        return $this->hasMany(Block::class);
    }
}
