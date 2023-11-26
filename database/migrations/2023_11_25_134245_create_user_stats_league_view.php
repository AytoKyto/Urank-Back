<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("
        CREATE VIEW user_stats_league_view 
        AS
        SELECT
            u.id AS user_id,
            u.name AS user_name,
            l.id AS league_id,
            l.name AS league_name,
            SUM(duel.winner_user_id = u.id) AS nb_wins,
            SUM(duel.loser_user_id = u.id) AS nb_losses,
            SUM(duel.winner_user_id = u.id) / COUNT(duel.id) AS win_rate,
            SUM(duel.winner_user_id = u.id) * 3 + SUM(duel.loser_user_id = u.id) AS points
        FROM
            users u
            JOIN league_users lu ON lu.user_id = u.id
            JOIN leagues l ON l.id = lu.league_id
            JOIN duels duel ON duel.league_id = l.id
        GROUP BY
            u.id,
            u.name,
            l.id,
            l.name
        ");
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('DROP VIEW user_stats_league_view');
    }
};
