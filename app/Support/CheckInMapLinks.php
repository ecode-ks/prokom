<?php

namespace App\Support;

use Illuminate\Support\Facades\DB;

class CheckInMapLinks
{
    public static function hydrate(array $locations, array $projectPins, int $companyId, ?int $managerId = null): array
    {
        $employeeIds = array_values(array_filter(array_map(
            static fn ($loc) => (int) ($loc['employee_id'] ?? 0),
            $locations
        )));

        if ($employeeIds === [] || $projectPins === []) {
            return self::withEmptyLinks($locations);
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

                $meters = GeoPoint::distanceMeters(
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
                    'distance' => $onSite ? __('pages.checkin_map_onsite') : GeoPoint::formatDistance($meters),
                ];
            }
            $location['project_links'] = $links;
        }
        unset($location);

        return $locations;
    }

    private static function withEmptyLinks(array $locations): array
    {
        foreach ($locations as &$location) {
            $location['project_links'] = [];
        }
        unset($location);

        return $locations;
    }
}
