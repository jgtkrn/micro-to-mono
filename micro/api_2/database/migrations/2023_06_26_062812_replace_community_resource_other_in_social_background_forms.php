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
        Schema::table('community_resource_tables', function (Blueprint $table) {
            $table->dropColumn('community_resource_other');
        });
        Schema::table('social_background_forms', function (Blueprint $table) {
            $table->string('community_resource_other')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('community_resource_tables', function (Blueprint $table) {
            $table->string('community_resource_other')->nullable();
        });
        Schema::table('social_background_forms', function (Blueprint $table) {
            $table->dropColumn('community_resource_other');
        });
    }
};
