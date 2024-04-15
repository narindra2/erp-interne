<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class UpdateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('firstname', 70)->nullable();
            $table->date('birthdate');
            $table->string('place_of_birth');
            $table->string('address', 100);
            $table->string('CIN', 30)->nullable();
            $table->string('avatar')->nullable();
            $table->date('cin_delivered')->nullable();
            $table->string('registration_number', 20)->unique();
            $table->date('hiring_date')->nullable();
            $table->string('last_check', 10)->nullable();
            $table->date('last_check_date')->nullable();
            $table->foreignId('user_type_id')->constrained('user_type');
            $table->foreignId('marital_status_id')->constrained('marital_statuses');
            $table->integer('sex');
            $table->decimal('nb_days_off_remaining', 15, 2)->default(0);
            $table->string('phone_number',50);
            $table->string('father_fullname',100)->nullable();
            $table->string('mother_fullname',100)->nullable();
            $table->string('qualification', 100)->nullable();
            $table->string('regulation',100)->nullable();
            $table->string('account_number',100)->nullable();
            $table->string('marry_fullname',100)->nullable();
            $table->date('marry_birthdate')->nullable();
            $table->string('marry_place_of_birth')->nullable();
            $table->string('marry_CIN',30)->nullable();
            $table->date('marry_cin_delivered')->nullable();
            $table->string('marry_phone_number',50)->nullable();
            $table->string('marry_email')->nullable();
            $table->string('marry_job')->nullable();

            $table->boolean('deleted')->default(0);
            $table->foreignId('deleted_by')->nullable();
        });

        DB::table('users')->insert([
            'name' => 'ADMIN',
            'firstname' => 'ADMIN',
            'birthdate' => '2001-01-01',
            'place_of_birth' => 'Clinique Ankadifotsy',
            'address' => 'Lot Admin',
            'CIN' => '12345689',
            'cin_delivered' => '2001-01-01',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('123456'),
            'registration_number' => '100000',
            'user_type_id' => 1,
            'marital_status_id' => 1,
            'sex' => 1,
            'phone_number' => '123456789'
        ]);
        DB::table('users')->insert([
            'name' => 'Mialy',
            'firstname' => 'Mialy',
            'birthdate' => '2001-01-01',
            'place_of_birth' => 'Clinique Ankadifotsy',
            'address' => 'Lot Admin',
            'CIN' => '12345689',
            'cin_delivered' => '2001-01-01',
            'email' => 'mialy_rh@gmail.com',
            'password' => Hash::make('w{9So)^I'),
            'registration_number' => '100001',
            'user_type_id' => 1,
            'marital_status_id' => 1,
            'sex' => 1,
            'phone_number' => '123456789'
        ]);
        DB::table('users')->insert([
            'name' => 'Eva',
            'firstname' => 'Eva',
            'birthdate' => '2001-01-01',
            'place_of_birth' => 'Clinique Ankadifotsy',
            'address' => 'Lot Admin',
            'CIN' => '12345689',
            'cin_delivered' => '2001-01-01',
            'email' => 'eva_rh@gmail.com',
            'password' => Hash::make('P-@n./,v'),
            'registration_number' => '100002',
            'user_type_id' => 1,
            'marital_status_id' => 1,
            'sex' => 1,
            'phone_number' => '123456789'
        ]);
        DB::table('users')->insert([
            'name' => 'Mamitiana',
            'firstname' => 'Mamitiana',
            'birthdate' => '2001-01-01',
            'place_of_birth' => 'Clinique Ankadifotsy',
            'address' => 'Lot Admin',
            'CIN' => '12345689',
            'cin_delivered' => '2001-01-01',
            'email' => 'mamitiana_ref@gmail.com',
            'password' => Hash::make('cjWG)#9W'),
            'registration_number' => '100003',
            'user_type_id' => 1,
            'marital_status_id' => 1,
            'sex' => 1,
            'phone_number' => '123456789'
        ]);
        DB::table('users')->insert([
            'name' => 'Harena',
            'firstname' => 'Harena',
            'birthdate' => '2001-01-01',
            'place_of_birth' => 'Clinique Ankadifotsy',
            'address' => 'Lot Admin',
            'CIN' => '12345689',
            'cin_delivered' => '2001-01-01',
            'email' => 'harena_ref@gmail.com',
            'password' => Hash::make('o?Kza$P6'),
            'registration_number' => '100004',
            'user_type_id' => 1,
            'marital_status_id' => 1,
            'sex' => 1,
            'phone_number' => '123456789'
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
