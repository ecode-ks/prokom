<?php

namespace App\Providers;

use App\Services\NotificationsService;
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
            $topbarNotifications = collect();
            $topbarUnreadNotifications = 0;

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

            if ($companyId && session('role') && session('role') !== 'admin') {
                try {
                    $notificationsService = app(NotificationsService::class);
                    $topbarNotifications = $notificationsService->getLatest(8);
                    $topbarUnreadNotifications = $notificationsService->countUnread();
                } catch (\Throwable $e) {
                    $topbarNotifications = collect();
                    $topbarUnreadNotifications = 0;
                }
            }

            $view->with([
                'topbarProjects' => $topbarProjects,
                'selectedProjectId' => $selectedProjectId,
                'selectedProjectName' => $selectedProjectName,
                'topbarNotifications' => $topbarNotifications,
                'topbarUnreadNotifications' => $topbarUnreadNotifications,
            ]);
        });
    }
}
