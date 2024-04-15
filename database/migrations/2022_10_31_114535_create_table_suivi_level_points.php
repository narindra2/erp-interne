<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableSuiviLevelPoints extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('suivi_level_points', function (Blueprint $table) {
            $table->id();
            $table->foreignId("version_id");
            $table->integer("level"); // level
            $table->integer("point");
            $table->boolean("deleted")->default(0);
        });
    }
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('suivi_level_points');
    }
}
