<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('file_replicas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('file_id')->constrained()->cascadeOnDelete();
            $table->foreignId('storage_node_id')->constrained()->cascadeOnDelete();
            $table->string('status', 20)->default('pending');    // pending | synced | failed
            $table->boolean('is_primary')->default(false);
            $table->string('last_error')->nullable();
            $table->timestamps();

            $table->unique(['file_id', 'storage_node_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('file_replicas');
    }
};
