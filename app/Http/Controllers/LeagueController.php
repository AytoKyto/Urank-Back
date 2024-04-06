<?php
// app/Http/Controllers/LeagueController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\League;
use App\Models\LeagueUser;
use App\Models\Duel;
use Illuminate\Support\Facades\Auth;
use App\Services\GetDataService;

class LeagueController extends Controller
{
    protected $getDataService;

    public function __construct(GetDataService $getDataService)
    {
        $this->getDataService = $getDataService;
    }

    public function index()
    {
        try {
            $id = Auth::id();
            $leagues = League::where('admin_user_id', $id)->get();

            return response()->json([
                'message' => 'Leagues retrieved successfully',
                'data' => $leagues
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Error',
                'error' => $th->getMessage()
            ], 500);
        }
    }


    public function store(Request $request)
    {
        try {
            $id = Auth::id();
            DB::commit();

             $leagueData = League::create([
                'admin_user_id' => $id,
                'name' => $request['name'],
                'icon' => $request['icon']
            ]);

            LeagueUser::create([
                'user_id' => $id,
                'league_id' => $leagueData['id'],
                'elo' => 1000,
                'type' => 2
            ]);

            return response()->json([
                'message' => 'League created successfully'
            ], 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'message' => 'Error',
                'error' => $th->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $userId = Auth::id();

            $league_data = League::where('id', $id)
                ->get();

            $league = $this->getDataService->leagueUser($id, 10);
            $duel_data = $this->getDataService->duelCardInLeague($userId, $id, 4);

            $global_stats = DB::table('view_league_stats')
                ->where('user_id', $userId)
                ->where('league_id', $id)
                ->first();

            return response()->json([
                'message' => 'League retrieved successfully',
                'league_data' => $league_data,
                'user_league_data' => $league,
                'duel_data' => $duel_data,
                'global_stats' => $global_stats
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Error',
                'error' => $th->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $league = League::findOrFail($id);
            $league->update($request->all());
            return response()->json([
                'message' => 'League updated successfully',
                'data' => $league
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Error',
                'error' => $th->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $league = League::findOrFail($id);
            $league->delete();
            LeagueUser::where('league_id', $id)->delete();
            return response()->json([
                'message' => 'League deleted successfully'
            ], 204);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Error',
                'error' => $th->getMessage()
            ], 500);
        }
    }
}
