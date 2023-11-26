<?php

// app/Http/Controllers/LeagueUserController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LeagueUser;

class LeagueUserController extends Controller {
    public function index() {
        try {
            $leagueUsers = LeagueUser::all();
            return response()->json([
                'message' => 'League users retrieved successfully',
                'data' => $leagueUsers
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
            $leagueUser = LeagueUser::create($request->all());
            return response()->json([
                'message' => 'League user created successfully',
                'data' => $leagueUser
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
            $leagueUser = LeagueUser::findOrFail($id);
            return response()->json([
                'message' => 'League user retrieved successfully',
                'leagueUser' => $leagueUser
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
            $leagueUser = LeagueUser::findOrFail($id);
            $leagueUser->update($request->all());
            return response()->json([
                'message' => 'League user updated successfully',
                'data' => $leagueUser
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
            $leagueUser = LeagueUser::findOrFail($id);
            $leagueUser->delete();
            return response()->json([
                'message' => 'League user deleted successfully'
            ], 204);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Error',
                'error' => $th->getMessage()
            ], 500);
        }
    }
}
