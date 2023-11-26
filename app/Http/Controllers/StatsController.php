<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ViewStatsModel;

class StatsController extends Controller
{
    public static function getStats($query_value) {
        // Get the query value
        $query_value = request('query_value');

        // Get data from query name
        parse_str($query_value, $query_data);

        // get the data from the query
        $query = ViewStatsModel::where('user_id', $query_data['user_id'])
            ->where('league_id', $query_data['league_id'])
            ->get();
        

        // Return data
        try {
            // Check if all parameters are set
            return response()->json([
                'status' => true,
                'message' => 'Requête effectuée avec succès',
                'query_name' => $query_value,
                'nbr' => count($query),
                'data' => $query
            ], 200);
        } catch (\Exception $e) {
            // Return error
            return response()->json([
                'status' => false,
                'message' => 'Erreur lors de la requête',
                'query_name' => $query_value,
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
