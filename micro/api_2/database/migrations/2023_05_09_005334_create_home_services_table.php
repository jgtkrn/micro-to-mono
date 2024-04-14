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
        Schema::table('social_worker_forms', function (Blueprint $table) {
            $table->dropColumn([
                'home_service'
            ]);
        });
        Schema::create('home_services', function (Blueprint $table) {
            $table->softDeletes();
            $table->id();
            $table->bigInteger('social_worker_form_id')->unsigned();
            $table->foreign('social_worker_form_id')->references('id')->on('social_worker_forms');
            $table->smallInteger('home_service')->unsigned()->nullable();
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
        Schema::table('social_worker_forms', function (Blueprint $table) {
            $table->enum('home_service', [1, 2, 3, 4, 5, 6, 7])->nullable();
        });
        Schema::dropIfExists('home_services');
    }
};
