<?php

namespace App\Services;

use App\Repositories\EmployeesRepository;
use App\Repositories\VacationsRepository;

class VacationsService
{
    protected VacationsRepository $repository;
    protected NotificationsService $notifications;
    protected EmployeesRepository $employees;

    public function __construct(
        VacationsRepository $repository,
        NotificationsService $notifications,
        EmployeesRepository $employees
    ) {
        $this->repository = $repository;
        $this->notifications = $notifications;
        $this->employees = $employees;
    }
    //---------------
    public function createVacation(array $data): bool
    {
        $companyId = (int) ($data['company_id'] ?? session('company_id'));
        $data['company_id'] = $companyId;
        $employeeId = (int) ($data['employee_id'] ?? 0);

        if (empty($data['status'])) {
            $data['status'] = 'Pending HR';
        }
        if (empty($data['date'])) {
            $data['date'] = now()->toDateString();
        }

        $vacationId = $this->repository->create($data);
        if ($vacationId <= 0) {
            return false;
        }

        $from = $data['from_date'] ?? '';
        $to = $data['to_date'] ?? '';
        $period = trim($from . ' - ' . $to, ' -');
        $message = $period !== ''
            ? __('pages.notif_vacation_body_period', ['period' => $period])
            : __('pages.notif_vacation_body');

        if ($employeeId > 0) {
            $this->notifications->notify(
                $companyId,
                'employee',
                $employeeId,
                'vacation_created',
                __('pages.notif_vacation_title'),
                $message . ' ' . __('pages.notif_pending'),
                route('vacations')
            );
        }

        $this->notifications->notify(
            $companyId,
            'company',
            $companyId,
            'vacation_request',
            __('pages.notif_vacation_new'),
            $message,
            route('vacations')
        );

        $hrEmployees = $this->employees->getEmployeesByRole($companyId, 'hr');
        foreach ($hrEmployees as $hr) {
            if ((int) $hr->id === $employeeId) {
                continue;
            }

            $this->notifications->notify(
                $companyId,
                'employee',
                (int) $hr->id,
                'vacation_request',
                __('pages.notif_vacation_new'),
                $message . ' ' . __('pages.notif_pending'),
                route('vacations')
            );
        }

        return true;
    }
    //---------------
    public function getVacations(int $limit, string $search = '', array $statuses = [], ?int $companyId = null, ?int $employeeId = null, ?string $type = null)
    {
        $companyId = $companyId ?: (int) session('company_id');
        $limit = $limit ?: 10;

        if (in_array('Pending HR', $statuses, true)) {
            $statuses[] = 'Pending Manager';
            $statuses[] = 'Reviewing';
        }

        if (!empty($search) || !empty($statuses) || $employeeId || !empty($type)) {
            return $this->repository->getSearchedVacations($limit, $companyId, $search, $statuses, $employeeId, $type);
        }

        return $this->repository->getVacations($limit, $companyId, $employeeId, $type);
    }
    //---------------
    public function getVacationStatistics(string $search = '', ?int $companyId = null, ?int $employeeId = null): object
    {
        $companyId = $companyId ?: (int) session('company_id');
        return $this->repository->getVacationStatistics($companyId, $search, $employeeId);
    }
    //---------------
    public function getPendingForDashboard(int $companyId, int $limit = 5)
    {
        return $this->repository->getPendingForDashboard($companyId, $limit);
    }
    //---------------
    public function generateEmployeeReport(string $fromDate, string $toDate, int $employeeId): object
    {
        $companyId = (int) session('company_id');

        return $this->repository->getEmployeeVacationStatistics($companyId, $employeeId, $fromDate, $toDate);
    }
    //---------------
    public function getEmployeeReportEntries(string $fromDate, string $toDate, int $employeeId, int $limit = 10, string $cursorName = 'vacations_cursor')
    {
        $companyId = (int) session('company_id');

        return $this->repository->getEmployeeVacationEntries($companyId, $employeeId, $fromDate, $toDate, $limit, $cursorName);
    }
    //---------------
    public function findOrFail(int $id, ?int $companyId = null): bool
    {
        $companyId = $companyId ?: (int) session('company_id');
        return $this->repository->checkVacationExist($id, $companyId);
    }
    //---------------
    public function getVacationById(int $id, ?int $companyId = null)
    {
        $companyId = $companyId ?: (int) session('company_id');
        return $this->repository->findVacation($id, $companyId);
    }
    //---------------
    public function updateVacation(int $id, array $data, ?int $companyId = null): bool
    {
        $companyId = $companyId ?: (int) session('company_id');
        $vacation = $this->repository->findVacation($id, $companyId);
        if (!$vacation) {
            return false;
        }

        return $this->repository->update($id, $data, $companyId);
    }
    //---------------
    public function deleteVacation(int $id, ?int $companyId = null): bool
    {
        $companyId = $companyId ?: (int) session('company_id');
        $vacation = $this->repository->findVacation($id, $companyId);
        if (!$vacation) {
            return false;
        }

        return $this->repository->delete($id, $companyId);
    }
    //---------------
    public function updateVacationStatus(int $id, string $status, string $role, ?int $companyId = null): bool
    {
        $companyId = $companyId ?: (int) session('company_id');
        $vacation = $this->repository->findVacation($id, $companyId);
        if (!$vacation) {
            return false;
        }

        $status = urldecode($status);
        $current = in_array((string) $vacation->status, ['Pending Manager', 'Reviewing'], true)
            ? 'Pending HR'
            : (string) $vacation->status;

        if ($current === $status) {
            return true;
        }

        $allowed = false;
        if (in_array($role, ['company', 'manager', 'hr'], true)) {
            if (in_array($current, ['Pending HR', 'Pending Manager', 'Reviewing'], true)
                && in_array($status, ['Approved', 'Refused'], true)) {
                $allowed = true;
            }
        }

        if (!$allowed) {
            return false;
        }

        $updated = $this->repository->updateStatus($id, $status, $companyId);
        if (!$updated) {
            return false;
        }

        $employeeId = (int) ($vacation->employee_id ?? 0);
        if ($employeeId <= 0) {
            return true;
        }

        $period = trim(($vacation->from_date ?? '') . ' - ' . ($vacation->to_date ?? ''), ' -');

        if ($status === 'Approved') {
            $this->notifications->notify(
                $companyId,
                'employee',
                $employeeId,
                'vacation_status',
                __('pages.notif_vacation_approved'),
                __('pages.notif_vacation_approved_body', ['period' => $period]),
                route('vacations')
            );
        } elseif ($status === 'Refused') {
            $this->notifications->notify(
                $companyId,
                'employee',
                $employeeId,
                'vacation_status',
                __('pages.notif_vacation_refused'),
                __('pages.notif_vacation_refused_body', ['period' => $period]),
                route('vacations')
            );
        }

        return true;
    }
}
