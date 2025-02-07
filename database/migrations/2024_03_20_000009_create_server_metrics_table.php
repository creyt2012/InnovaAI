<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('server_metrics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('server_id')->constrained()->onDelete('cascade');
            $table->float('cpu_usage')->nullable(); // Phần trăm CPU
            $table->float('memory_usage')->nullable(); // Phần trăm RAM
            $table->float('disk_usage')->nullable(); // Phần trăm ổ đĩa
            $table->integer('active_connections')->default(0);
            $table->integer('requests_per_minute')->default(0);
            $table->float('response_time')->nullable(); // ms
            $table->json('additional_metrics')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('server_metrics');
    }
}; 