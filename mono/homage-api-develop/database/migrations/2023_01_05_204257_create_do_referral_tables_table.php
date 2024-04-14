<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('do_referral_tables', function (Blueprint $table) {
            $table->softDeletes();
            $table->id();
            $table->bigInteger('social_worker_form_id')->unsigned();
            $table->foreign('social_worker_form_id')->references('id')->on('social_worker_forms');
            $table->smallInteger('do_referral')->unsigned()->nullable();
            $table->timestamps();

        });

        Schema::table('social_worker_forms', function (Blueprint $table) {
            $table->dropColumn([
                'do_referral',
            ]);
        });
    }

    public function down()
    {
        Schema::dropIfExists('do_referral_tables');
        Schema::table('social_worker_forms', function (Blueprint $table) {
            $table->smallInteger('do_referral')->unsigned()->nullable();
        });
    }
};
