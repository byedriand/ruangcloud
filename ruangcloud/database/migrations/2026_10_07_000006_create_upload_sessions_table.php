<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('upload_sessions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('folder_id')->nullable()->constrained()->nullOnDelete();
            $table->string('file_name');
            $table->string('mime')->nullable();
            $table->unsignedBigInteger('file_size');
            $table->char('fingerprint', 64);
            $table->unsignedInteger('chunk_size');
            $table->unsignedInteger('total_chunks');
            $table->unsignedInteger('received_count')->default(0);
            $table->string('status', 20)->default('uploading'); // uploading | assembling | completed | failed | cancelled | expired
            $table->text('error')->nullable();
            $table->foreignId('file_id')->nullable()->constrained()->nullOnDelete();
            // WAJIB dateTime, BUKAN timestamp: di MariaDB (XAMPP) kolom TIMESTAMP NOT NULL
            // pertama tanpa default eksplisit diam-diam mendapat ON UPDATE CURRENT_TIMESTAMP,
            // sehingga expires_at tertimpa jam sekarang setiap baris di-update (per chunk)
            // dan sesi dianggap kedaluwarsa pada chunk berikutnya.
            $table->dateTime('expires_at');
            $table->timestamp('last_chunk_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'fingerprint']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('upload_sessions');
    }
};
