<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->string('resume_limit_mode', 20)->default('inherit')->after('password');
            $table->unsignedInteger('resume_limit')->nullable()->after('resume_limit_mode');
        });

        DB::table('users')->update([
            'resume_limit_mode' => 'unlimited',
            'resume_limit' => null,
        ]);
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->dropColumn(['resume_limit_mode', 'resume_limit']);
        });
    }
};
