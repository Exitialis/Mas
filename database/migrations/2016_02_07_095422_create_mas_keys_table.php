<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class CreateMasKeysTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mas_keys', function(Blueprint $table){
            $table->increments("id");
            $table->timestamps();
            $table->bigInteger("user_id"); // For integration with User model
            $table->string("username");
            $table->char("uuid", 36);
            $table->string("session");
            $table->string("serverid");
            $table->string("HID");
            $table->string("pass"); //token sashok, remembering user pass
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('mas_keys');
    }
}
