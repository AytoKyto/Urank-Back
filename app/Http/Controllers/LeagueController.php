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
            $leagues = League::all();
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
            $league = League::create($request->all());
            return response()->json([
                'message' => 'League created successfully',
                'data' => $league
            ], 201);
        } catch (\Throwable $th) {
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

            $league = $this->getDataService->leagueCard($userId, 3);
            $duel_data = $this->getDataService->duelCard($userId, 4);

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
