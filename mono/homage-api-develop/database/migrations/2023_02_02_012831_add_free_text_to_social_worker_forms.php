<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('social_worker_forms', function (Blueprint $table) {
            $table->string('social_fa')->nullable();
            $table->string('social_rs')->nullable();
            $table->string('stratification_fa')->nullable();
            $table->string('stratification_rs')->nullable();
            $table->string('psycho_fa')->nullable();
            $table->string('psycho_rs')->nullable();
            $table->string('cognitive_fa')->nullable();
            $table->string('cognitive_rs')->nullable();
        });
    }

    public function down()
    {
        Schema::table('social_worker_forms', function (Blueprint $table) {
            $table->dropColumn([
                'social_fa',
                'social_rs',
                'stratification_fa',
                'stratification_rs',
                'psycho_fa',
                'psycho_rs',
                'cognitive_fa',
                'cognitive_rs',
            ]);
        });
    }
};
