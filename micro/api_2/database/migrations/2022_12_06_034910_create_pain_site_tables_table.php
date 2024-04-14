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
        Schema::create('pain_site_tables', function (Blueprint $table) {
            $table->softDeletes();
            $table->id();
            $table->bigInteger('physical_condition_form_id')->unsigned();
            $table->foreign('physical_condition_form_id')->references('id')->on('physical_condition_forms');
            $table->boolean('is_pain')->nullable();
            $table->string('provoking_factor')->nullable();
            $table->string('pain_location1')->nullable();
            $table->boolean('is_dull')->nullable();
            $table->boolean('is_achy')->nullable();
            $table->boolean('is_sharp')->nullable();
            $table->boolean('is_stabbing')->nullable();
            $table->enum('stabbing_option', ['constant', 'intermittent'])->nullable();
            $table->string('pain_location2')->nullable();
            $table->boolean('is_relief')->nullable();
            $table->string('what_relief')->nullable();
            $table->boolean('have_relief_method')->nullable();
            $table->smallInteger('relief_method')->unsigned()->nullable();
            $table->string('other_relief_method')->nullable();
            $table->smallInteger('pain_scale')->unsigned()->nullable();
            $table->string('when_pain')->nullable();
            $table->boolean('affect_adl')->nullable();
            $table->string('adl_info')->nullable();
            $table->string('pain_remark')->nullable();
            $table->json('created_by')->nullable();
            $table->json('updated_by')->nullable();
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
        Schema::dropIfExists('pain_site_tables');
    }
};
