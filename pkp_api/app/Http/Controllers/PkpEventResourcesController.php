<?php

namespace App\Http\Controllers;

use App\Models\Pkp_event_resources;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PkpEventResourcesController extends Controller
{
    public function createEventResource(Request $request): JsonResponse
    {
        $validatedData = $request->validate([
            'name' => 'required|string',
            'type' => 'integer',
            'beneficiary_count' => 'numeric',
            'amount' => 'numeric',
            'event_id' => 'integer'
        ]);
        $program = Pkp_event_resources::create($validatedData);
        return response()->json([
            'message' => 'Created successfully',
            'data' => $program
        ], 201);
    }
    public function getEventResources(): JsonResponse
    {
        $data = Pkp_event_resources::all();
        return response()->json([
            'message' => 'Data retrieved successfully',
            'data' => $data
        ], 200);
    }

    public function getEventResourcesForAnEvent(Request $request): JsonResponse
    {
        $validatedData = $request->validate([
            'event_id' => 'required|integer|exists:pkp_event_resources,event_id',
        ]);
        $data = Pkp_event_resources::where('event_id', $validatedData['event_id'])->get();
        return response()->json([
            'message' => 'Data retrieved successfully',
            'data' => $data
        ], 200);
    }
    public function deleteEventResources(Request $request): JsonResponse
    {
        $validatedData = $request->validate([
            'event_resource_id' => 'required|integer|exists:pkp_event_resources,event_resource_id',
        ]);
        Pkp_event_resources::findOrFail($validatedData['event_resource_id'])->delete();
        return response()->json([
            'message' => 'Deleted successfully'
        ], 200);
    }
}
