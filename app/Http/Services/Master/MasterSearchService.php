<?php
namespace App\Http\Services\Master;

use Illuminate\Support\Facades\DB;

class MasterSearchService
{
    public function getMastersOnDistance(float $lat, float $lng, float $zoom, array $filters, int $perPage, int $page): array
    {
        $max_distance = $this::calculateSearchRadius($zoom);
        $offset = ($page - 1) * $perPage;

        $distanceQuery = '(6371 * acos(cos(radians(' . $lat . ')) * cos(radians(latitude)) * cos(radians(longitude) - radians(' . $lng . ')) + sin(radians(' . $lat . ')) * sin(radians(latitude)))) as distance';
        $query = '
            SELECT
                masters.id,
                masters.name,
                masters.phone,
                masters.address,
                masters.latitude,
                masters.longitude,
                masters.description,
                masters.age,
                masters.photo,
                masters.main_service_id,
                ' . $distanceQuery . ',
                COUNT(time_slots.id) as available,
                COUNT(reviews.id) as reviews_count,
                IF(COUNT(reviews.id) > 0, AVG(reviews.rating), 0) as rating
            FROM
                masters
            LEFT JOIN
                time_slots ON time_slots.master_id = masters.id
                AND time_slots.date = CURDATE()
                AND time_slots.is_booked = false
                AND time_slots.time > CURTIME()
                AND ADDDATE(CONCAT(time_slots.date, " ", time_slots.time), INTERVAL duration MINUTE) > NOW()
            LEFT JOIN reviews ON reviews.master_id = masters.id
        ';

        $queryParams = ['max_distance' => $max_distance];

        MasterFilterService::applyFilters($filters, $query, $queryParams);

        $query .= '
            GROUP BY
                masters.id
            HAVING
                distance <= :max_distance
            ORDER BY
                available DESC, distance ASC
            LIMIT :limit OFFSET :offset
        ';

        $queryParams['limit'] = $perPage;
        $queryParams['offset'] = $offset;

        return DB::select($query, $queryParams);
    }

    private static function calculateSearchRadius(int $zoom): float
    {
        $earthRadiusKm = 20037.5;
        return $earthRadiusKm / $zoom;
    }
}
