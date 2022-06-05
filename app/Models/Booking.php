<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Booking.
 *
 * @OA\Schema(
 *     description="Booking model",
 *     title="Booking model",
 *     @OA\Xml(
 *         name="Booking"
 *     )
 * )
 */
class Booking extends Model
{
    use HasFactory;

    public $timestamps = false;

    /**
     * @OA\Property(
     *     title="Booking UID",
     *     type="string"
     * )
     *
     * @var BookingUID
     */
    private $booking_uid;

    /**
     * @OA\Property(
     *     title="User ID",
     *     type="integer"
     * )
     *
     * @var UserID
     */
    private $user_id;

    /**
     * @OA\Property(
     *     title="Price",
     *     type="decimal"
     * )
     *
     * @var Price
     */
    private $price;

    /**
     * @OA\Property(
     *     title="Location ID",
     *     type="integer"
     * )
     *
     * @var LocationID
     */
    private $location_id;

    /**
     * @OA\Property(
     *     title="Warehouses Used",
     *     type="array",
     *     @OA\items(type="integer")
     * )
     *
     * @var WarehousesUsed
     */
    private $warehouses_used;

    protected $fillable = [
        'booking_uid',
        'user_id',
        'price',
        'location_id',
        'warehouses_used'
    ];
}
