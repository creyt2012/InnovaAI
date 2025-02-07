<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('query_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained();
            $table->text('query');
            $table->text('response')->nullable();
            $table->json('server_responses')->nullable();
            $table->integer('processing_time')->nullable();
            $table->enum('status', ['pending', 'processing', 'completed', 'failed']);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('query_logs');
    }
}; 