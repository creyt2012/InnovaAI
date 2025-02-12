<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('analytics_alerts', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('metric');
            $table->enum('condition', ['above', 'below', 'change']);
            $table->decimal('threshold', 10, 2);
            $table->json('notification_channels');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('analytics_alert_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('alert_id')->constrained('analytics_alerts')->onDelete('cascade');
            $table->string('message');
            $table->json('data');
            $table->timestamp('triggered_at');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('analytics_alert_logs');
        Schema::dropIfExists('analytics_alerts');
    }
}; 