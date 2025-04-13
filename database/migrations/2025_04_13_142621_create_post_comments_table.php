<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('post_comments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('post_id')->constrained('posts')->onDelete('cascade');
            $table->foreignUuid('customer_id')->constrained('users')->onDelete('cascade');
            $table->foreignUuid('parent_id')->nullable()->constrained('post_comments')->onDelete('cascade');
            $table->longText('content');
            $table->boolean('is_visible')->default(true);
            $table->boolean('is_approved')->default(true);
            $table->foreignUuid('approved_by')->constrained('users')->onDelete('cascade');
            $table->softDeletes();
            $table->timestamps();
            $table->index(['post_id', 'is_visible', 'is_approved']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('post_comments');
    }
};
