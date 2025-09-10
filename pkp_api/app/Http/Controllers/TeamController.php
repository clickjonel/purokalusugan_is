<?php

namespace App\Http\Controllers;

use App\Models\Team;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TeamController extends Controller
{
    public function list():JsonResponse
    {
        $teams = Team::get();

        return response()->json(['teams' => $teams]);
    }

    public function update(Request $request):JsonResponse
    {
        $team = Team::find($request->team_id)->update([
            'team_name' => $request->team_name 
        ]);

        return response()->json(['message' => 'Team Updated Successfully']);
    }

    public function create(Request $request):JsonResponse
    {
        $team = Team::create([
            'team_name' => $request->team_name 
        ]);

        return response()->json(['message' => 'Team Created Successfully']);
    }

    public function delete(Request $request):JsonResponse
    {
        $team = Team::find($request->team_id)->delete();

        return response()->json(['message' => 'Team Deleted Successfully']);
    }

    public function getTeam(Request $request){
        $team = Team::find($request->team_id);

        return response()->json([
            'team' => $team
        ]);
    }

    

}
