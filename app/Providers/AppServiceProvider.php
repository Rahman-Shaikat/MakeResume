<?php

declare(strict_types=1);

namespace App\Providers;

use App\Contracts\PackageResumeAllowanceProvider;
use App\Models\Resume;
use App\Models\ResumeSection;
use App\Models\ResumeSectionItem;
use App\Policies\ResumePolicy;
use App\Policies\ResumeSectionItemPolicy;
use App\Policies\ResumeSectionPolicy;
use App\Services\NullPackageResumeAllowanceProvider;
use App\View\Composers\AdminTopbarComposer;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(PackageResumeAllowanceProvider::class, NullPackageResumeAllowanceProvider::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if($this->app->environment('production')) {
            URL::forceScheme('https');
        }
        Gate::policy(Resume::class, ResumePolicy::class);
        Gate::policy(ResumeSection::class, ResumeSectionPolicy::class);
        Gate::policy(ResumeSectionItem::class, ResumeSectionItemPolicy::class);
        View::composer('admin.partials.topbar', AdminTopbarComposer::class);

        RateLimiter::for('contact-form', static fn (Request $request): Limit => Limit::perMinute(5)
            ->by((string) $request->ip()));
    }
}
