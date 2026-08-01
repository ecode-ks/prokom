<?php

namespace App\Repositories;

use App\Models\NotificationsModel;
use Illuminate\Support\Facades\DB;

class NotificationsRepository
{
    protected string $table;

    public function __construct(NotificationsModel $model)
    {
        $this->table = $model->getTable();
    }
    //---------------
    public function create(array $data): bool
    {
        if (empty($data['created_at'])) {
            $data['created_at'] = now();
        }

        return DB::table($this->table)->insert($data);
    }
    //---------------
    public function getNotifications(int $limit, int $companyId, string $recipientType, int $recipientId)
    {
        return DB::table($this->table)
            ->where('company_id', $companyId)
            ->where('recipient_type', $recipientType)
            ->where('recipient_id', $recipientId)
            ->orderByDesc('id')
            ->cursorPaginate($limit);
    }
    //---------------
    public function getLatest(int $limit, int $companyId, string $recipientType, int $recipientId)
    {
        return DB::table($this->table)
            ->where('company_id', $companyId)
            ->where('recipient_type', $recipientType)
            ->where('recipient_id', $recipientId)
            ->orderByDesc('id')
            ->limit($limit)
            ->get();
    }
    //---------------
    public function countUnread(int $companyId, string $recipientType, int $recipientId): int
    {
        return DB::table($this->table)
            ->where('company_id', $companyId)
            ->where('recipient_type', $recipientType)
            ->where('recipient_id', $recipientId)
            ->where('is_read', 0)
            ->count();
    }
    //---------------
    public function findNotification(int $id, int $companyId, string $recipientType, int $recipientId)
    {
        return DB::table($this->table)
            ->where('id', $id)
            ->where('company_id', $companyId)
            ->where('recipient_type', $recipientType)
            ->where('recipient_id', $recipientId)
            ->first();
    }
    //---------------
    public function markAsRead(int $id, int $companyId, string $recipientType, int $recipientId): bool
    {
        return DB::table($this->table)
            ->where('id', $id)
            ->where('company_id', $companyId)
            ->where('recipient_type', $recipientType)
            ->where('recipient_id', $recipientId)
            ->update(['is_read' => 1]) > 0;
    }
    //---------------
    public function markAllAsRead(int $companyId, string $recipientType, int $recipientId): bool
    {
        return DB::table($this->table)
            ->where('company_id', $companyId)
            ->where('recipient_type', $recipientType)
            ->where('recipient_id', $recipientId)
            ->where('is_read', 0)
            ->update(['is_read' => 1]) >= 0;
    }
}
