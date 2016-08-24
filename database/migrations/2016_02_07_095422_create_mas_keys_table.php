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
            $table->integer("user_id")->unsigned(); // For integration with User model
            $table->string("username");
            $table->uuid('uuid');
            $table->string("session");
            $table->string("serverid");
            $table->string("HID");
            $table->string("pass"); //token sashok, remembering user pass
            $table->string("user_hash")->nullable();
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
