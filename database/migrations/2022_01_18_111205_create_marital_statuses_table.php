<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateMaritalStatusesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('marital_statuses', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50);
            $table->boolean('deleted')->default(0);
            $table->timestamps();
        });

        DB::table('marital_statuses')->insert(['name' => 'Célibataire']);
        DB::table('marital_statuses')->insert(['name' => 'Marié(e)']);
        DB::table('marital_statuses')->insert(['name' => 'Divorcé(e)']);
        DB::table('marital_statuses')->insert(['name' => 'Veuf(ve)']);

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('marital_statuses');
    }
}
