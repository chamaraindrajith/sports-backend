<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Commentary extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('commentary', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('game_id');

            $table->integer('min');
            $table->integer('min_ex');
            $table->integer('it');

            $table->double('over'); // "Ov"
            $table->integer('bowler'); // "Aid"
            $table->integer('batter'); // "Oid"
            $table->string('run'); // "Sv"

            


            $table->string('heading'); // "S"

            $table->string('text'); // "T"
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
