<?php

namespace App\Providers;

use App\Models\Employer;
use App\Models\FollowUp;
use App\Models\Job;
use App\Models\Portfolio;
use App\Models\Proposal;
use App\Observers\ProposalObserver;
use App\Policies\EmployerPolicy;
use App\Policies\FollowUpPolicy;
use App\Policies\JobPolicy;
use App\Policies\PortfolioPolicy;
use App\Policies\ProposalPolicy;
use App\Services\SettingsService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\View;
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
        Proposal::observe(ProposalObserver::class);

        Gate::policy(Proposal::class, ProposalPolicy::class);
        Gate::policy(Job::class, JobPolicy::class);
        Gate::policy(Employer::class, EmployerPolicy::class);
        Gate::policy(Portfolio::class, PortfolioPolicy::class);
        Gate::policy(FollowUp::class, FollowUpPolicy::class);

        View::composer('layouts.app', function ($view): void {
            if (! Auth::check()) {
                return;
            }

            $view->with([
                'connectsRemaining' => app(SettingsService::class)->getConnectsRemainingThisWeek(),
                'todayFollowUpsCount' => FollowUp::query()->dueToday()->pending()->count(),
            ]);
        });
    }
}
