<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('security_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained();
            $table->string('action');
            $table->string('ip_address');
            $table->string('user_agent')->nullable();
            $table->json('details')->nullable();
            $table->boolean('is_suspicious')->default(false);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('security_logs');
    }
}; 