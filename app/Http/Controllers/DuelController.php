<?php

// app/Http/Controllers/DuelController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Duel;

class DuelController extends Controller {
    public function index() {
        try {
            $duels = Duel::all();
            return response()->json([
                'message' => 'Duels retrieved successfully',
                'data' => $duels
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
            $duel = Duel::create($request->all());
            return response()->json([
                'message' => 'Duel created successfully',
                'data' => $duel
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
            $duel = Duel::findOrFail($id);
            return response()->json([
                'message' => 'Duel retrieved successfully',
                'duel' => $duel
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
            $duel = Duel::findOrFail($id);
            $duel->update($request->all());
            return response()->json([
                'message' => 'Duel updated successfully',
                'data' => $duel
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
            $duel = Duel::findOrFail($id);
            $duel->delete();
            return response()->json([
                'message' => 'Duel deleted successfully'
            ], 204);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Error',
                'error' => $th->getMessage()
            ], 500);
        }
    }
}
