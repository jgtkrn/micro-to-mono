<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('social_background_forms', function (Blueprint $table) {
            $table->text('other_living_status')->nullable();
            $table->text('relationship_other')->nullable();
            $table->text('financial_state_other')->nullable();
            $table->text('religion_remark')->nullable();
        });
    }

    public function down()
    {
        Schema::table('social_background_forms', function (Blueprint $table) {
            $table->dropColumn([
                'other_living_status',
                'relationship_other',
                'financial_state_other',
                'religion_remark',
            ]);
        });
    }
};
