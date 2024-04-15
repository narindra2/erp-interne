<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableTaskStatus extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
       
        Schema::create('task_status', function (Blueprint $table) {
            $table->id();
            $table->string("title");
            $table->string("class")->nullable();
            $table->string("section_id");
            $table->string("acronym")->nullable();
            $table->integer("order_board")->default(0);
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
        Schema::dropIfExists('task_status');
    }
}
