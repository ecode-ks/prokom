<?php

namespace App\Support;

class PayTypes
{
    public const MONTHLY = 'monthly';
    public const HOURLY = 'hourly';
    public const DAILY = 'daily';

    public static function all(): array
    {
        return [
            self::MONTHLY,
            self::HOURLY,
            self::DAILY,
        ];
    }

    public static function label(string $type): string
    {
        return match ($type) {
            self::HOURLY => 'Me orë',
            self::DAILY => 'Me ditë',
            default => 'Mujore (fikse)',
        };
    }

    public static function rateLabel(string $type): string
    {
        return match ($type) {
            self::HOURLY => 'Tarifa / orë',
            self::DAILY => 'Tarifa / ditë',
            default => 'Paga bazë (mujore)',
        };
    }

    public static function options(): array
    {
        return [
            self::MONTHLY => self::label(self::MONTHLY),
            self::HOURLY => self::label(self::HOURLY),
            self::DAILY => self::label(self::DAILY),
        ];
    }
}
