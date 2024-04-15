<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePointingResumesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pointing_resumes', function (Blueprint $table) {
            $table->id();
            $table->date('day');
            $table->string('registration_number', 10);
            $table->dateTime('entry_time');
            $table->dateTime('exit_time');
            $table->integer('minute_worked');
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
        Schema::dropIfExists('pointing_resumes');
    }
}
