<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('custom_events', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->json('data')->nullable();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->string('session_id')->nullable();
            $table->string('ip_address');
            $table->string('user_agent')->nullable();
            $table->timestamps();
            
            $table->index(['name', 'created_at']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('custom_events');
    }
}; 