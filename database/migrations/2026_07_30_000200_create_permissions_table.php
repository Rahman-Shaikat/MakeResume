<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('permissions', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('group_id')->constrained('permission_groups')->cascadeOnDelete();
            $table->unsignedBigInteger('parent_id')->default(0);
            $table->string('name', 100);
            $table->string('meta_name', 100)->unique();
            $table->string('short_desc')->nullable();
            $table->unsignedTinyInteger('type')->default(1)->comment('1 = Admin, 3 = User');
            $table->unsignedTinyInteger('status')->default(1)->comment('1 = Active, 2 = Inactive');
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->nullable();

            $table->index(['group_id', 'parent_id', 'type', 'status'], 'permissions_hierarchy_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('permissions');
    }
};
