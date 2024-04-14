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
            $table->boolean('visual_impaired_left')->nullable();
            $table->boolean('visual_impaired_right')->nullable();
            $table->boolean('visual_impaired_both')->nullable();
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
            $table->dropColumn([
                'visual_impaired_left', 
                'visual_impaired_right',
                'visual_impaired_both'
            ]);
        });
    }
};
