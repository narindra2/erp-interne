<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableSuiviItems extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('suivi_items', function (Blueprint $table) {
            $table->id();
            $table->string("status_id")->nullable();
            $table->string("suivi_id")->nullable();
            $table->string("version_id")->nullable();
            $table->integer("montage")->nullable();
            $table->foreignId("user_id");
            $table->dateTime("last_check")->nullable();
            $table->dateTime("finished_at")->nullable();
            $table->string("duration")->nullable();
            $table->string("level_id")->nullable();
            $table->string("times_estimated")->nullable();
            $table->string("poles")->nullable();
            $table->foreignId("follower")->nullable();
            $table->boolean("disabled")->default(0);
            $table->boolean("deleted")->default(0);
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
        Schema::dropIfExists('suivi_items');
    }
}
