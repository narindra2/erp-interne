<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateHourRecoveriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hour_recoveries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id');
            $table->foreignId('job_id');
            $table->date('date_of_absence');
            $table->unsignedInteger('duration_of_absence');
            $table->date('recovery_start_date');
            $table->date('recovery_end_date');
            $table->text('description');
            $table->boolean('is_validated')->nullable();
            $table->boolean('deleted')->default(false);
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
        Schema::dropIfExists('hour_recoveries');
    }
}
