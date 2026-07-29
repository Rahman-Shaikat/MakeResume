<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('resumes', function (Blueprint $table): void {
            $table->index(['user_id', 'updated_at'], 'resumes_user_updated_at_index');
            $table->dropUnique('resumes_user_id_unique');
        });
    }

    public function down(): void
    {
        Schema::table('resumes', function (Blueprint $table): void {
            $table->unique('user_id');
            $table->dropIndex('resumes_user_updated_at_index');
        });
    }
};
