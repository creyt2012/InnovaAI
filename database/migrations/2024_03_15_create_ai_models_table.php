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
            $table->foreignId('server_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('path'); // Đường dẫn tới model trên LMStudio server
            $table->text('description')->nullable();
            $table->string('category'); // Base, Chat, Code, etc.
            $table->json('parameters')->nullable(); // Các thông số của model
            $table->integer('context_length')->default(4096);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('ai_models');
    }
}; 