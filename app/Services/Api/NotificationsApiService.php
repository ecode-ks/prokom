<?php

namespace App\Services\Api;

use App\Models\CompaniesModel;
use App\Models\EmployeesModel;
use App\Repositories\NotificationsRepository;
use App\Repositories\PushTokensRepository;

class NotificationsApiService
{
    protected NotificationsRepository $repository;
    protected PushTokensRepository $pushTokens;
    protected ?array $recipient = null;

    public function __construct(
        NotificationsRepository $repository,
        PushTokensRepository $pushTokens
    ) {
        $this->repository = $repository;
        $this->pushTokens = $pushTokens;
    }

    public function bindRecipient(mixed $account): ?array
    {
        if ($account instanceof CompaniesModel) {
            $this->recipient = [
                'company_id' => (int) $account->id,
                'recipient_type' => 'company',
                'recipient_id' => (int) $account->id,
            ];

            return $this->recipient;
        }

        if (
            $account instanceof EmployeesModel &&
            $account->status === 'Active' &&
            $account->company_id
        ) {
            $this->recipient = [
                'company_id' => (int) $account->company_id,
                'recipient_type' => 'employee',
                'recipient_id' => (int) $account->id,
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
