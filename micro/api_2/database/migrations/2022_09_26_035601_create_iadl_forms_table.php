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
        Schema::create('iadl_forms', function (Blueprint $table) {
            $table->softDeletes();
            $table->id();
            $table->bigInteger('assessment_case_id')->unsigned();
            $table->foreign('assessment_case_id')->references('id')->on('assessment_cases');

            $table->string('elderly_central_ref_number')->nullable();
            $table->date('assessment_date')->nullable();
            $table->string('assessor_name')->nullable();
            $table->smallInteger('assessment_kind')->unsigned()->nullable();

            $table->smallInteger('can_use_phone')->unsigned()->nullable();
            $table->string('text_use_phone')->nullable();
            $table->smallInteger('can_take_ride')->unsigned()->nullable();
            $table->string('text_take_ride')->nullable();
            $table->smallInteger('can_buy_food')->unsigned()->nullable();
            $table->string('text_buy_food')->nullable();
            $table->smallInteger('can_cook')->unsigned()->nullable();
            $table->string('text_cook')->nullable();
            $table->smallInteger('can_do_housework')->unsigned()->nullable();
            $table->string('text_do_housework')->nullable();
            $table->smallInteger('can_do_repairment')->unsigned()->nullable();
            $table->string('text_do_repairment')->nullable();
            $table->smallInteger('can_do_laundry')->unsigned()->nullable();
            $table->string('text_do_laundry')->nullable();
            $table->smallInteger('can_take_medicine')->unsigned()->nullable();
            $table->string('text_take_medicine')->nullable();
            $table->smallInteger('can_handle_finances')->unsigned()->nullable();
            $table->string('text_handle_finances')->nullable();
            $table->integer('iadl_total_score')->nullable();

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
        Schema::dropIfExists('iadl_forms');
    }
};
