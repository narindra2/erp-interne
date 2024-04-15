<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableMeetingRoomTimes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('meeting_room_use', function (Blueprint $table) {
            $table->id();
            $table->foreignId("room_id");
            $table->string("creator_id");
            $table->string("type");
            $table->text("subject")->nullable();
            $table->date("day_meeting")->nullable();
            $table->dateTime("time_start");
            $table->dateTime("time_end");
            $table->boolean("deleted");
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
        Schema::dropIfExists('meeting_room_use');
    }
}
