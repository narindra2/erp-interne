<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateViewMessages extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("CREATE OR REPLACE VIEW v_messages AS SELECT messages.*, message_seens.id as message_seens_id, user_id, message_seens.deleted as message_seens_deleted, message_seens.created_at as message_seens_created_at, message_seens.updated_at as message_seens_updated_at FROM messages left join message_seens on message_seens.message_id = messages.id");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("DROP VIEW v_messages");
    }
}
