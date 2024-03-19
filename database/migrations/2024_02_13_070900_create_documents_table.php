<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('documents', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('user_id')->constrained('users');
            $table->foreignUuid('category_id')->constrained('categories');
            $table->string('document_number')->index();
            $table->string('location')->index();
            $table->foreignId('claim_user_id')->nullable()->constrained('users');
            $table->enum('status', ['claimed', 'not_claimed'])->default('not_claimed');
            $table->enum('type', ['lost', 'found']);
            $table->foreignUuid('notification_id')->nullable()->constrained('notifications');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};
