<?php

namespace App\Repositories;

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class PushTokensRepository
{
    protected string $table = 'push_tokens';

    public function available(): bool
    {
        return Schema::hasTable($this->table);
    }

    public function upsert(array $data): bool
    {
        if (!$this->available() || empty($data['expo_push_token'])) {
            return false;
        }

        $now = now();
        $existing = DB::table($this->table)
            ->where('expo_push_token', $data['expo_push_token'])
            ->first();

        $fields = [
            'company_id' => $data['company_id'],
            'recipient_type' => $data['recipient_type'],
            'recipient_id' => $data['recipient_id'],
            'platform' => $data['platform'] ?? null,
            'updated_at' => $now,
        ];

        if ($existing) {
            return DB::table($this->table)
                ->where('id', $existing->id)
                ->update($fields) >= 0;
        }

        $fields['expo_push_token'] = $data['expo_push_token'];
        $fields['created_at'] = $now;

        return DB::table($this->table)->insert($fields);
    }

    public function tokensFor(int $companyId, string $recipientType, int $recipientId): array
    {
        if (!$this->available()) {
            return [];
        }

        return DB::table($this->table)
            ->where('company_id', $companyId)
            ->where('recipient_type', $recipientType)
            ->where('recipient_id', $recipientId)
            ->pluck('expo_push_token')
            ->filter()
            ->values()
            ->all();
    }

    public function deleteByToken(string $token): void
    {
        if (!$this->available() || $token === '') {
            return;
        }

        DB::table($this->table)->where('expo_push_token', $token)->delete();
    }
}
