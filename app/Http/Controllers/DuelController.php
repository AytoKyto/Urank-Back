<?php

// app/Http/Controllers/DuelController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Duel;
use App\Models\DuelUser;
use App\Models\LeagueUser;
use App\Models\CoinUser;
use Illuminate\Support\Facades\DB;
use App\Services\RankingService;
use Illuminate\Support\Facades\Auth;

class DuelController extends Controller
{
    protected $rankingService;

    public function __construct(RankingService $rankingService)
    {
        $this->rankingService = $rankingService;
    }

    public function index()
    {
        try {
            $id = Auth::id();
            $duels = DuelUser::where('user_id', $id)->get();
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

    private function calculateElo($currentElo, $opponentElo, $score, $K = 35)
    {
        $expectedScore = 1 / (1 + pow(10, ($opponentElo - $currentElo) / 400));
        $newElo = $K * ($score - $expectedScore);
        return round($newElo);
    }

    public function store(Request $request)
    {

        $validatedData = $request->validate([
            'league_id' => 'required',
            'is_null' => 'required|boolean',
            'description' => 'nullable',
            'win_user' => 'required|array',
            'lose_user' => 'required|array',
        ]);

        DB::beginTransaction();

        try {
            $id = Auth::id();

            $duel = Duel::create([
                'league_id' => $validatedData['league_id'],
                'author_id' => $id,
                'description' => $validatedData['description']
            ]);

            $league_user_count = LeagueUser::where('league_id', $validatedData['league_id'])
                ->where('type', 0)
                ->count();

            $winners = collect($request->win_user);
            $losers = collect($request->lose_user);

            $winner_elo_moyen = $winners->avg('elo');
            $loser_elo_moyen = $losers->avg('elo');

            // Calculer le nouveau elo
            $winner_elo = $this->calculateElo($winner_elo_moyen, $loser_elo_moyen, $validatedData['is_null'] ? 0.5 : 1);
            $loser_elo = $this->calculateElo($loser_elo_moyen, $winner_elo_moyen, $validatedData['is_null'] ? 0.5 : 0);

            $this->processDuelUsers($winners, $duel, $validatedData, $winner_elo, true, $league_user_count);
            $this->processDuelUsers($losers, $duel, $validatedData, $loser_elo, false, $league_user_count);

            DB::commit();

            return response()->json([
                'message' => 'Duel created successfully',
            ], 201);
        } catch (\Throwable $th) {
            DB::rollBack();

            return response()->json([
                'message' => 'Error',
                'error' => $th->getMessage()
            ], 500);
        }
    }

    private function processDuelUsers($users, $duel, $validatedData, $eloChange, $isWinner, $league_user_count)
    {
        foreach ($users as $value) {


            $league_user = LeagueUser::where('user_id', $value['id'])
                ->where('league_id', $validatedData['league_id'])
                ->first();

            if ($league_user) {
                $league_user->elo += $eloChange;
                $league_user->save();
            }

            $coinValue = max(0, round($eloChange / 3 * ($league_user_count / 2)));
            DuelUser::create([
                'user_id' => $value['id'],
                'duel_id' => $duel->id,
                'league_id' => $validatedData['league_id'],
                'league_user_elo_init' => $value['elo'],
                'league_user_elo_add' => $eloChange,
                'coin' => $coinValue,
                'status' => $isWinner ? ($validatedData['is_null'] ? 0.5 : 1) : 0
            ]);

            CoinUser::create([
                'user_id' => $value['id'],
                'value' => $coinValue,
            ]);

            $this->rankingService->updateRanking($validatedData['league_id']);

            $user = User::findOrFail($value['id']);
            $user->coins += $coinValue;
            $user->save();
        }
    }

    public function show($id)
    {
        try {
            $userId = Auth::id();
            $duel = Duel::findOrFail($id);
            $duel_users = DuelUser::where('duel_id', $duel->id)->get();

            $duel_user_main = $duel_users->first(function ($item) use ($userId) {
                return $item->user_id === $userId;
            });

            // Separating the users into winners and losers
            $winners = $duel_users->filter(function ($item) {
                return $item->status === 1;
            });

            $losers = $duel_users->filter(function ($item) {
                return $item->status === 0;
            });

            return response()->json([
                'message' => 'Duel retrieved successfully',
                'duel' => $duel,
                'duel_users' => $duel_users,
                'nbr_users' => count($duel_users),
                'duel_user_main' => $duel_user_main,
                'winners' => $winners->values(), // Re-index the collection
                'losers' => $losers->values(), // Re-index the collection
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
        DB::beginTransaction(); // Début de la transaction
        try {
            $duel = Duel::findOrFail($id);
            $duel->update($request->all());

            DB::commit(); // Commit de la transaction

            return response()->json([
                'message' => 'Duel updated successfully',
                'data' => $duel
            ], 200);
        } catch (\Throwable $th) {
            DB::rollBack(); // Rollback en cas d'échec de l'envoi de l'email
            return response()->json([
                'message' => 'Error',
                'error' => $th->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction(); // Début de la transaction
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
            $this->rankingService->updateRanking($league_user['league_id']);

            DB::commit(); // Commit de la transaction

            return response()->json([
                'message' => 'Duel created successfully',
            ], 201);
        } catch (\Throwable $th) {
            DB::rollBack(); // Rollback en cas d'échec de l'envoi de l'email
            return response()->json([
                'message' => 'Error',
                'error' => $th->getMessage()
            ], 500);
        }
    }
}
