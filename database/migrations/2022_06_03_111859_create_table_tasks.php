<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableTasks extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->string("title")->nullable();
            $table->text("description")->nullable();
            $table->foreignId("creator");
            $table->string("assign_to")->nullable();
            $table->foreignId("status_id");
            $table->foreignId("section_id");
            $table->integer("order_on_board")->default(0);
            $table->integer("label")->nullable();
            $table->boolean("recurring")->default(0);
            $table->string("recurring_type")->nullable();
            $table->text("recurring_detail")->nullable();
            $table->date("last_set_recycle")->nullable();
            $table->date("start_date_recurring")->nullable();
            $table->date("start_deadline_date")->nullable();
            $table->date("end_deadline_date")->nullable();
            $table->date("last_recurring")->nullable();
            $table->boolean("archived")->default(0);
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
        Schema::dropIfExists('tasks');
    }
}
