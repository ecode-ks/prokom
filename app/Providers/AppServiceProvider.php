<?php

namespace App\Providers;

use App\Services\ProjectsService;
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
        View::composer('template.template', function ($view) {
            $topbarProjects = collect();
            $selectedProjectId = session('project_id');
            $selectedProjectName = null;

            $companyId = session('company_id');
            if ($companyId && (session('role') === 'company' || session('role') === 'manager')) {
                $topbarProjects = app(ProjectsService::class)->getAllProjects($companyId);

                if ($selectedProjectId) {
                    $selected = $topbarProjects->firstWhere('id', (int) $selectedProjectId);
                    $selectedProjectName = $selected->project_name ?? null;

                    if (!$selected) {
                        session()->forget('project_id');
                        $selectedProjectId = null;
                    }
                }
            }

            $view->with([
                'topbarProjects' => $topbarProjects,
                'selectedProjectId' => $selectedProjectId,
                'selectedProjectName' => $selectedProjectName,
            ]);
        });
    }
}
