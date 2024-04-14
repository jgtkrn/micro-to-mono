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
        Schema::table('social_background_forms', function (Blueprint $table) {
            $table->dropColumn('financial_state');
        });
        Schema::create('financial_state_tables', function (Blueprint $table) {
            $table->softDeletes();
            $table->id();
            $table->bigInteger('social_background_form_id')->unsigned();
            $table->foreign('social_background_form_id')->references('id')->on('social_background_forms');
            $table->smallInteger('financial_state')->unsigned()->nullable();
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
        Schema::table('social_background_forms', function (Blueprint $table) {
            $table->smallInteger('financial_state')->unsigned()->nullable();
        });
        Schema::dropIfExists('financial_state_tables');
    }
};
