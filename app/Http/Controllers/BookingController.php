<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Location;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class BookingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return Booking::where('user_id', $request->user()->id)->get();
    }

    /**
     * @OA\Get (
     *     path="/bookings/create",
     *     tags={"bookings"},
     *     summary="Get booking form data",
     *     security={ {"apiAuth": {} }},
     *     @OA\Response(
     *         response=200,
     *         description="OK",
     *         @OA\JsonContent(
     *             @OA\Examples(
     *                  example="result",
     *                  value={"location_id": 2, "location": "Портленд (Орегон)", "warehouses_info": {
     *                      {"warehouse_id": 5, "temperature": "-20.97","available_blocks": 5}}},
     *                  summary="An result object.")
     *         )
     *     )
     * )
     */
    public function create()
    {
        $data = [];
        foreach (Location::all() as $location) {
            array_push($data, [
               'location_id' => $location->id,
               'location' => $location->name,
               'warehouses_info' => $location->getWarehousesInfo()
            ]);
        }
        return response($data);
    }

    /**
     * @OA\Post(
     *     path="/bookings/calculate",
     *     tags={"bookings"},
     *     summary="Calculate required blocks",
     *     security={ {"apiAuth": {} }},
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="days",
     *                     type="integer"
     *                 ),
     *                 @OA\Property(
     *                     property="required_space",
     *                     type="integer"
     *                 ),
     *                 @OA\Property(
     *                     property="location_id",
     *                     type="integer"
     *                 ),
     *                 @OA\Property(
     *                     property="temperature",
     *                     type="decimal"
     *                 ),
     *                 example={"days": 22, "required_space": 21, "location_id": 2, "temperature": -2.2}
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="OK",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="booking_uid",
     *                 type="string",
     *             ),
     *             @OA\Property(
     *                 property="price",
     *                 type="decimal",
     *             ),
     *             @OA\Property(
     *                 property="warehouses_used",
     *                 type="array",
     *                 @OA\Items(type="integer")
     *             ),
     *             @OA\Examples(
     *                  example="result",
     *                  value={"required_blocks": 11, "price": 484, "warehouses_used": {5, 8}},
     *                  summary="An result object."),
     *         )
     *     )
     * )
     */
    public function calculateSpace(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'days' => 'required|numeric|max:24',
            'required_space' => 'required|numeric',
            'location_id' => 'required|numeric',
            'temperature' => 'required|numeric'
        ]);
        if ($validator->fails())
            return response($validator->errors());

        $requiredBlocks = ceil($request->required_space / 2);
        $location = Location::find($request->location_id);
        $availableBlocks = 0;
        $warehouseIds = [];
        foreach ($location->warehouses as $warehouse) {
            if ($warehouse->temperature < 0
            && $warehouse->temperature - 2 <= $request->temperature
            && $warehouse->temperature + 2 != 0
            && $warehouse->temperature + 2 <= $request->temperature) {
                $availableBlocks += count($warehouse->availableBlocks());
                if(!in_array($warehouse->id, $warehouseIds)){
                    array_push($warehouseIds, $warehouse->id);
                }
                if ($availableBlocks >= $requiredBlocks) break;
            }
        }
        if ($availableBlocks >= $requiredBlocks) {
            return response([
                'required_blocks' => $requiredBlocks,
                'price' => $this->calculatePrice($requiredBlocks, $request->days),
                'warehouses_used' => $warehouseIds,
            ]);
        }
        else {
            return response([
               'error' => "Not enough free blocks at location $location->name"
            ]);
        }
    }

    private function calculatePrice($requiredBlocks, $days)
    {
        return $requiredBlocks * $days * 2;
    }

    /**
     * @OA\Post(
     *     path="/bookings",
     *     tags={"bookings"},
     *     summary="Adds a new booking",
     *     security={ {"apiAuth": {} }},
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="location_id",
     *                     type="integer"
     *                 ),
     *                 @OA\Property(
     *                     property="required_blocks",
     *                     type="integer"
     *                 ),
     *                 @OA\Property(
     *                     property="price",
     *                     type="decimal"
     *                 ),
     *                 @OA\Property(
     *                     property="warehouses_used",
     *                     type="array",
     *                     @OA\Items(type="integer")
     *                 ),
     *                 example={"location_id": 2, "required_blocks": 1, "price": 484, "warehouses_used": {5,8}}
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="OK",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="booking_uid",
     *                 type="string",
     *             ),
     *             @OA\Examples(example="result", value={"booking_uid": "tUPzqV2F5zZ8"}, summary="An result object."),
     *         )
     *     )
     * )
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'location_id' => 'required|numeric',
            'required_blocks' => 'required|numeric',
            'price' => 'required|numeric',
            'warehouses_used' => 'required'
        ]);
        if ($validator->fails())
            return response($validator->errors());

        $booking = Booking::create([
            'booking_uid' => Str::random(12),
            'user_id' => $request->user()->id,
            'price' => $request->price,
            'location_id' => $request->location_id,
            'warehouses_used' => json_encode($request->warehouses_used),
        ]);

        return response(['booking_uid' => $booking->booking_uid]);
    }

    /**
     * @OA\Get(
     *     path="/bookings/{id}",
     *     tags={"bookings"},
     *     summary="Get the booking by id",
     *     security={ {"apiAuth": {} }},
     *     @OA\Parameter(
     *          name="id",
     *          in="path",
     *          required=true,
     *          description="Booking ID",
     *          @OA\Schema(
     *              type="string"
     *          ),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="OK",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 ref="#/components/schemas/Booking",
     *             ),
     *             @OA\Examples(example="result",
     *                  value={"booking_uid": "tUPzqV2F5zZ8", "user_id": 1, "price": 44,00, "location_id": 1, "warehouses_used": {1,3}},
     *                  summary="An result object."),
     *         )
     *     )
     * )
     */
    public function show($id)
    {
        return response(['error' => "Endpoint is not implemented"], 404);
    }

