<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('social_worker_forms', function (Blueprint $table) {
            $table->string('elder_edu_text')->nullable();
            $table->string('elder_religious_text')->nullable();
            $table->string('elder_housetype_text')->nullable();
            $table->string('elder_home_fall_text')->nullable();
            $table->string('elder_home_hygiene_text')->nullable();
            $table->string('home_service_text')->nullable();
        });
    }

    public function down()
    {
        Schema::table('social_worker_forms', function (Blueprint $table) {
            $table->dropColumn([
                'elder_edu_text',
                'elder_religious_text',
                'elder_housetype_text',
                'elder_home_fall_text',
                'elder_home_hygiene_text',
                'home_service_text',
            ]);
        });
    }
};
