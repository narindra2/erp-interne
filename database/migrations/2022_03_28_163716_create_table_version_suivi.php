<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableVersionSuivi extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->down();
        Schema::create('suivi_versions', function (Blueprint $table) {
            $table->id();
            $table->string("title");
            $table->string("belongs");
            $table->bigInteger("creator_id")->nullable();
            $table->boolean("deleted")->default(0);
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
        Schema::dropIfExists('suivi_versions');
    }
    private function seed_it(){
        $versions = [
            ["title" => "Initilisation" , "belongs" => "urba,cq"],
            ["title" => "Version 1" , "belongs" => "all"],
            ["title" => "Version 2" , "belongs" => "all"],
            ["title" => "Version 3" , "belongs" => "all"],
            ["title" => "Finalisation" , "belongs" => "all"],
            ["title" => "DPC - Version 1" , "belongs" => "all"],
            ["title" => "DPC - Version 2" , "belongs" => "all"],
            ["title" => "DPC - Version 3" , "belongs" => "all"],
            ["title" => "NOTICE" , "belongs" => "all"],
            ["title" => "Fichier source" , "belongs" => "dessi,m2p"],
            ["title" => "ERREUR M2P" , "belongs" => "dessi,urba"],
            ["title" => "ERREUR CQ" , "belongs" => "dessi,urba"],
            ["title" => "INPRESSION" , "belongs" => "m2p"],
            ["title" => "DPC-IMPRESSION" , "belongs" => "m2p"],
            ["title" => "QR-CODE" , "belongs" => "m2p"],
            ["title" => "LIVRAISON CLIENT" , "belongs" => "m2p"],
            ["title" => "SUIVI SQUAD" , "belongs" => "m2p"],
            ["title" => "LOGISTIQUE" , "belongs" => "urba"],
            ["title" => "AUTRE" , "belongs" => "all"],
            ["title" => "TEST" , "belongs" => "all"],
            ["title" => "COMPLEMENTS" , "belongs" => "all"],
            ["title" => "SUIVI HELP" , "belongs" => "cq"],
        ];
        DB::table('suivi_versions')->insert($versions); 
    }
}
