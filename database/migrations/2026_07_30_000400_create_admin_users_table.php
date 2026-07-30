<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('admin_users', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('role_id')->constrained()->restrictOnDelete();
            $table->unsignedBigInteger('country_id')->default(0);
            $table->string('name', 100);
            $table->string('email', 100)->unique();
            $table->string('address')->nullable();
            $table->unsignedTinyInteger('gender')->default(0)->comment('0 = Unspecified, 1 = Male, 2 = Female, 3 = Other');
            $table->string('phone', 20)->nullable()->unique();
            $table->string('image_path')->nullable();
            $table->string('password');
            $table->unsignedTinyInteger('status')->default(1)->comment('1 = Active, 2 = Inactive');
            $table->unsignedTinyInteger('is_super')->default(2)->comment('1 = Yes, 2 = No');
            $table->timestamp('email_verified_at')->nullable();
            $table->rememberToken();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->nullable();

            $table->index(['role_id', 'status']);
            $table->index(['is_super', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('admin_users');
    }
};
