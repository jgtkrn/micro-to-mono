<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('living_status_tables', function (Blueprint $table) {
            $table->softDeletes();
            $table->id();
            $table->bigInteger('social_background_form_id')->unsigned();
            $table->foreign('social_background_form_id')->references('id')->on('social_background_forms');

            $table->smallInteger('ls_options')->unsigned()->nullable();

            $table->json('created_by')->nullable();
            $table->json('updated_by')->nullable();
            $table->timestamps();
        });
        Schema::table('social_background_forms', function (Blueprint $table) {
            $table->dropColumn([
                'living_status',
            ]);
        });
    }

    public function down()
    {
        Schema::dropIfExists('living_status_tables');
        Schema::table('social_background_forms', function (Blueprint $table) {
            $table->smallInteger('living_status')->unsigned()->nullable();
        });
    }
};
