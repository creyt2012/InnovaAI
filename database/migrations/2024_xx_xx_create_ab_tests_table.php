<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('ab_tests', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->json('variants');
            $table->string('target_metric');
            $table->dateTime('start_date');
            $table->dateTime('end_date')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('ab_test_participants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('test_id')->constrained('ab_tests')->onDelete('cascade');
            $table->foreignId('visitor_id')->constrained('visitor_analytics')->onDelete('cascade');
            $table->string('variant');
            $table->boolean('converted')->default(false);
            $table->decimal('conversion_value', 10, 2)->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('ab_test_participants');
        Schema::dropIfExists('ab_tests');
    }
}; 