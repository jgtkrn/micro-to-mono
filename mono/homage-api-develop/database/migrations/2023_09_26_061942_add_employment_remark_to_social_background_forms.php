<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('social_background_forms', function (Blueprint $table) {
            $table->text('employment_remark')->nullable();
        });
    }

    public function down()
    {
        Schema::table('social_background_forms', function (Blueprint $table) {
            $table->dropColumn('employment_remark');
        });
    }
};
