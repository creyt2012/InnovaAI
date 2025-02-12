<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('landing_pages', function (Blueprint $table) {
            $table->id();
            $table->string('hero_title');
            $table->text('hero_description');
            $table->string('hero_image')->nullable();
            $table->json('features')->nullable();
            $table->json('testimonials')->nullable();
            $table->string('pricing_title')->nullable();
            $table->text('pricing_description')->nullable();
            $table->string('contact_email');
            $table->json('social_links')->nullable();
            $table->text('footer_text')->nullable();
            $table->string('meta_title');
            $table->text('meta_description');
            $table->text('custom_css')->nullable();
            $table->text('custom_js')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('landing_pages');
    }
}; 