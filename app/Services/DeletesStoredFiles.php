<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;

trait DeletesStoredFiles
{
    private function deleteStoredFiles(mixed $payload): void
    {
        $paths = $this->storedFilePaths($payload);
        if ($paths === []) {
            return;
        }

        Storage::disk('public')->delete($paths);
    }

    private function deleteManyStoredFiles(iterable $payloads): void
    {
        foreach ($payloads as $payload) {
            $this->deleteStoredFiles($payload);
        }
    }

    private function storedFilePaths(mixed $payload): array
    {
        if ($payload === null || $payload === '') {
            return [];
        }

        if (is_array($payload)) {
            $items = $payload;
        } else {
            $decoded = json_decode((string) $payload, true);

            if (json_last_error() === JSON_ERROR_NONE) {
                if (is_array($decoded)) {
                    $items = $decoded;
                } elseif (is_string($decoded) && $decoded !== '') {
                    $items = [$decoded];
                } else {
                    return [];
                }
            } else {
                $items = [(string) $payload];
            }
        }

        $paths = [];
        foreach ($items as $item) {
            if (!is_string($item) || trim($item) === '') {
                continue;
            }

            $path = str_replace('\\', '/', trim($item));
            $path = preg_replace('#^https?://[^/]+/#i', '', $path) ?? $path;
            $path = preg_replace('#^(?:public/|storage/|/storage/)#i', '', $path) ?? $path;
            $path = ltrim($path, '/');

            if ($path !== '') {
                $paths[] = $path;
            }
        }

        return array_values(array_unique($paths));
    }
}
