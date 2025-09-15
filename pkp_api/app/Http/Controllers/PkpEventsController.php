<?php

namespace App\Http\Controllers;

use App\Http\Requests\Event\CreateEventRequest;
use App\Models\Pkp_events;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PkpEventsController extends Controller
{
    public function createEvent(Request $request):JsonResponse
    {
        $validatedData=$request->validate([            
            'event_type' => 'required|integer',
            'event_date' => 'required|date',
            'event_venue' => 'string',     
            'event_budget' => 'numeric',     
            'event_actual_budget' => 'numeric',     
            'event_fund_source' => 'string',     
            'event_proponent' => 'string',     
            'event_partner' => 'string',     
            'event_scope' => 'string',     
            'is_pk_site' => 'integer',     
        ]);
        $data=Pkp_events::create($validatedData);
         return response()->json([
            'message' => 'Created successfully',
            'data'=>$data
        ], 201);
    }
    public function list(): JsonResponse
    {
        $list = Pkp_events::with(['programs','barangays'])->get();
        return response()->json([
            'data' => $list
        ], 200);
    }

    public function updateEvent(Request $request): JsonResponse
    {
        $validatedData = $request->validate([
            'event_id' => 'required|integer|exists:pkp_events,event_id',
            'event_date' => 'required|date',
            'event_venue' => 'string',     
            'event_budget' => 'numeric',     
            'event_actual_budget' => 'numeric',     
            'event_fund_source' => 'string',     
            'event_proponent' => 'string',     
            'event_partner' => 'string',     
            'event_scope' => 'string',     
            'is_pk_site' => 'integer'         
        ]);
        $data = Pkp_events::find($validatedData['event_id'])
            ->update($validatedData);
        return response()->json([
            'message' => 'Updated successfully',
            'data' => $data
        ], 200);
    }  
    public function deleteEvent(Request $request): JsonResponse
    {
        $validatedData = $request->validate([
            'event_id' => 'required|integer|exists:pkp_events,event_id',            
        ]);
        Pkp_events::findOrFail($validatedData['event_id'])->delete();        
        return response()->json([
            'message' => 'Deleted successfully'
        ], 200);
    }

    public function saveEvent(CreateEventRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $event = Pkp_events::create($validated);

        $event->programs()->sync($validated['programs']);
        $event->barangays()->sync($validated['barangays']);

        return response()->json([
            'message' => 'Created Successfully'
        ], 200);
    }
}
