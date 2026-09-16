<?php

namespace App\Support;

class ProjectMapPins
{
    public static function fromRows($rows): array
    {
        $pins = [];

        foreach ($rows as $row) {
            $coords = GeoPoint::parse($row->location ?? null);
            if (!$coords) {
                continue;
            }

            $status = strtolower((string) ($row->status ?? ''));

            $pins[] = [
                'id' => (int) $row->id,
                'kind' => 'project',
                'name' => $row->project_name,
                'lat' => $coords['lat'],
                'lng' => $coords['lng'],
                'location_label' => GeoPoint::label($row->location),
                'status' => $status,
                'status_label' => match ($status) {
                    'in_progress' => __('pages.in_progress'),
                    'completed' => __('pages.completed'),
                    'on_hold' => __('pages.pending'),
                    default => __('pages.status_planning'),
                },
                'manager_name' => $row->manager_name ?? '-',
                'client_name' => $row->client_name ?? '-',
            ];
        }

        return $pins;
    }
}
