<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('system_configs', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->string('group')->nullable();
            $table->string('type')->default('text');
            $table->boolean('is_public')->default(false);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('system_configs');
    }
}; 