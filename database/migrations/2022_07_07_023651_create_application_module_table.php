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
        Schema::create('application_module', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("application_id");
            $table->unsignedBigInteger("module_id");
            $table->foreign("application_id")->references("id")->on("applications");
            $table->foreign("module_id")->references("id")->on("application_modules");
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
        Schema::dropIfExists('application_module');
    }
};
