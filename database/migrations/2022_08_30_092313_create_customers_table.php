<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_type_id');
            $table->integer('civility')->default(0);
            $table->string('lastname');
            $table->string('firstname')->nullable();
            $table->date('birthday');
            $table->string('birthday_place')->nullable();
            $table->string('email');
            $table->timestamp('email_verified_at')->nullable();
            $table->string('phone_number')->nullable();
            $table->string('c_way_number')->nullable();
            $table->string('c_locality')->nullable();
            $table->string('c_postal_code')->nullable();
            $table->string('c_town')->nullable();
            $table->string('denomination')->nullable();
            $table->string('social_reason')->nullable();
            $table->string('society_type')->nullable();
            $table->string('siret_number')->nullable();
            $table->boolean('deleted')->default(0);
            $table->rememberToken()->nullable();
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
        Schema::dropIfExists('customers');
    }
}
