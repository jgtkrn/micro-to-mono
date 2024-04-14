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
                'elder_living',
            ]);
        });
        Schema::create('elder_livings', function (Blueprint $table) {
            $table->softDeletes();
            $table->id();
            $table->bigInteger('social_worker_form_id')->unsigned();
            $table->foreign('social_worker_form_id')->references('id')->on('social_worker_forms');
            $table->smallInteger('elder_living')->unsigned()->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::table('social_worker_forms', function (Blueprint $table) {
            $table->enum('elder_living', [1, 2, 3, 4, 5])->nullable();
        });
        Schema::dropIfExists('elder_livings');
    }
};
