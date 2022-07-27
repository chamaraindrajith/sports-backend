<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class Sports extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sports', function (Blueprint $table) {
            $table->increments('id');
            $table->string('slug');
            $table->string('name');
        });

        DB::table('sports')->insert([
            ['id' => 1, 'slug' => 'soccer', 'name' => 'Soccer'],
            ['id' => 2, 'slug' => 'hockey', 'name' => 'Hockey'],
            ['id' => 3, 'slug' => 'basketball', 'name' => 'Basketball'],
            ['id' => 4, 'slug' => 'tennis', 'name' => 'Tennis'],
            ['id' => 5, 'slug' => 'cricket', 'name' => 'Cricket'],
        ]);
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
