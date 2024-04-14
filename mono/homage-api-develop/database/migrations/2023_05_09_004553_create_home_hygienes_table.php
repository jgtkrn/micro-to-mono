<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('social_worker_forms', function (Blueprint $table) {
            $table->dropColumn([
                'elder_home_hygiene',
            ]);
        });
        Schema::create('home_hygienes', function (Blueprint $table) {
            $table->softDeletes();
            $table->id();
            $table->bigInteger('social_worker_form_id')->unsigned();
            $table->foreign('social_worker_form_id')->references('id')->on('social_worker_forms');
            $table->smallInteger('elder_home_hygiene')->unsigned()->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::table('social_worker_forms', function (Blueprint $table) {
            $table->enum('elder_home_hygiene', [1, 2, 3, 4, 5, 6, 7])->nullable();
        });
        Schema::dropIfExists('home_hygienes');
    }
};
