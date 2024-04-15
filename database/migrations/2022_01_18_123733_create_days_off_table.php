<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDaysOffTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('days_off', function (Blueprint $table) {
            $table->id();
            $table->foreignId('applicant_id')->constrained('users');
            $table->foreignId('author_id')->constrained('users');
            $table->date('start_date');
            $table->date('return_date');
            $table->boolean('start_date_is_morning')->default(1);
            $table->boolean('return_date_is_morning')->default(0);
            $table->text('reason');
            $table->date('result_date')->nullable();
            $table->string('result', 50);
            $table->foreignId('manager_id')->nullable()->constrained('users');
            $table->foreignId("type_id")->constrained('days_off_types');
            $table->boolean('deleted')->default(0);
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
        Schema::dropIfExists('days_off');
    }
}
