<?php

namespace Modules\Excon\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Modules\Excon\Models\Engagement;

/**
 * @tags Module Excon: Engagement
 */
class EngagementController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
       /**
         * Là, il faut:
         * - pour chaque engagement
         * - récupérer l'unité qui tire
         * - récupérer (voire extrapoler) sa position au moment du tir depuis la table des positions
         * - récupérer le weapon utilisé et notamment ses paramètres DIS
         * - enrichir chaque engagement avec la position et les paramètres DIS
         */ 
        $engagements = Engagement::all();
        $eng_avec_position_et_dis = $engagements->map(function ($item){
            $timestamp = $item->timestamp;
            $unit = $item->unit;
            [$latitude, $longitude] = $unit->extrapolatePositionForTimestamp($timestamp);

            $weapon = $item->weapon;

            return [
                "timestamp" => $timestamp,
                "latitude" => $latitude,
                "longitude" => $longitude,
                "kind" => $weapon->kind,
                "domain" => $weapon->domain,
                "country" => $weapon->country,
                "category" => $weapon->category,
                "subcategory" => $weapon->subcategory,
                "specific" => $weapon->specific,
                "extra" => $weapon->extra,
            ];
        });
        return $eng_avec_position_et_dis;
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //

        return response()->json([]);
    }

    /**
     * Show the specified resource.
     */
    public function show($id)
    {
        //

        return response()->json([]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        //

        return response()->json([]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        //

        return response()->json([]);
    }
}
