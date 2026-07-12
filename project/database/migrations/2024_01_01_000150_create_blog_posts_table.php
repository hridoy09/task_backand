<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('blog_posts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('admin_id')->default(0);
            $table->unsignedBigInteger('category_id')->nullable();
            $table->string('title');
            $table->text('body');
            $table->string('image')->nullable();
            $table->string('thumb')->nullable();
            $table->timestamps();
            $table->string('slug')->nullable();
            $table->unsignedBigInteger('views')->default(0)->comment('total views of this post');
            $table->tinyInteger('status')->default(0)->comment('0=unpublished, 1=published');
            $table->text('seo_content')->nullable();
            $table->string('seo_image')->nullable();

            $table->index('status');
            $table->index('admin_id');
            $table->unique('slug');

            $table->foreign('category_id')
                ->references('id')
                ->on('blog_categories')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('blog_posts');
    }
};
