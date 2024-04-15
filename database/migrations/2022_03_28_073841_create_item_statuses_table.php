<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateItemStatusesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('item_statuses', function (Blueprint $table) {
            $table->id();
            $table->string("name", 50);
            $table->boolean('deleted')->default(0);
            $table->timestamps();
        });

        DB::table("item_statuses")->insert(["name" => "OK"]);
        DB::table("item_statuses")->insert(["name" => "En Panne"]);
        DB::table("item_statuses")->insert(["name" => "Détruit"]);
        DB::table("item_statuses")->insert(["name" => "Perdu"]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('item_statuses');
    }
}
