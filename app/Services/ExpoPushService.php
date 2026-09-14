<?php

namespace App\Services;

use App\Repositories\PushTokensRepository;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ExpoPushService
{
    public function __construct(protected PushTokensRepository $tokens)
    {
    }

    public function sendToRecipient(
        int $companyId,
        string $recipientType,
        int $recipientId,
        string $title,
        string $message,
        array $data = []
    ): void {
        $tokens = $this->tokens->tokensFor($companyId, $recipientType, $recipientId);
        if (!$tokens) {
            return;
        }

        $messages = array_map(static function (string $token) use ($title, $message, $data) {
            return [
                'to' => $token,
                'sound' => 'default',
                'title' => $title,
                'body' => $message,
                'data' => $data,
                'channelId' => 'workom-notifications',
            ];
        }, $tokens);

        foreach (array_chunk($messages, 100) as $chunk) {
            $this->dispatch($chunk);
        }
    }

    private function dispatch(array $messages): void
    {
        try {
            $response = Http::acceptJson()
                ->asJson()
                ->timeout(8)
                ->post('https://exp.host/--/api/v2/push/send', $messages);

            if (!$response->successful()) {
                Log::warning('Expo push failed', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                return;
            }

            $tickets = $response->json('data') ?? [];
            foreach ($tickets as $index => $ticket) {
                if (($ticket['status'] ?? '') !== 'error') {
                    continue;
                }

                $error = $ticket['details']['error'] ?? '';
                if ($error === 'DeviceNotRegistered') {
                    $token = $messages[$index]['to'] ?? null;
                    if (is_string($token)) {
                        $this->tokens->deleteByToken($token);
                    }
                }
            }
        } catch (\Throwable $e) {
            Log::warning('Expo push exception: '.$e->getMessage());
        }
    }
}
