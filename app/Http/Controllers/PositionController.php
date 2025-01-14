<?php

namespace Modules\Excon\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Modules\Excon\Http\Requests\PositionRequest;
use Modules\Excon\Models\Position;
use Modules\Excon\Models\Identifier;

/**
 * @tags Module Excon: Position
 */
class PositionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //

        return response()->json([]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PositionRequest $request)
    {
        $validated = $request->validated();

        $source = $validated["source"];
        $identifier = $validated["identifier"];
        $ident = Identifier::where("source", $source)
            ->where("identifier", $identifier)
            ->firstOrFail();

        $validated["identifier_id"] = $ident->id;

        $p=Position::create($validated);

        return response()->json($p);
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
