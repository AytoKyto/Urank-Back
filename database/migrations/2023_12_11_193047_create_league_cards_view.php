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
        return "CREATE VIEW view_league_cards AS
        SELECT
            leagues.id,
            leagues.icon,
            leagues.name,
            user.user_id,
            user.elo,
            user.ranking,
            user.type,
            leagues.created_at,
            leagues.updated_at
        FROM leagues
        LEFT JOIN league_users AS user
            ON user.league_id = leagues.id
        GROUP BY  
            leagues.id,
            leagues.icon,
            leagues.name,
            user.user_id,
            user.elo,
            user.ranking,
            user.type,
            leagues.created_at,
            leagues.updated_at;
    ";
    }

    /**
     * Drop the view.
     *
     * @return string
     */
    private function dropView(): string
    {
        return 'DROP VIEW IF EXISTS view_league_cards';
    }
};
