<?php

namespace App\Services;

use App\Repositories\EmployeesRepository;
use App\Repositories\VacationsRepository;
use Carbon\Carbon;

class VacationsService
{
    public const TYPE_ANNUAL = 'Pushim vjetor';
    public const TYPE_MEDICAL = 'Pushim mjekësor';

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

        if (empty($data['status'])) {
            $data['status'] = 'Reviewing';
        }
        if (empty($data['date'])) {
            $data['date'] = now()->toDateString();
        }

        $vacationId = $this->repository->create($data);
        if ($vacationId <= 0) {
            return false;
        }

        $employeeId = (int) ($data['employee_id'] ?? 0);
        $from = $data['from_date'] ?? '';
        $to = $data['to_date'] ?? '';
        $period = trim($from . ' - ' . $to, ' -');
        $message = $period !== ''
            ? 'Është regjistruar një kërkesë pushimi (' . $period . ').'
            : 'Është regjistruar një kërkesë pushimi.';

        // Notify the employee on the vacation
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

        // Notify company account
        $this->notifications->notify(
            $companyId,
            'company',
            $companyId,
            'vacation_request',
            'Kërkesë e re pushimi',
            $message,
            route('vacations')
        );

        // Notify all active HR employees (they approve requests)
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
                $message,
                route('vacations')
            );
        }

        // Notify managers about the new leave request
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
                $message,
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
    public function updateVacationStatus(int $id, string $status, ?int $companyId = null): bool
    {
        $companyId = $companyId ?: (int) session('company_id');
        $vacation = $this->repository->findVacation($id, $companyId);
        if (!$vacation) {
            return false;
        }

        if ($vacation->status === $status) {
            return true;
        }

        $updated = $this->repository->updateStatus($id, $status, $companyId);
        if (!$updated) {
            return false;
        }

        $employeeId = (int) ($vacation->employee_id ?? 0);
        if ($employeeId <= 0) {
            return true;
        }

        if ($status === 'Approved') {
            $title = 'Pushimi u aprovua';
            $message = 'Kërkesa juaj e pushimit (' . ($vacation->from_date ?? '') . ' - ' . ($vacation->to_date ?? '') . ') u aprovua.';
        } elseif ($status === 'Refused') {
            $title = 'Pushimi u refuzua';
            $message = 'Kërkesa juaj e pushimit (' . ($vacation->from_date ?? '') . ' - ' . ($vacation->to_date ?? '') . ') u refuzua.';
        } else {
            $title = 'Statusi i pushimit';
            $message = 'Kërkesa juaj e pushimit është në shqyrtim.';
        }

        $this->notifications->notify(
            $companyId,
            'employee',
            $employeeId,
            'vacation_status',
            $title,
            $message,
            route('notifications')
        );

        if ($status === 'Approved') {
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
        }

        return true;
    }
}
