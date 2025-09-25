<?php

namespace App\Http\Controllers;

use App\Http\Requests\Event\CreateEventRequest;
use App\Http\Requests\Event\PopulateEventRequest;
use App\Http\Requests\Event\UpdateEventDetails;
use App\Models\Pkp_barangay;
use App\Models\Pkp_events;
use App\Models\Pkp_indicator;
use App\Models\Pkp_indicator_values;
use App\Models\Programs;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
            'data' => $list,
            'total' => $list->count()
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

        $event->programs()->attach($validated['programs']);
        $event->barangays()->attach($validated['barangays']);

        return response()->json([
            'message' => 'Created Successfully'
        ], 200);
    }

    public function fetchEvent(Request $request):JsonResponse
    {
        $event = Pkp_events::with(['programs.indicators.disaggregations','barangays','values.indicatorDisaggregation.indicator','values.barangay'])->find($request->event_id);
        return response()->json([
            'event' => $event
        ], 200);
    }

    public function  updateEventDetails(UpdateEventDetails $request):JsonResponse
    {
        $validated = $request->validated();

        $event = Pkp_events::findOrFail($validated['event_id']);
        $event->update($validated);
        
        // Sync barangays - extract IDs from the barangay objects
        // if (isset($validated['barangays'])) {
        //     $current_barangays = $event->barangays->pluck('barangay_id');
        //     $event->barangays()->sync($validated['barangays']);
        // }
        
        // // Sync programs if you also have programs relation
        // if (isset($validated['programs'])) {
        //     $event->programs()->sync($validated['programs']);
        // }
        

        return response()->json([
            'message' => 'Updated Successfully'
        ]);
    }

    public function populateEvent(PopulateEventRequest $request){

        $validated = $request->validated();

        // Start a database transaction
        DB::beginTransaction();

        try {
            // Get the event
            $event = Pkp_events::findOrFail($validated['event_id']);

            // Loop through barangays
            foreach ($validated['barangays'] as $barangayData) {
                $barangay = Pkp_barangay::findOrFail($barangayData['barangay_id']);

                // Loop through programs
                foreach ($barangayData['programs'] as $programData) {
                    $program = Programs::findOrFail($programData['program_id']);

                    // Loop through indicators
                    foreach ($programData['indicators'] as $indicatorData) {
                        // Loop through disaggregations
                        foreach ($indicatorData['disaggregations'] as $disaggregationData) {
                            // if(isset($disaggregationData['value']) && $disaggregationData['value'] !== null && $disaggregationData['value'] !== ''){
                                Pkp_indicator_values::create([
                                    'event_id' => $event->event_id,
                                    'indicator_disaggregation_id' => $disaggregationData['indicator_disaggregation_id'],
                                    'value' => isset($disaggregationData['value']) ? $disaggregationData['value'] : 0,
                                    'remarks' => $disaggregationData['remarks'] ?? null,
                                    'barangay_id' => $barangay->barangay_id,
                                ]); 
                            //}
                        }
                    }
                }
            }

            $event->update([
                'is_populated' => true
            ]);

            // Commit the transaction if everything is successful
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Data saved successfully!',
            ]);

        } catch (\Exception $e) {
            // Roll back the transaction if an error occurs
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Failed to save data: ' . $e->getMessage(),
            ], 500);
        }

    }


}
