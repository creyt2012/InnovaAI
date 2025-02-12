<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('plugins', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description');
            $table->string('version');
            $table->string('author');
            $table->decimal('price', 8, 2)->default(0);
            $table->enum('status', ['active', 'inactive', 'pending'])->default('inactive');
            $table->json('settings')->nullable();
            $table->json('permissions')->nullable();
            $table->string('icon')->nullable();
            $table->timestamps();
        });

        Schema::create('user_plugins', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('plugin_id')->constrained()->onDelete('cascade');
            $table->json('settings')->nullable();
            $table->timestamp('activated_at')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('user_plugins');
        Schema::dropIfExists('plugins');
    }
}; 