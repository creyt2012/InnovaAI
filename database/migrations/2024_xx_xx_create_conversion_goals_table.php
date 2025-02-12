<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('conversion_goals', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('type', ['pageview', 'event', 'custom']);
            $table->string('target');
            $table->json('conditions')->nullable();
            $table->decimal('value', 10, 2)->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('conversions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('goal_id')->constrained('conversion_goals')->onDelete('cascade');
            $table->foreignId('visitor_id')->constrained('visitor_analytics')->onDelete('cascade');
            $table->decimal('value', 10, 2)->default(0);
            $table->json('data')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('conversions');
        Schema::dropIfExists('conversion_goals');
    }
}; 