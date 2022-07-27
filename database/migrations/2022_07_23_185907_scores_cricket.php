<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ScoresCricket extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('scores_cricket', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('game_id');

            $table->integer('t1i1r')->nullable();
            $table->integer('t2i1r')->nullable();
            $table->integer('t1i2r')->nullable();
            $table->integer('t2i2r')->nullable();

            $table->integer('t1i1w')->nullable();
            $table->integer('t2i1w')->nullable();
            $table->integer('t1i2w')->nullable();
            $table->integer('t2i2w')->nullable();
            
            $table->double('t1i1o')->nullable();
            $table->double('t2i1o')->nullable();
            $table->double('t1i2o')->nullable();
            $table->double('t2i2o')->nullable();

            $table->integer('t1i1d')->nullable();
            $table->integer('t2i1d')->nullable();
            $table->integer('t1i2d')->nullable();
            $table->integer('t2i2d')->nullable();

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
