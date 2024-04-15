<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTablePointsSuivi extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('suivi_points', function (Blueprint $table) {
            $table->id();
            $table->foreignId("client_type_id");
            $table->foreignId("project_type_id");
            $table->integer("niveau")->default(1);
            $table->decimal("point")->default(0);
            $table->decimal("point_sup")->default(0);
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
        Schema::dropIfExists('suivi_points');
    }
}
