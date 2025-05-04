<?php

namespace Modules\Excon\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

use Modules\Excon\Models\Engagement;
use Modules\Excon\Models\Identifier;

use Modules\Excon\Http\Requests\AckEngagementRequest;



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

         // The forCurrentUser scope relies on data filled in by the acknowlegeForUser method of Engagement.
        $engagements = Engagement::forCurrentUser()
            ->ofType("surface to surface")
            ->get();

        $engagements = $engagements->filter(function ($item){
            return $item->isValid;
        })
        ->map(function ($item){
            return $item->description_for_dis();
        });

        logger()->info("Provided " . $engagements->count() . " engagements data to system: " . auth()->user()->nom);
        return $engagements->toArray();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function acknowledge(AckEngagementRequest $request)
    {
        $validated = $request->validated();
        $engagement = Engagement::where("entity_number", $validated["engagement"])->first();

        if ($engagement != null)
        {
            // The acknowlegeForUser fills data used by the forCurrentUser scope of Engagement.
            $engagement->acknowlegeForUser(auth()->user());

            logger()->info("Ackownledge received for " . $validated["engagement"] . " from: " . auth()->user()->nom);
        }
        return response()->json([]);
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
