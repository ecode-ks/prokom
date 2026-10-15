<?php

/*
 * Template packages for the pricing section of the public site. They are not read from the
 * packages table. Prices are in EUR; staff is the most employees the package covers.
 * The price per employee falls as the team grows (5 € → ~4 € → ~3.6 €); a year costs ten
 * months (two months free). Teams above the largest package get an offer.
 * Names and shared features are translated in lang/{sq,en,de}/marketing.php under
 * 'pricing_plans' and 'pricing_features'. All packages include the same access and features.
 */
return [
    'plans' => [
        'start' => ['staff' => 10, 'monthly' => 50, 'yearly' => 500],
        'standard' => ['staff' => 25, 'monthly' => 99, 'yearly' => 990, 'popular' => true],
        'pro' => ['staff' => 50, 'monthly' => 179, 'yearly' => 1790],
    ],
];
