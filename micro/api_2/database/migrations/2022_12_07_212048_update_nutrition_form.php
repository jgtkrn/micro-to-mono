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
        Schema::table('physical_condition_forms', function (Blueprint $table) {
            $table->dropColumn([
                'dat',
                'is_special_diet',
                'is_special_feeding',
                'is_intact',
                'is_abnormal_skin',
                'bowel_normal',
                'bowel_abnormal',
                'urine_normal',
                'urine_abnormal',
                'urine_incontinence'
            ]);
        });
        
        Schema::table('physical_condition_forms', function (Blueprint $table) {
            // nutrition
            $table->enum('dat_special_diet', [ 1 , 2 ])->nullable();
            $table->enum('is_special_feeding', [ 1 , 2 ])->nullable();

            // skin
            $table->enum('intact_abnormal', [ 1 , 2 ])->nullable();

            //elimination
            $table->enum('bowel_habit', [ 1 , 2 ])->nullable();
            $table->enum('urinary_habit', [ 1 , 2 , 3 ])->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('physical_condition_forms', function (Blueprint $table) {
            $table->boolean('dat')->nullable();
            $table->boolean('is_special_diet')->nullable();
            $table->boolean('is_special_feeding')->nullable();
            $table->boolean('is_intact')->nullable();
            $table->boolean('is_abnormal_skin')->nullable();
            $table->boolean('bowel_normal')->nullable();
            $table->boolean('bowel_abnormal')->nullable();
            $table->boolean('urine_normal')->nullable();
            $table->boolean('urine_abnormal')->nullable();
            $table->boolean('urine_incontinence')->nullable();
        });
        
        Schema::table('physical_condition_forms', function (Blueprint $table) {
            $table->dropColumn([
                'dat_special_diet',
                'intact_abnormal',
                'bowel_habit',
                'urinary_habit'
            ]);
        });
    }
};
