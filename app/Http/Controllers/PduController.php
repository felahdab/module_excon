<?php

namespace Modules\Excon\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Carbon\Carbon;

use Modules\Excon\Http\Requests\PositionRequest;
use Modules\Excon\Models\Position;
use Modules\Excon\Models\Identifier;

/**
 * @tags Module Excon: Pdu
 */
class PduController extends Controller
{
    /**
     * Transmit a newly received Pdu
     */
    public function store(Request $request)
    {
        if ($request->has("pduType") && $request->input("pduType") == 1) # On sélectionne uniquement les messages EntityState
        {
            $TN = $request->input("marking");
            $identifier = Identifier::firstOrCreate(["source" => "LDT", "identifier" => $TN]);

            $real_world_location = $request->input('real_world_location');
            $real_world_course = $request->input('real_world_course');
            $real_world_speed = $request->input('real_world_speed');

            $position = new Position();
            
            $position->latitude = $real_world_location[0];
            $position->longitude = $real_world_location[1];
            $position->course = $real_world_course;
            $position->speed = $real_world_speed;

            $position->identifier_id = $identifier->id;
            $position->timestamp = Carbon::now();

            $position->save();
        }
        return response()->json(["status" => "success"]);
    }
}
