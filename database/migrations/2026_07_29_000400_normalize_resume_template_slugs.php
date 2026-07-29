<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * @var array<string, string>
     */
    private const TEMPLATE_SLUGS = [
        'temp-1' => 'template-two',
        'temp-2' => 'template-three',
        'temp-3' => 'template-four',
    ];

    public function up(): void
    {
        foreach (self::TEMPLATE_SLUGS as $legacySlug => $templateSlug) {
            DB::table('resumes')
                ->where('template_slug', $legacySlug)
                ->update(['template_slug' => $templateSlug]);
        }
    }

    public function down(): void
    {
        foreach (array_flip(self::TEMPLATE_SLUGS) as $templateSlug => $legacySlug) {
            DB::table('resumes')
                ->where('template_slug', $templateSlug)
                ->update(['template_slug' => $legacySlug]);
        }
    }
};
