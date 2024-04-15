<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class TicketUrgenceTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->down();
        Schema::create('tickets_urgence', function (Blueprint $table) {
            $table->id();
            $table->string("name");
            $table->string("class");
            $table->timestamps();
        });
        $this->seed_it();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tickets_urgence');
    }
    private function  seed_it(){
        DB::table('tickets_urgence')->insert(
            [
                ["name" => "low" , "class" => "dark"],
                ["name" => "medium","class" => "warning"],
                ["name" => "urgent","class" => "danger"],
            ]
        );
    }
}
