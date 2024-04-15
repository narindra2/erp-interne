<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class UpdateUserJobsAddCategorieAndGroup extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_jobs', function (Blueprint $table) {
            $table->string('category', 10)->nullable();
            $table->string('group', 10)->nullable();
        });
        //Recreate the view SQL
        DB::statement("CREATE OR REPLACE VIEW v_max_date_job_user AS SELECT MAX(date_user_job) as date_user_job_max, users_id FROM user_jobs GROUP BY users_id");
        $query = "CREATE OR REPLACE VIEW v_userjob AS SELECT user_jobs.* FROM user_jobs JOIN v_max_date_job_user ON date_user_job = date_user_job_max AND v_max_date_job_user.users_id = user_jobs.users_id";
        DB::statement($query);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_jobs', function (Blueprint $table) {
            $table->dropColumn('category');
            $table->dropColumn('group');
        });
    }
}
