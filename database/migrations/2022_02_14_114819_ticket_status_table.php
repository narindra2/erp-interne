<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class TicketStatusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->down();
        Schema::create('tickets_status', function (Blueprint $table) {
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
        Schema::dropIfExists('tickets_status');
    }

    private function  seed_it(){
        DB::table('tickets_status')->insert(
            [
                ["name" => "new" , "class" => "danger"],
                ["name" => "in_progress","class" => "primary"],
                ["name" => "stand_by","class" => "warning"],
                ["name" => "resolve" ,"class" => "success"],
                ["name" => "close_ticket" ,"class" => "dark" ],
            ]
        );
    }
}
