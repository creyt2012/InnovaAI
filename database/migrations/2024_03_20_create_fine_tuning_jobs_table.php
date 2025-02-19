<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('fine_tuning_jobs', function (Blueprint $table) {
            $table->id();
            $table->string('model_id');
            $table->string('status');
            $table->json('training_config');
            $table->json('metrics')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('fine_tuning_jobs');
    }
}; 