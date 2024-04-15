<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateUserTypeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_type', function (Blueprint $table) {
            $table->id();
            $table->string('name', 40);
            $table->boolean('deleted')->default(0);
            $table->timestamps();
        });

        DB::table('user_type')->insert(['name' => 'admin']);
        DB::table('user_type')->insert(['name' => 'rh']);
        DB::table('user_type')->insert(['name' => 'tech']);
        DB::table('user_type')->insert(['name' => 'contributeur']);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_type');
    }
}
