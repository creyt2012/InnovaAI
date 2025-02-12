<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('lm_studio_apis', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('endpoint');
            $table->string('api_key');
            $table->string('model');
            $table->integer('max_tokens')->default(2048);
            $table->float('temperature')->default(0.7);
            $table->enum('status', ['active', 'inactive'])->default('inactive');
            $table->integer('priority')->default(0);
            $table->integer('rate_limit')->default(60);
            $table->integer('timeout')->default(30);
            $table->timestamp('last_check')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('lm_studio_apis');
    }
}; 