<?php

namespace App\Support;

class GeoPoint
{
    public static function parse(?string $value): ?array
    {
        if ($value === null || trim($value) === '') {
            return null;
        }

        if (!preg_match('/(-?\d+(?:\.\d+)?)\s*,\s*(-?\d+(?:\.\d+)?)/', $value, $match)) {
            return null;
        }

        $lat = (float) $match[1];
        $lng = (float) $match[2];
        if (abs($lat) > 90 || abs($lng) > 180) {
            return null;
        }

        return ['lat' => $lat, 'lng' => $lng];
    }

    public static function label(?string $value): string
    {
        $text = trim((string) $value);
        if ($text === '') {
            return '-';
        }

        $withoutCoords = trim(preg_replace('/(-?\d+(?:\.\d+)?)\s*,\s*(-?\d+(?:\.\d+)?)/', '', $text));
        $withoutCoords = trim($withoutCoords, " \t\n\r\0\x0B|-");

        return $withoutCoords !== '' ? $withoutCoords : $text;
    }

    public static function distanceMeters(float $lat1, float $lng1, float $lat2, float $lng2): float
    {
        $earth = 6371000;
        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);
        $a = sin($dLat / 2) ** 2
            + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLng / 2) ** 2;

        return 2 * $earth * asin(min(1, sqrt($a)));
    }

    public static function formatDistance(float $meters): string
    {
        if ($meters < 1000) {
            return (string) round($meters) . ' m';
        }

        return number_format($meters / 1000, 2, ',', '.') . ' km';
    }
}
