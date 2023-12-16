<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement($this->createView());
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement($this->dropView());
    }

    /**
     * Create the view.
     *
     * @return string
     */
    private function createView(): string
    {
        return "CREATE VIEW view_league_stats AS
         SELECT
             league_users.user_id,
             league_users.league_id,
             AVG(league_users.elo) AS elo_moyen,  -- ou MAX, MIN, etc.
             COUNT(DISTINCT duel.duel_id) AS nb_duel,
             SUM(CASE WHEN duel.status = 1 THEN 1 ELSE 0 END) AS nb_win,
             SUM(CASE WHEN duel.status = 0 THEN 1 ELSE 0 END) AS nb_lose,
             SUM(CASE WHEN duel.status = 0.5 THEN 1 ELSE 0 END) AS nb_null
         FROM league_users
         LEFT JOIN duel_users AS duel
             ON league_users.user_id = duel.user_id
         GROUP BY league_users.user_id, league_users.league_id;
    ";
    }

    /**
     * Drop the view.
     *
     * @return string
     */
    private function dropView(): string
    {
        return 'DROP VIEW IF EXISTS view_league_stats';
    }
};
