<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Games extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('games', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('stage_id');
            $table->string('slug');
            $table->integer('sport_id');
            $table->string('ground');
            // $table->text('teams1');
            // $table->text('teams2');
            $table->text('team_id_teams1');
            $table->text('team_id_teams2');
            // $table->integer('stage_id');
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->integer('category_id');

            $table->string('cricket_phase')->nullable();
            $table->string('cricket_phase_info')->nullable();
            $table->string('live_time')->nullable();
            $table->string('live_status_comment')->nullable();

            $table->string('status_text');
            $table->integer('status');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
