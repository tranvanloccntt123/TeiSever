<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('group_message', function (Blueprint $table) {
            //
            $table->text("config")->default('{ theme: "default" }');
        });
        // alter view ROOM_MESSAGE as
        // select group_message.id, group_message.name, group_message.config,group_message.created_at, group_message.updated_at, COUNT(group_message.id) as count_member from group_message INNER JOIN group_message_user ON group_message_user.group_message_id = group_message.id GROUP BY group_message.id;
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('group_message', function (Blueprint $table) {
            //
        });
    }
};
