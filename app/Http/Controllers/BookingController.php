<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Location;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
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
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
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

    public function calculateSpace(Request $request)
    {
        $request->validate([
            'days' => 'required|numeric|max:24',
            'required_space' => 'required|numeric',
            'location_id' => 'required|numeric',
            'temperature' => 'required|numeric'
        ]);
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
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'location_id' => 'required|numeric',
            'required_blocks' => 'required|numeric',
            'price' => 'required|numeric',
            'warehouses_used' => 'required'
        ]);
        $id = $request->user()->id;
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
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
