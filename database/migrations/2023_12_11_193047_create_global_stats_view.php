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
        return "CREATE VIEW view_global_stats AS
        SELECT
            duel_users.user_id,
            COUNT(DISTINCT duel_users.duel_id) AS nb_duel,
            SUM(CASE WHEN duel_users.status = 1 THEN 1 ELSE 0 END) AS nb_win,
            SUM(CASE WHEN duel_users.status = 0 THEN 1 ELSE 0 END) AS nb_lose,
            SUM(CASE WHEN duel_users.status = 0.5 THEN 1 ELSE 0 END) AS nb_null
        FROM duel_users
        GROUP BY duel_users.user_id;
    ";
    }

    /**
     * Drop the view.
     *
     * @return string
     */
    private function dropView(): string
    {
        return 'DROP VIEW IF EXISTS view_global_stats';
    }
};
