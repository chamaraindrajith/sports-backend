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

            $table->integer('t1i1r')->default('0');
            $table->integer('t2i1r')->default('0');
            $table->integer('t1i2r')->default('0');
            $table->integer('t2i2r')->default('0');

            $table->integer('t1i1w')->default('0');
            $table->integer('t2i1w')->default('0');
            $table->integer('t1i2w')->default('0');
            $table->integer('t2i2w')->default('0');
            
            $table->double('t1i1o')->default('0');
            $table->double('t2i1o')->default('0');
            $table->double('t1i2o')->default('0');
            $table->double('t2i2o')->default('0');

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
