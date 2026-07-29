<?php

namespace App\Providers;

use App\Models\Resume;
use App\Models\ResumeSection;
use App\Models\ResumeSectionItem;
use App\Policies\ResumePolicy;
use App\Policies\ResumeSectionItemPolicy;
use App\Policies\ResumeSectionPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::policy(Resume::class, ResumePolicy::class);
        Gate::policy(ResumeSection::class, ResumeSectionPolicy::class);
        Gate::policy(ResumeSectionItem::class, ResumeSectionItemPolicy::class);
    }
}
