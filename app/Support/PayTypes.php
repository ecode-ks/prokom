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
            self::HOURLY => __('pages.pay_type_hourly'),
            self::DAILY => __('pages.pay_type_daily'),
            default => __('pages.pay_type_monthly'),
        };
    }

    public static function rateLabel(string $type): string
    {
        return match ($type) {
            self::HOURLY => __('pages.pay_rate_hourly'),
            self::DAILY => __('pages.pay_rate_daily'),
            default => __('pages.pay_rate_monthly'),
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
