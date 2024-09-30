<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableProspectCompany extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('prospects_company', function (Blueprint $table) {
            $table->id();
            $table->string("name_company");
            $table->string("tel_company" , 30)->nullable();
            $table->string("email_company" , 100)->nullable();
            $table->text("linkedin_company" , 100)->nullable();
            $table->text("site_company")->nullable();
            $table->string("size_company" , 50)->nullable();
            $table->boolean("deleted"  )->default(0);
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
        Schema::dropIfExists('prospects_company');
    }
}
