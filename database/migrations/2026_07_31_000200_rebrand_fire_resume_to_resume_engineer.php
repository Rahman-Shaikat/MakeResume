<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('roles')
            ->where('name', 'Super Admin')
            ->where('type', 1)
            ->where('short_desc', 'Full access to every FireResume administration feature.')
            ->update(['short_desc' => 'Full access to every Resume Engineer administration feature.']);
    }

    public function down(): void
    {
        DB::table('roles')
            ->where('name', 'Super Admin')
            ->where('type', 1)
            ->where('short_desc', 'Full access to every Resume Engineer administration feature.')
            ->update(['short_desc' => 'Full access to every FireResume administration feature.']);
    }
};
