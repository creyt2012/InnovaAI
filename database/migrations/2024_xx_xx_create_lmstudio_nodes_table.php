<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('lmstudio_nodes', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('url');
            $table->boolean('is_active')->default(true);
            $table->integer('priority')->default(0);
            $table->integer('max_tokens')->default(2000);
            $table->float('temperature')->default(0.7);
            $table->integer('timeout')->default(30);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('lmstudio_nodes');
    }
}; 