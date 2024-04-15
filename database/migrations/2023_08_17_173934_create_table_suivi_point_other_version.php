<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableSuiviPointOtherVersion extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('suivi_version_point_montage', function (Blueprint $table) {
            $table->id();
            $table->foreignId("base_id_point");
            $table->foreignId("version_id");
            $table->string("point")->nullable();
            $table->string("percentage")->nullable();
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
        Schema::dropIfExists('suivi_version_point_montage');
    }
}
