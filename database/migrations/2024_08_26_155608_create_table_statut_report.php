<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableStatutReport extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('statut_report', function (Blueprint $table) {
            $table->id();
            $table->foreignId("user_id");
            $table->foreignId("type_status_report_id");

            $table->date("start_date");
            $table->string("time_start")->nullable();
            $table->boolean("start_date_is_morning")->default(1);
            
            $table->date("fin_date")->nullable();
            $table->string("time_fin")->nullable();
            $table->boolean("fin_date_is_morning")->default(1);

            $table->mediumText("report")->nullable();
            $table->string("status")->nullable();
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
        Schema::dropIfExists('statut_report');
    }
}
