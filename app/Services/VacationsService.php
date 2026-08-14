<?php

namespace App\Services;

use App\Repositories\EmployeesRepository;
use App\Repositories\VacationsRepository;
use Carbon\Carbon;

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

        $employee = $employeeId > 0
            ? $this->employees->findEmployee($employeeId, $companyId)
            : null;
        $employeeRole = $employee->role ?? '';

        // Vetëm punëtori (employee) pret aprovimin e menaxherit; të tjerët shkojnë te HR
        if (empty($data['status'])) {
            $data['status'] = $employeeRole === 'employee'
                ? 'Pending Manager'
                : 'Pending HR';
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
            ? 'Është regjistruar një kërkesë pushimi (' . $period . ').'
            : 'Është regjistruar një kërkesë pushimi.';

        if ($employeeId > 0) {
            $this->notifications->notify(
                $companyId,
                'employee',
                $employeeId,
                'vacation_created',
                'Kërkesë pushimi',
                $message,
                route('vacations')
            );
        }

        $this->notifications->notify(
            $companyId,
            'company',
            $companyId,
            'vacation_request',
            'Kërkesë e re pushimi',
            $message,
            route('vacations')
        );

        if ($data['status'] === 'Pending Manager') {
            $managers = $this->employees->getEmployeesByRole($companyId, 'manager');
            foreach ($managers as $manager) {
                if ((int) $manager->id === $employeeId) {
                    continue;
                }

                $this->notifications->notify(
                    $companyId,
                    'employee',
                    (int) $manager->id,
                    'vacation_request',
                    'Kërkesë e re pushimi',
                    $message . ' Në pritje të menaxherit.',
                    route('vacations')
                );
            }
        } else {
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
                    'Kërkesë e re pushimi',
                    $message . ' Në pritje të HR.',
                    route('vacations')
                );
            }
        }

        return true;
    }
    //---------------
    public function getVacations(int $limit, string $search = '', array $statuses = [], ?int $companyId = null, ?int $employeeId = null, ?string $type = null)
    {
        $companyId = $companyId ?: (int) session('company_id');
        $limit = $limit ?: 10;

        if (in_array('Pending Manager', $statuses, true)) {
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
        $current = $vacation->status === 'Reviewing' ? 'Pending Manager' : (string) $vacation->status;

        if ($current === $status) {
            return true;
        }

        $allowed = false;
        if ($role === 'company') {
            if ($current === 'Pending Manager' && in_array($status, ['Pending HR', 'Approved', 'Refused'], true)) {
                $allowed = true;
            }
            if ($current === 'Pending HR' && in_array($status, ['Approved', 'Refused', 'Pending Manager'], true)) {
                $allowed = true;
            }
        } elseif ($role === 'manager') {
            if ($current === 'Pending Manager' && in_array($status, ['Pending HR', 'Refused'], true)) {
                $allowed = true;
            }
        } elseif ($role === 'hr') {
            if ($current === 'Pending HR' && in_array($status, ['Approved', 'Refused'], true)) {
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

        if ($status === 'Pending HR') {
            $this->notifications->notify(
                $companyId,
                'employee',
                $employeeId,
                'vacation_status',
                'Pushimi u aprovua nga menaxheri',
                'Kërkesa juaj e pushimit (' . $period . ') u aprovua nga menaxheri dhe është në pritje të HR.',
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
                    'Pushim në pritje të HR',
                    'Një kërkesë pushimi (' . $period . ') është aprovuar nga menaxheri dhe pret aprovimin e HR.',
                    route('vacations')
                );
            }

            $this->notifications->notify(
                $companyId,
                'company',
                $companyId,
                'vacation_request',
                'Pushim në pritje të HR',
                'Një kërkesë pushimi (' . $period . ') është aprovuar nga menaxheri dhe pret aprovimin e HR.',
                route('vacations')
            );
        } elseif ($status === 'Approved') {
            $this->notifications->notify(
                $companyId,
                'employee',
                $employeeId,
                'vacation_status',
                'Pushimi u aprovua',
                'Kërkesa juaj e pushimit (' . $period . ') u aprovua.',
                route('notifications')
            );

            $employee = $this->employees->findEmployee($employeeId, $companyId);
            $employeeName = $employee->employee ?? ('Punonjësi #' . $employeeId);
            $fromDate = !empty($vacation->from_date) ? Carbon::parse($vacation->from_date)->format('d/m/Y') : '';
            $toDate = !empty($vacation->to_date) ? Carbon::parse($vacation->to_date)->format('d/m/Y') : '';
            $managerMessage = $employeeName . ' është në pushim nga ' . $fromDate . ' deri më ' . $toDate . '.';

            $managers = $this->employees->getEmployeesByRole($companyId, 'manager');
            foreach ($managers as $manager) {
                if ((int) $manager->id === $employeeId) {
                    continue;
                }

                $this->notifications->notify(
                    $companyId,
                    'employee',
                    (int) $manager->id,
                    'vacation_approved',
                    'Punonjës në pushim',
                    $managerMessage,
                    route('vacations')
                );
            }
        } elseif ($status === 'Refused') {
            $this->notifications->notify(
                $companyId,
                'employee',
                $employeeId,
                'vacation_status',
                'Pushimi u refuzua',
                'Kërkesa juaj e pushimit (' . $period . ') u refuzua.',
                route('notifications')
            );
        }

        return true;
    }
}
