<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('function_mobility_forms', function (Blueprint $table) {
            $table->boolean('mi_independent')->nullable();
            $table->boolean('mi_walk_assisst')->nullable();
            $table->boolean('mi_wheelchair_bound')->nullable();
            $table->boolean('mi_bed_bound')->nullable();
            $table->text('mi_remark')->nullable();
            $table->boolean('mo_independent')->nullable();
            $table->boolean('mo_walk_assisst')->nullable();
            $table->boolean('mo_wheelchair_bound')->nullable();
            $table->boolean('mo_bed_bound')->nullable();
            $table->text('mo_remark')->nullable();
        });
    }

    public function down()
    {
        Schema::table('function_mobility_forms', function (Blueprint $table) {
            $table->dropColumn([
                'mi_independent',
                'mi_walk_assisst',
                'mi_wheelchair_bound',
                'mi_bed_bound',
                'mi_remark',
                'mo_independent',
                'mo_walk_assisst',
                'mo_wheelchair_bound',
                'mo_bed_bound',
                'mo_remark',
            ]);
        });
    }
};
