<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('bzn_care_targets', function (Blueprint $table) {
            $table->smallInteger('ct_domain')->unsigned()->nullable();
            $table->smallInteger('ct_urgency')->unsigned()->nullable();
            $table->smallInteger('ct_category')->unsigned()->nullable();
            $table->string('ct_area')->nullable();
            $table->smallInteger('ct_priority')->unsigned()->nullable();
            $table->string('ct_target')->nullable();
            $table->smallInteger('ct_modifier')->unsigned()->nullable();
            $table->string('ct_ssa')->nullable();
            $table->smallInteger('ct_knowledge')->unsigned()->nullable();
            $table->smallInteger('ct_behaviour')->unsigned()->nullable();
            $table->smallInteger('ct_status')->unsigned()->nullable();
        });
    }

    public function down()
    {
        Schema::table('bzn_care_targets', function (Blueprint $table) {
            $table->dropColumn([
                'ct_area',
                'ct_target',
                'ct_ssa',
                'ct_domain',
                'ct_urgency',
                'ct_category',
                'ct_priority',
                'ct_modifier',
                'ct_knowledge',
                'ct_behaviour',
                'ct_status',
            ]);
        });
    }
};
