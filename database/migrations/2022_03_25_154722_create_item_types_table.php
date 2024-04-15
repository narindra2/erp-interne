<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateItemTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('item_types', function (Blueprint $table) {
            $table->id();
            $table->foreignId('item_category_id')->constrained('item_categories');
            $table->string('name');
            $table->string('brand')->nullable();
            $table->string('rule_code');
            $table->boolean('deleted')->default(0);
            $table->timestamps();
        });

        DB::table("item_types")->insert(['item_category_id' => 1, 'name' => "UC", "brand" => "HP", "rule_code" => "UC-"]);
        DB::table("item_types")->insert(['item_category_id' => 1, 'name' => "UC", "brand" => "Lenovo", "rule_code" => "UC-"]);
        DB::table("item_types")->insert(['item_category_id' => 1, 'name' => "Clavier", "brand" => "Dell", "rule_code" => "CL-"]);
        DB::table("item_types")->insert(['item_category_id' => 1, 'name' => "Souris", "brand" => "Asus", "rule_code" => "SO-"]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('item_types');
    }
}
