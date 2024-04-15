<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableTickets extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->string("description")->nullable();
            $table->string("type_id");
            $table->string("assign_to")->nullable();
            $table->foreignId("author_id");
            $table->string("proprietor_id")->nullable();
            $table->foreignId("status_id");
            $table->foreignId("urgence_id");
            $table->foreignId("resolve_by")->nullable();
            $table->date("resolve_date")->nullable();
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
        Schema::dropIfExists('tickets');
    }
}
