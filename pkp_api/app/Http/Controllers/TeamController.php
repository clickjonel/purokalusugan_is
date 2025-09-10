<?php

namespace App\Http\Controllers;

use App\Http\Requests\Team\SaveMemberRequest;
use App\Http\Requests\Team\SaveScopeRequest;
use App\Models\Team;
use App\Models\TeamMember;
use App\Models\TeamScope;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TeamController extends Controller
{
    public function list():JsonResponse
    {
        $teams = Team::with(['members'])->withCount(['scopes', 'members'])->get();

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
        $team = Team::with([
            'scopes',
            'scopes.barangay.province',
            'scopes.barangay.municipality',
            'scopes.team','members.team',
            'members.hrh'
            ])
            ->find($request->team_id);

        return response()->json([
            'team' => $team
        ]);
    }
    
    public function saveMember(SaveMemberRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $teamMember = TeamMember::create($validated);

        return response()->json([
            'message' => 'Successfully Added Member'
        ]);
    }

    public function saveScope(SaveScopeRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $teamMember = TeamScope::create($validated);

        return response()->json([
            'message' => 'Successfully Added Member'
        ]);
    }

    public function removeMember(Request $request): JsonResponse
    {
        $teamMember = TeamMember::find($request->team_member_id)->delete();

        return response()->json([
            'message' => 'Successfully Removed Member'
        ]);
    }

    public function removeScope(Request $request): JsonResponse
    {
        $teamScope = TeamScope::find($request->team_scope_id)->delete();

        return response()->json([
            'message' => 'Successfully Removed Member'
        ]);
    }
    

}
