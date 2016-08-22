<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddUserHashColumnToMasKeysTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table("mas_keys", function(Blueprint $table){
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
        Schema::table("mas_keys", function(Blueprint $table){
            $table->dropColumn("user_hash");
        });
    }
}
