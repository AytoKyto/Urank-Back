<?php
// app/Http/Controllers/LeagueController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\League;
use App\Models\LeagueUser;
use App\Models\Duel;

class LeagueController extends Controller {
    public function index() {
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


    public function store(Request $request) {
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

    public function show($id) {
        try {
            $league = League::findOrFail($id);

            $user_league_data = LeagueUser::where('league_id', $id)
                ->orderBy('elo', 'desc')
                ->get();

            $duel_data = Duel::where('league_id', $id)
                ->orderBy('created_at', 'desc')
                ->get();

            $data[] = [
                'league' => $league,
                'user_league_data' => $user_league_data,
                'duel_data' => $duel_data
            ];

            return response()->json([
                'message' => 'League retrieved successfully',
                'nbr' => count($data),
                'data' => $data,
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Error',
                'error' => $th->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $id) {
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

    public function destroy($id) {
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