//    public function edit(Request $request)
//    {
//        return response(['error' => 'Endpoint is not implemented'], 404);
//    }

    /**
     * @OA\Put(
     *     path="/bookings/{id}",
     *     tags={"bookings"},
     *     summary="Update the booking by id",
     *     security={ {"apiAuth": {} }},
     *     @OA\Parameter(
     *          name="id",
     *          in="path",
     *          required=true,
     *          description="Booking ID",
     *          @OA\Schema(
     *              type="string"
     *          ),
     *     ),
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="value_to_update",
     *                     type="string"
     *                 ),
     *                 example={"value_to_update": "test"}
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="OK",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="booking_uid",
     *                 ref="#/components/schemas/Booking",
     *             ),
     *             @OA\Examples(example="result",
     *                  value={"booking_uid": "tUPzqV2F5zZ8", "user_id": 1, "price": 44,00, "location_id": 1, "warehouses_used": {1,3}},
     *                  summary="An result object."),
     *         )
     *     )
     * )
     */
    public function update(Request $request, $id)
    {
        return response(['error' => 'Endpoint is not implemented'], 404);
    }

    /**
     * @OA\Delete(
     *     path="/bookings/{id}",
     *     tags={"bookings"},
     *     summary="Delete the booking by id",
     *     security={ {"apiAuth": {} }},
     *     @OA\Parameter(
     *          name="id",
     *          in="path",
     *          required=true,
     *          description="Booking ID",
     *          @OA\Schema(
     *              type="string"
     *          ),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="OK",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="booking_uid",
     *                 ref="#/components/schemas/Booking",
     *             ),
     *             @OA\Examples(example="result",
     *                  value={"booking_uid": "tUPzqV2F5zZ8", "user_id": 1, "price": 44,00, "location_id": 1, "warehouses_used": {1,3}},
     *                  summary="An result object."),
     *         )
     *     )
     * )
     */
    public function destroy($id)
    {
        return response(['error' => 'Endpoint is not implemented'], 404);
    }
}
