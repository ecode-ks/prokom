<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

trait CheckInMapHelpers
{
    public function attachMapProjectLinks(array $locations, array $projectPins, ?int $companyId = null, ?int $managerId = null): array
    {
        $companyId = $companyId ?? ($this->companyId ?? 0);
        $employeeIds = array_values(array_filter(array_map(
            static fn ($loc) => (int) ($loc['employee_id'] ?? 0),
            $locations
        )));

        if ($employeeIds === [] || $projectPins === []) {
            foreach ($locations as &$location) {
                $location['project_links'] = [];
            }
            unset($location);

            return $locations;
        }

        $pinsById = [];
        foreach ($projectPins as $pin) {
            $pinsById[(int) $pin['id']] = $pin;
        }

        $query = DB::table('tasks as t')
            ->join('projects as p', 'p.id', '=', 't.project_id')
            ->where('t.company_id', $companyId)
            ->whereRaw("LOWER(COALESCE(t.status, '')) <> 'completed'")
            ->select('t.assigned_to', 'p.id as project_id');

        if ($managerId) {
            $query->where('p.project_manager', $managerId);
        }

        $byEmployee = [];
        foreach ($query->get() as $row) {
            foreach (array_filter(array_map('intval', explode(',', (string) $row->assigned_to))) as $employeeId) {
                if (!in_array($employeeId, $employeeIds, true)) {
                    continue;
                }
                $byEmployee[$employeeId][(int) $row->project_id] = (int) $row->project_id;
            }
        }

        foreach ($locations as &$location) {
            $links = [];
            $employeeId = (int) ($location['employee_id'] ?? 0);
            foreach ($byEmployee[$employeeId] ?? [] as $projectId) {
                $pin = $pinsById[$projectId] ?? null;
                if (!$pin) {
                    continue;
                }

                $meters = $this->distanceMeters(
                    (float) $location['lat'],
                    (float) $location['lng'],
                    (float) $pin['lat'],
                    (float) $pin['lng']
                );
                $onSite = $meters < 40;

                $links[] = [
                    'project_id' => (int) $pin['id'],
                    'name' => $pin['name'],
                    'lat' => $pin['lat'],
                    'lng' => $pin['lng'],
                    'on_site' => $onSite,
                    'distance' => $onSite ? __('pages.checkin_map_onsite') : $this->formatDistance($meters),
                ];
            }
            $location['project_links'] = $links;
        }
        unset($location);

        return $locations;
    }

    private function projectMapPinsFromRows($rows): array
    {
        $pins = [];

        foreach ($rows as $row) {
            $coords = $this->parseLatLng($row->location ?? null);
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
                'location_label' => $this->locationLabel($row->location),
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

    private function parseLatLng(?string $value): ?array
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

    private function locationLabel(?string $value): string
    {
        $text = trim((string) $value);
        if ($text === '') {
            return '-';
        }

        $withoutCoords = trim(preg_replace('/(-?\d+(?:\.\d+)?)\s*,\s*(-?\d+(?:\.\d+)?)/', '', $text));
        $withoutCoords = trim($withoutCoords, " \t\n\r\0\x0B|-");

        return $withoutCoords !== '' ? $withoutCoords : $text;
    }

    private function distanceMeters(float $lat1, float $lng1, float $lat2, float $lng2): float
    {
        $earth = 6371000;
        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);
        $a = sin($dLat / 2) ** 2
            + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLng / 2) ** 2;

        return 2 * $earth * asin(min(1, sqrt($a)));
    }

    private function formatDistance(float $meters): string
    {
        if ($meters < 1000) {
            return (string) round($meters) . ' m';
        }

        return number_format($meters / 1000, 2, ',', '.') . ' km';
    }
}
