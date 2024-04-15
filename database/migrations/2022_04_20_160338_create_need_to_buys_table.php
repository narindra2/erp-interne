<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNeedToBuysTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('need_to_buys', function (Blueprint $table) {
            $table->id();
            $table->foreignId('item_type_id')->constrained('item_types');
            $table->integer('nb');
            $table->foreignId('author_id');
            $table->boolean('is_solved')->default(false);
            $table->boolean('deleted')->default(false);
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
        Schema::dropIfExists('need_to_buys');
    }
}
