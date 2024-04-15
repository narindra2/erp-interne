<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateContractTypeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contract_type', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50);
            $table->string('acronym', 10);
            $table->boolean('deleted')->default(0);
            $table->timestamps();
        });

        DB::table('contract_type')->insert(['name' => "CDI - Employé", "acronym" => "CDI"]);
        DB::table('contract_type')->insert(['name' => "CDI - Prestataire", "acronym" => "CDI"]);
        DB::table('contract_type')->insert(['name' => "Période d'essai", "acronym" => 'PE']);
        DB::table('contract_type')->insert(['name' => "Stagiaire", "acronym" => "Stagiaire"]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('contract_type');
    }
}
