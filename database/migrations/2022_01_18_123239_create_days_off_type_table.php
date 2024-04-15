<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDaysOffTypeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('days_off_types', function (Blueprint $table) {
            $table->id();
            $table->string("name");
            $table->enum("type" ,["daysoff","permission"]);
            $table->string("description");
            $table->string("nb_days");
            $table->boolean("impact_in_dayoff_balance")->default(0);
            $table->boolean("enable")->default(1);
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
        Schema::dropIfExists('days_off_type');
    }
}
