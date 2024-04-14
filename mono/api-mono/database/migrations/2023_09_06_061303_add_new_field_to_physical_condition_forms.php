<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('physical_condition_forms', function (Blueprint $table) {
            $table->text('other_emotional_state')->nullable();
            $table->boolean('deaf_right')->nullable();
            $table->boolean('deaf_left')->nullable();
            $table->text('sensory_remark')->nullable();
            $table->text('other_radiation')->nullable();
            $table->boolean('skin_rash')->nullable();
            $table->text('other_skin_rash')->nullable();
            $table->text('bowel_remark')->nullable();
            $table->text('urine_remark')->nullable();
        });
    }

    public function down()
    {
        Schema::table('physical_condition_forms', function (Blueprint $table) {
            $table->dropColumn([
                'other_emotional_state',
                'deaf_right',
                'deaf_left',
                'sensory_remark',
                'other_radiation',
                'skin_rash',
                'other_skin_rash',
                'bowel_remark',
                'urine_remark',
            ]);
        });
    }
};
