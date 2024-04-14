<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('pain_site_tables', function (Blueprint $table) {
            $table->smallInteger('have_relief_method')->unsigned()->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('pain_site_tables', function (Blueprint $table) {
            $table->boolean('have_relief_method')->nullable()->change();
        });
    }
};
