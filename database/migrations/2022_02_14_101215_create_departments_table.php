<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateDepartmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('departments', function (Blueprint $table) {
            $table->id();
            $table->string("name", 60);
            $table->boolean('deleted')->default(false);
            $table->timestamps();
        });

        DB::table('departments')->insert(['name' => 'IT']);
        DB::table('departments')->insert(['name' => 'DESSI - JM']);
        DB::table('departments')->insert(['name' => 'DESSI - PCM']);
        DB::table('departments')->insert(['name' => 'DESSI - PMDC']);
        DB::table('departments')->insert(['name' => 'DESSI - DTAH']);
        DB::table('departments')->insert(['name' => 'REDAC']);
        DB::table('departments')->insert(['name' => 'CALL']);
        DB::table('departments')->insert(['name' => 'DEV']);

        DB::table('departments')->insert(['name' => 'DEPCOM']);
        DB::table('departments')->insert(['name' => 'DEPFIN']);
        DB::table('departments')->insert(['name' => 'DEPRH']);

        Schema::table('user_jobs', function (Blueprint $table) {
            $table->foreignId('department_id')->nullable()->constrained('departments');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_jobs', function (Blueprint $table) {
            $table->dropColumn('department_id');
        });
        Schema::dropIfExists('departments');
    }
}
