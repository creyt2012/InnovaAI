<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('ai_models', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('endpoint');
            $table->string('api_key')->nullable();
            $table->string('type')->default('lmstudio');
            $table->string('status')->default('active');
            $table->integer('priority')->default(0);
            $table->integer('max_tokens')->default(2000);
            $table->float('temperature')->default(0.7);
            $table->integer('context_length')->default(4096);
            $table->timestamps();
        });

        // Add preferred_model_id to users table
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('preferred_model_id')
                  ->nullable()
                  ->constrained('ai_models')
                  ->nullOnDelete();
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['preferred_model_id']);
            $table->dropColumn('preferred_model_id');
        });
        Schema::dropIfExists('ai_models');
    }
}; 