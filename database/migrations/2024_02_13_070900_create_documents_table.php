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
            $table->string('document_name')->index();
            $table->string('location')->index();
            $table->enum('type', ['lost', 'found']);
            $table->foreignId('claim_user_id')->nullable()->constrained('users');
            $table->enum('status', ['pending', 'approved', 'rejected']);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};
