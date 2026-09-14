<?php

namespace App\Services;

use App\Repositories\CompaniesRepository;
use App\Repositories\EmployeesRepository;
use App\Repositories\NotificationsRepository;

class NotificationsService
{
    protected NotificationsRepository $repository;
    protected EmployeesRepository $employees;
    protected CompaniesRepository $companies;
    protected ExpoPushService $push;
    protected array $localeCache = [];

    protected array $employeeRoles = ['manager', 'employee', 'finance', 'hr'];

    public function __construct(
        NotificationsRepository $repository,
        EmployeesRepository $employees,
        CompaniesRepository $companies,
        ExpoPushService $push
    ) {
        $this->repository = $repository;
        $this->employees = $employees;
        $this->companies = $companies;
        $this->push = $push;
    }
    //---------------
    public function localeForCompany(int $companyId): string
    {
        if ($companyId <= 0) {
            return 'sq';
        }

        if (!isset($this->localeCache[$companyId])) {
            $company = $this->companies->findCompanyById($companyId);
            $language = (string) ($company->language ?? 'sq');
            $this->localeCache[$companyId] = in_array($language, ['sq', 'en', 'de'], true) ? $language : 'sq';
        }

        return $this->localeCache[$companyId];
    }
    //---------------
    public function line(int $companyId, string $key, array $replace = []): string
    {
        return trans($key, $replace, $this->localeForCompany($companyId));
    }
    //---------------
    public function notify(
        int $companyId,
        string $recipientType,
        int $recipientId,
        string $type,
        string $title,
        string $message,
        ?string $link = null,
        array $replace = []
    ): bool {
        if ($recipientId <= 0 || $companyId <= 0) {
            return false;
        }

        if (!in_array($recipientType, ['company', 'employee'], true)) {
            return false;
        }

        $title = $this->localize($companyId, $title, $replace);
        $message = $this->localize($companyId, $message, $replace);

        $appLink = $this->toAppPath($link);
        $id = $this->repository->create([
            'company_id' => $companyId,
            'recipient_type' => $recipientType,
            'recipient_id' => $recipientId,
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'link' => $appLink,
            'is_read' => 0,
            'created_at' => now(),
        ]);

        if ($id <= 0) {
            return false;
        }

        $this->push->sendToRecipient($companyId, $recipientType, $recipientId, $title, $message, [
            'notificationId' => $id,
            'type' => $type,
            'link' => $appLink,
        ]);

        return true;
    }
    //---------------
    public function resolveRecipient(): ?array
    {
        $role = (string) session('role');
        $companyId = (int) session('company_id');

        if (!$companyId || $role === 'admin') {
            return null;
        }

        if ($role === 'company') {
            return [
                'company_id' => $companyId,
                'recipient_type' => 'company',
                'recipient_id' => $companyId,
            ];
        }

        if (!in_array($role, $this->employeeRoles, true)) {
            return null;
        }

        $employeeId = (int) session('employee_id');

        if ($employeeId <= 0 && session('username')) {
            $employee = $this->employees->findEmployeeByUsername((string) session('username'));
            if ($employee && (int) $employee->company_id === $companyId) {
                $employeeId = (int) $employee->id;
                session()->put('employee_id', $employeeId);
            }
        }

        if ($employeeId <= 0) {
            return null;
        }

        return [
            'company_id' => $companyId,
            'recipient_type' => 'employee',
            'recipient_id' => $employeeId,
        ];
    }
    //---------------
    public function getNotifications(int $limit = 10)
    {
        $recipient = $this->resolveRecipient();
        if (!$recipient) {
            return null;
        }

        return $this->repository->getNotifications(
            $limit,
            $recipient['company_id'],
            $recipient['recipient_type'],
            $recipient['recipient_id']
        );
    }
    //---------------
    public function getLatest(int $limit = 8)
    {
        $recipient = $this->resolveRecipient();
        if (!$recipient) {
            return collect();
        }

        return $this->repository->getLatest(
            $limit,
            $recipient['company_id'],
            $recipient['recipient_type'],
            $recipient['recipient_id']
        );
    }
    //---------------
    public function countUnread(): int
    {
        $recipient = $this->resolveRecipient();
        if (!$recipient) {
            return 0;
        }

        return $this->repository->countUnread(
            $recipient['company_id'],
            $recipient['recipient_type'],
            $recipient['recipient_id']
        );
    }
    //---------------
    public function markAsRead(int $id): ?string
    {
        $recipient = $this->resolveRecipient();
        if (!$recipient) {
            return null;
        }

        $notification = $this->repository->findNotification(
            $id,
            $recipient['company_id'],
            $recipient['recipient_type'],
            $recipient['recipient_id']
        );

        if (!$notification) {
            return null;
        }

        if (!(int) $notification->is_read) {
            $this->repository->markAsRead(
                $id,
                $recipient['company_id'],
                $recipient['recipient_type'],
                $recipient['recipient_id']
            );
        }

        return $this->toAppPath($notification->link) ?: '/notifications';
    }
    //---------------
    public function markAllAsRead(): bool
    {
        $recipient = $this->resolveRecipient();
        if (!$recipient) {
            return false;
        }

        return $this->repository->markAllAsRead(
            $recipient['company_id'],
            $recipient['recipient_type'],
            $recipient['recipient_id']
        );
    }
    //---------------
    private function toAppPath(?string $link): ?string
    {
        if ($link === null || trim($link) === '') {
            return null;
        }

        $path = parse_url($link, PHP_URL_PATH);
        if (!$path) {
            return null;
        }

        $query = parse_url($link, PHP_URL_QUERY);

        return $query ? $path . '?' . $query : $path;
    }
    //---------------
    private function localize(int $companyId, string $value, array $replace = []): string
    {
        if (!str_starts_with($value, 'pages.')) {
            return $value;
        }

        return $this->line($companyId, $value, $replace);
    }
}
