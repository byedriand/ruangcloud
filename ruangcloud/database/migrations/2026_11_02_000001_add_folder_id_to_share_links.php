<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('share_links', function (Blueprint $table) {
            $table->unsignedBigInteger('file_id')->nullable()->change();
            $table->foreignId('folder_id')->nullable()->after('file_id')->constrained()->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('share_links', function (Blueprint $table) {
            $table->dropConstrainedForeignId('folder_id');
            $table->unsignedBigInteger('file_id')->nullable(false)->change();
        });
    }
};
