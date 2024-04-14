<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('social_background_forms', function (Blueprint $table) {
            $table->dropColumn([
                'community_resource',
            ]);
        });
        Schema::create('community_resource_tables', function (Blueprint $table) {
            $table->softDeletes();
            $table->id();
            $table->bigInteger('social_background_form_id')->unsigned();
            $table->foreign('social_background_form_id')->references('id')->on('social_background_forms');
            $table->string('community_resource')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::table('social_background_forms', function (Blueprint $table) {
            $table->string('community_resource')->nullable();
        });
        Schema::dropIfExists('community_resource_tables');
    }
};
