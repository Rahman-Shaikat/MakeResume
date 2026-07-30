<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('homepage_template_showcases', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('eyebrow', 120);
            $table->string('headline');
            $table->string('cta_label', 120);
            $table->unsignedTinyInteger('status')->default(2)->comment('1-Active, 2-Inactive');
            $table->timestamps();

            $table->index('status');
        });

        Schema::create('homepage_template_showcase_resume_template', function (Blueprint $table): void {
            $table->foreignId('homepage_template_showcase_id');
            $table->foreignId('resume_template_id');
            $table->unsignedTinyInteger('position')->default(0);
            $table->unique(['homepage_template_showcase_id', 'resume_template_id'], 'homepage_showcase_template_unique');
            $table->foreign('homepage_template_showcase_id', 'home_showcase_template_showcase_fk')
                ->references('id')
                ->on('homepage_template_showcases')
                ->cascadeOnDelete();
            $table->foreign('resume_template_id', 'home_showcase_template_resume_fk')
                ->references('id')
                ->on('resume_templates')
                ->cascadeOnDelete();
        });

        $showcaseId = DB::table('homepage_template_showcases')->insertGetId([
            'name' => 'Primary template showcase',
            'eyebrow' => 'Designed to be read',
            'headline' => 'Choose a template that puts your experience in focus.',
            'cta_label' => 'Explore your workspace',
            'status' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $templateIds = DB::table('resume_templates')
            ->where('status', 1)
            ->orderByRaw('CASE WHEN is_featured = 1 THEN 0 ELSE 1 END')
            ->orderBy('position')
            ->orderBy('name')
            ->limit(4)
            ->pluck('id');

        if ($templateIds->isNotEmpty()) {
            DB::table('homepage_template_showcase_resume_template')->insert(
                $templateIds
                    ->values()
                    ->map(fn (int $templateId, int $position): array => [
                        'homepage_template_showcase_id' => $showcaseId,
                        'resume_template_id' => $templateId,
                        'position' => $position,
                    ])
                    ->all(),
            );
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('homepage_template_showcase_resume_template');
        Schema::dropIfExists('homepage_template_showcases');
    }
};
