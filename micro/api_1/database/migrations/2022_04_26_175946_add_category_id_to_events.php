<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCategoryIdToEvents extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table("events", function (Blueprint $table) {
            $table
                ->bigInteger("category_id")
                ->unsigned()
                ->nullable();
            $table
                ->foreign("category_id")
                ->references("id")
                ->on("categories");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table("events", function (Blueprint $table) {
            $table->dropForeign(["category_id"]);
            $table->dropColumn("category_id");
        });
    }
}
