<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('knowledge_bases', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('content');
            $table->string('source_type'); // file, url, text
            $table->string('source_url')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamp('last_trained_at')->nullable();
            $table->foreignId('author_id')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('knowledge_bases');
    }
}; 