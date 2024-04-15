<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDetailNeedsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('detail_needs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('need_to_buy_id');
            $table->integer('qty');
            $table->string('status');
            $table->date('status_date');
            $table->foreignId('author_id');
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
        Schema::dropIfExists('detail_needs');
    }
}
