<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use PhpOffice\PhpSpreadsheet\Calculation\Database\DVar;

class CreateItemCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('item_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name', 60);
            $table->boolean('deleted')->default(0);
            $table->timestamps();
        });

        DB::table("item_categories")->insert(['name' => 'Matériel informatique']);
        DB::table("item_categories")->insert(['name' => 'Meuble de bureau']);
        DB::table("item_categories")->insert(['name' => 'Fourniture de bureau']);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('item_categories');
    }
}
