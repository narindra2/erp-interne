<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateAdditionalHourTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('additional_hour_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->boolean('deleted')->default(false);
            $table->timestamps();
        });

        DB::table('additional_hour_types')->insert(['name' => 'Présence']);
        DB::table('additional_hour_types')->insert(['name' => 'Congé']);
        DB::table('additional_hour_types')->insert(['name' => 'AMIT']);
        DB::table('additional_hour_types')->insert(['name' => 'Mission']);
        DB::table('additional_hour_types')->insert(['name' => 'Déplacement']);

        Schema::table('pointing_resumes', function (Blueprint $table) {
            $table->foreignId('additional_hour_type_id')->default(1);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('additional_hour_types');
        Schema::table('pointing_resumes', function (Blueprint $table) {
            $table->dropColumn('additional_hour_type_id');
        });
    }
}
