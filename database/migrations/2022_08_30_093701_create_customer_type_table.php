<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomerTypeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    private $table = "customer_type";

    public function up()
    {
        Schema::create($this->table, function (Blueprint $table) {
            $table->id();
            $table->string("name");
            $table->boolean('deleted')->default(0);
            $table->timestamps();
        });
        $this->seed();
    }

    private function seed(){
        DB::table($this->table)->insert([
                ['name' => 'particulier'],
                ['name' => 'société']
            ]
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('customer_type');
    }
}
