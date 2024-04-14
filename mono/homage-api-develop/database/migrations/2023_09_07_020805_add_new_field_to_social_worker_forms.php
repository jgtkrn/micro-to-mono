<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('social_worker_forms', function (Blueprint $table) {
            $table->text('referral_other_text')->nullable();
        });
    }

    public function down()
    {
        Schema::table('social_worker_forms', function (Blueprint $table) {
            $table->dropColumn('referral_other_text');
        });
    }
};
