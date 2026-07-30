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
        Schema::create('resume_templates', function (Blueprint $table): void {
            $table->id();
            $table->string('slug')->unique();
            $table->string('renderer_key')->index();
            $table->string('name');
            $table->text('short_desc')->nullable();
            $table->string('thumbnail_path')->nullable();
            $table->string('accent_color', 7);
            $table->unsignedTinyInteger('allows_profile_photo')->default(1)->comment('1-Yes, 2-No');
            $table->unsignedTinyInteger('is_ats_friendly')->default(1)->comment('1-Yes, 2-No');
            $table->unsignedTinyInteger('is_featured')->default(2)->comment('1-Yes, 2-No');
            $table->unsignedTinyInteger('status')->default(2)->comment('1-Active, 2-Inactive');
            $table->unsignedInteger('position')->default(0);
            $table->timestamps();

            $table->index(['status', 'position']);
            $table->index(['is_featured', 'status']);
        });

        Schema::create('category_resume_template', function (Blueprint $table): void {
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();
            $table->foreignId('resume_template_id')->constrained()->cascadeOnDelete();
            $table->unique(['category_id', 'resume_template_id']);
        });

        $now = now();
        $templates = [
            ['template-one', 'Professional Cyan', 'A crisp two-column layout for developers and technical professionals.', '#00B6CE', 'assets/resume-templates/template-one.png'],
            ['template-two', 'Classic Blue Sidebar', 'A refined engineering resume with a spacious experience column and structured blue sidebar.', '#075A9E', 'assets/resume-templates/template-two.png'],
            ['template-three', 'Modern Mint Professional', 'A confident two-column resume with mint highlights, icon-led sections, and detailed experience.', '#0E594D', 'assets/resume-templates/template-three.png'],
            ['template-four', 'Teal Impact', 'A bold full-height teal sidebar paired with a spacious, achievement-focused professional layout.', '#087671', 'assets/resume-templates/template-four.png'],
            ['template-five', 'Structured Indigo', 'A polished photo-led resume with indigo accents, skill bars, and a structured two-column layout.', '#39436B', 'assets/resume-templates/template-five.jpg'],
            ['template-six', 'Indigo Profile Sidebar', 'A confident photo-led resume with a full-height indigo sidebar, visual skill bars, and language ratings.', '#303B69', 'assets/resume-templates/template-six.jpg'],
        ];

        DB::table('resume_templates')->insert(array_map(
            static fn (array $template, int $position): array => [
                'slug' => $template[0],
                'renderer_key' => $template[0],
                'name' => $template[1],
                'short_desc' => $template[2],
                'thumbnail_path' => $template[4],
                'accent_color' => $template[3],
                'allows_profile_photo' => 1,
                'is_ats_friendly' => 1,
                'is_featured' => $position < 3 ? 1 : 2,
                'status' => 1,
                'position' => $position,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            $templates,
            array_keys($templates),
        ));
    }

    public function down(): void
    {
        Schema::dropIfExists('category_resume_template');
        Schema::dropIfExists('resume_templates');
    }
};
