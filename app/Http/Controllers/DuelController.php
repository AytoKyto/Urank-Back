<?php

// app/Http/Controllers/DuelController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Duel;
use App\Models\DuelUser;
use App\Models\LeagueUser;
use App\Models\CoinUser;

class DuelController extends Controller
{

    public function index()
    {
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

    public function store(Request $request)
    {
        function calculateElo($currentElo, $opponentElo, $score, $K = 35)
        {
            $expectedScore = 1 / (1 + pow(10, ($opponentElo - $currentElo) / 400));
            $newElo = $K * ($score - $expectedScore);
            return round($newElo);
        }

        $validatedData = $request->validate([
            'league_id' => 'required',
            'author_id' => 'required',
            'is_null' => 'required|boolean',
            'description' => 'nullable',
            'win_user' => 'required|array',
            'lose_user' => 'required|array',
        ]);

        try {
            $duel = Duel::create([
                'league_id' => $validatedData['league_id'],
                'author_id' => $validatedData['author_id'],
                'description' => $validatedData['description']
            ]);

            $league_user_count = LeagueUser::where('league_id', $validatedData['league_id'])
                ->where('type', 0)
                ->count();

            $winner = $request->win_user;
            $loser = $request->lose_user;

            $winner_elo_moyen = 0;
            $loser_elo_moyen = 0;

            foreach ($winner as $key => $value) {
                $winner_elo_moyen += $value['elo'];
            }

            foreach ($loser as $key => $value) {
                $loser_elo_moyen += $value['elo'];
            }

            $winner_elo_moyen = $winner_elo_moyen / count($winner);
            $loser_elo_moyen = $loser_elo_moyen / count($loser);

            // Calculer le nouveau elo
            $winner_elo = calculateElo($winner_elo_moyen, $loser_elo_moyen, $validatedData['is_null'] ? 0.5 : 1); // 1 pour une victoire, 0.5 pour un match nul, 0 pour une défaite
            $loser_elo = calculateElo($loser_elo_moyen, $winner_elo_moyen, $validatedData['is_null'] ? 0.5 : 0); // 1 pour une victoire, 0.5 pour un match nul, 0 pour une défaite


            foreach ($winner as $key => $value) {
                DuelUser::create([
                    'user_id' => $value['id'],
                    'duel_id' => $duel['id'],
                    'league_id' => $validatedData['league_id'],
                    'league_user_elo_init' => $value['elo'],
                    'league_user_elo_add' => $winner_elo,
                    'status' => $request->is_null ? 0.5 : 1
                ]);

                $league_user = LeagueUser::where('user_id', $value['id'])
                    ->where('league_id', $validatedData['league_id'])
                    ->first();

                $league_user->elo += $winner_elo;
                $league_user->save();

                CoinUser::create([
                    'user_id' => $value['id'],
                    'value' => round($winner_elo / 3 * ($league_user_count / 2)),
                ]);

                // Update user coin
                $user = User::findOrFail($value['id']);
                $user->coins += round($winner_elo / 3 * ($league_user_count / 2));
                $user->save();
            }

            foreach ($loser as $key => $value) {
                DuelUser::create([
                    'user_id' => $value['id'],
                    'duel_id' => $duel['id'],
                    'league_id' => $validatedData['league_id'],
                    'league_user_elo_init' => $value['elo'],
                    'league_user_elo_add' => $loser_elo,
                    'status' => $request->is_null ? 0.5 : 0
                ]);

                $league_user = LeagueUser::where('user_id', $value['id'])
                    ->where('league_id', $validatedData['league_id'])
                    ->first();

                $league_user->elo += $loser_elo;
                $league_user->save();
            }


            return response()->json([
                'message' => 'Duel created successfully',
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

    public function update(Request $request, $id)
    {
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

    public function destroy($id)
    {
        try {
            $duel_user = DuelUser::where('duel_id', $id)
                ->get();

            foreach ($duel_user as $key => $value) {
                DuelUser::destroy($value['id']);

                $league_user = LeagueUser::findOrFail($value['user_id']);
                $league_user->elo -= $value['league_user_elo_add'];
                $league_user->save();

                CoinUser::findOrFail($value['user_id'])->delete();

                if ($value['status'] == 1) {
                    // Update user coin
                    $user = User::findOrFail($value['id']);
                    $user->coins -= 5;
                    $user->save();
                }
            }


            return response()->json([
                'message' => 'Duel created successfully',
            ], 201);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Error',
                'error' => $th->getMessage()
            ], 500);
        }
    }
}
