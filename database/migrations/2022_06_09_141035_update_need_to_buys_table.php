<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateNeedToBuysTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('need_to_buys', function (Blueprint $table) {
            $table->string('ticket_id')->change();
            $table->dropColumn('is_solved');
            $table->foreignId('department_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('need_to_buys', function (Blueprint $table) {
            //
        });
    }
}
