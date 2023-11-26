<?php

// app/Http/Controllers/UserPaymentController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserPayment;

class UserPaymentController extends Controller {
    public function index() {
        try {
            $payments = UserPayment::all();
            return response()->json([
                'message' => 'User payments retrieved successfully',
                'data' => $payments
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
            $payment = UserPayment::create($request->all());
            return response()->json([
                'message' => 'Payment created successfully',
                'data' => $payment
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
            $payment = UserPayment::findOrFail($id);
            return response()->json([
                'message' => 'User payment retrieved successfully',
                'payment' => $payment
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
            $payment = UserPayment::findOrFail($id);
            $payment->update($request->all());
            return response()->json([
                'message' => 'Payment updated successfully',
                'data' => $payment
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
            $payment = UserPayment::findOrFail($id);
            $payment->delete();
            return response()->json([
                'message' => 'Payment deleted successfully'
            ], 204);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Error',
                'error' => $th->getMessage()
            ], 500);
        }
    }
}
