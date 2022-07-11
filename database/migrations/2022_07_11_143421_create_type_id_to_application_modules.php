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
        Schema::table('application_modules', function (Blueprint $table) {
            //
            $table->unsignedBigInteger("type_id")->nullable();
            $table->foreign("type_id")->references("id")->on("application_type");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('application_modules', function (Blueprint $table) {
            //
        });
    }
};
