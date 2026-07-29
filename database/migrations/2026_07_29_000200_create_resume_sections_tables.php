<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('resume_sections', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('resume_id')->constrained()->cascadeOnDelete();
            $table->string('section_key', 64);
            $table->string('type', 40);
            $table->string('title', 100);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->boolean('is_custom')->default(false);
            $table->boolean('is_visible')->default(true);
            $table->timestamps();

            $table->unique(['resume_id', 'section_key']);
            $table->index(['resume_id', 'sort_order']);
        });

        Schema::create('resume_section_items', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('resume_section_id')->constrained()->cascadeOnDelete();
            $table->json('data');
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index(['resume_section_id', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('resume_section_items');
        Schema::dropIfExists('resume_sections');
    }
};
