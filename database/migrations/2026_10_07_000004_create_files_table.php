<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('files', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('folder_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->string('mime')->nullable();
            $table->unsignedBigInteger('size');
            $table->char('checksum_sha256', 64);
            $table->string('stored_path');                       // path di storage node (nama acak, tanpa ekstensi)
            $table->string('status', 20)->default('ready');
            $table->timestamps();

            $table->index(['user_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('files');
    }
};
