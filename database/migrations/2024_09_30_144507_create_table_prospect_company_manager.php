<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableProspectCompanyManager extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('prospects_company_manager', function (Blueprint $table) {
            $table->id();
            $table->string("name_manager")->nullable();
            $table->string("email_manager")->nullable();
            $table->string("tel_manager")->nullable();
            $table->string("site_manager")->nullable();
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
        Schema::dropIfExists('prospects_company_manager');
    }
}
