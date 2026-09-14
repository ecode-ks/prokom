<?php

namespace App\Services\Api;

use App\Repositories\CompaniesRepository;
use App\Repositories\EmployeesRepository;
use App\Repositories\NotificationsRepository;
use App\Repositories\PushTokensRepository;

class NotificationsApiService
{
    protected NotificationsRepository $repository;
    protected EmployeesRepository $employees;
    protected CompaniesRepository $companies;
    protected PushTokensRepository $pushTokens;
    protected ?array $recipient = null;
    protected array $employeeRoles = ['manager', 'employee', 'finance', 'hr'];

    public function __construct(
        NotificationsRepository $repository,
        EmployeesRepository $employees,
        CompaniesRepository $companies,
        PushTokensRepository $pushTokens
    ) {
        $this->repository = $repository;
        $this->employees = $employees;
        $this->companies = $companies;
        $this->pushTokens = $pushTokens;
    }

    public function bindRecipient(?string $username = null, ?int $employeeId = null, ?int $companyId = null, ?string $role = null): ?array
    {
        if ($username) {
            $employee = $this->employees->findEmployeeByUsername(trim($username));
            if ($employee && strcasecmp((string) ($employee->status ?? ''), 'Active') === 0) {
                $this->recipient = [
                    'company_id' => (int) $employee->company_id,
                    'recipient_type' => 'employee',
                    'recipient_id' => (int) $employee->id,
                ];
                $this->applyLocale($this->recipient['company_id']);
                return $this->recipient;
            }
        }

        $role = (string) $role;
        $companyId = (int) $companyId;
        $this->applyLocale($companyId);

        if ($role === 'company' && $companyId > 0) {
            $this->recipient = [
                'company_id' => $companyId,
                'recipient_type' => 'company',
                'recipient_id' => $companyId,
            ];
            return $this->recipient;
        }

        if (in_array($role, $this->employeeRoles, true) && $employeeId > 0 && $companyId > 0) {
            $this->recipient = [
                'company_id' => $companyId,
                'recipient_type' => 'employee',
                'recipient_id' => $employeeId,
            ];
            return $this->recipient;
        }

        $this->recipient = null;
        return null;
    }

    public function resolveRecipient(): ?array
    {
        return $this->recipient;
    }

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

    private function applyLocale(int $companyId): void
    {
        if ($companyId <= 0) {
            return;
        }

        $company = $this->companies->findCompanyById($companyId);
        $language = (string) ($company->language ?? 'sq');
        if (!in_array($language, ['sq', 'en', 'de'], true)) {
            $language = 'sq';
        }

        app()->setLocale($language);
    }

    public function registerPushToken(string $token, ?string $platform = null): bool
    {
        $recipient = $this->resolveRecipient();
        if (!$recipient) {
            return false;
        }

        $token = trim($token);
        if ($token === '' || !str_starts_with($token, 'ExponentPushToken[')) {
            return false;
        }

        return $this->pushTokens->upsert([
            'company_id' => $recipient['company_id'],
            'recipient_type' => $recipient['recipient_type'],
            'recipient_id' => $recipient['recipient_id'],
            'expo_push_token' => $token,
            'platform' => $platform,
        ]);
    }
}
