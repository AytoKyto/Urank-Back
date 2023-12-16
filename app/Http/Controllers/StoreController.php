<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\ProductStore;
use App\Models\CoinUser;
use App\Models\User;

class StoreController extends Controller
{
    public function index()
    {
        try {
            $userId = Auth::id();

            $transaction = CoinUser::where('user_id', $userId)
                ->with('product_id')
                ->get();

            $stores = ProductStore::whereNotIn('id', $transaction->pluck('product_id'))
                ->get();

            return response()->json([
                'message' => 'Stores retrieved successfully',
                'data' => $stores
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Error',
                'error' => $th->getMessage()
            ], 500);
        }
    }

    public function byProduct(Request $request)
    {
        $validatedData = $request->validate([
            'user_id' => 'required',
            'user_coin_value' => 'required|numeric',
            'product_id' => 'required',
            'product_coin_value' => 'required|numeric'
        ]);

        try {
            if ($validatedData['user_coin_value'] >= $validatedData['product_coin_value']) {
                // Création de la transaction
                $transaction = CoinUser::create([
                    'user_id' => $validatedData['user_id'],
                    'product_id' => $validatedData['product_id'],
                    'value' => -$validatedData['product_coin_value']
                ]);

                // Update User
                $user = User::findOrFail($validatedData['user_id']);
                $user->coin_value = $validatedData['user_coin_value'] - $validatedData['product_coin_value'];
                $user->save();


                return response()->json([
                    'message' => 'Product added to user successfully',
                    'data' => $transaction
                ], 201);
            } else {
                return response()->json([
                    'message' => 'User coin value is not enough',
                ], 422); // Code de statut modifié
            }
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Error',
                'error' => $th->getMessage()
            ], 500);
        }
    }
}
