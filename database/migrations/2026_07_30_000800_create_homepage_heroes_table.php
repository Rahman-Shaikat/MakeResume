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
        Schema::create('homepage_heroes', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->foreignId('resume_template_id')->nullable()->constrained()->nullOnDelete();
            $table->string('preview_image_path')->nullable();
            $table->string('eyebrow', 120);
            $table->string('headline');
            $table->text('description');
            $table->string('top_badge', 160);
            $table->string('editor_title', 160);
            $table->string('editor_description', 255);
            $table->string('bottom_status_title', 160);
            $table->string('bottom_status_text', 160);
            $table->unsignedTinyInteger('status')->default(2)->comment('1-Active, 2-Inactive');
            $table->timestamps();

            $table->index('status');
        });

        $templateId = DB::table('resume_templates')
            ->where('slug', 'template-one')
            ->value('id');

        DB::table('homepage_heroes')->insert([
            'name' => 'Primary homepage hero',
            'resume_template_id' => $templateId,
            'preview_image_path' => null,
            'eyebrow' => 'Your next role starts here',
            'headline' => 'Build a resume that makes your next move feel possible.',
            'description' => 'Choose a thoughtful template, shape every detail around your experience, and see a polished resume take form as you work.',
            'top_badge' => 'Designed around you',
            'editor_title' => 'Professional summary',
            'editor_description' => 'Clear, focused, and ready for the role you want next.',
            'bottom_status_title' => 'Saved automatically',
            'bottom_status_text' => 'Your progress is safe',
            'status' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('homepage_heroes');
    }
};
