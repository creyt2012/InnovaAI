<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('user_preferences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('theme')->default('light');
            $table->string('language')->default('vi');
            $table->string('font_size')->default('medium');
            $table->boolean('notification_enabled')->default(true);
            $table->json('keyboard_shortcuts')->nullable();
            $table->string('display_mode')->default('default');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('user_preferences');
    }
}; 