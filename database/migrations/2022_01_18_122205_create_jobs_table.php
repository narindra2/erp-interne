<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateJobsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('jobs', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50);
            $table->boolean('deleted')->default(0);
            $table->timestamps();
        });

        DB::table('jobs')->insert(['name' => 'Responsable RH']);
        DB::table('jobs')->insert(['name' => 'Technicien']);
        DB::table('jobs')->insert(['name' => 'Dessinateur']);
        DB::table('jobs')->insert(['name' => 'Développeur']);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('jobs');
    }
}
