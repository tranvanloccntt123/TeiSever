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
        Schema::create('pm_products', function (Blueprint $table) {
            $table->id();
            $table->string("name")->default("");
            $table->text("description")->nullable();
            $table->double("price")->default(0);
            $table->text("options")->nullable();
            $table->unsignedBigInteger("category_id");
            $table->foreign("category_id")->references("id")->on("pm_categorys");
            $table->unsignedBigInteger("application_id");
            $table->foreign("application_id")->references("id")->on("applications");
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
        Schema::dropIfExists('pm_products');
    }
};
