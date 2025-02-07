<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('preferred_model_id')->nullable()->constrained('ai_models');
            $table->json('model_parameters')->nullable();
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['preferred_model_id']);
            $table->dropColumn(['preferred_model_id', 'model_parameters']);
        });
    }
}; 