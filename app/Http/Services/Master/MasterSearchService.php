<?php

namespace App\Http\Services\Master;

use Illuminate\Support\Facades\DB;

class MasterSearchService
{
    public function getMastersOnDistance(float $lat, float $lng, float $zoom, array $filters, int $perPage, int $page): array
    {
        $maxDistance = $this::calculateSearchRadius($zoom);
        $offset = ($page - 1) * $perPage;

        $latDelta = $maxDistance / 111; // 111 км ≈ 1° широти
        $lngDelta = $maxDistance / (111 * cos(deg2rad($lat))); // Δ довготи залежить від широти

        $query = '
    SELECT
        masters.id,
        masters.name,
        masters.phone,
        masters.address,
        masters.latitude,
        masters.longitude,
        masters.description,
        masters.slug,
        masters.age,
        masters.photo,
        masters.service_id,
        masters.approved,
        (
            6371 * acos(
                cos(radians(:distance_lat)) * cos(radians(latitude)) * cos(radians(longitude) - radians(:distance_lng))
                + sin(radians(:distance_lat2)) * sin(radians(latitude))
            )
        ) as distance,
        CASE
            WHEN busy_appointments.master_id IS NOT NULL THEN false
            ELSE true
        END as available,
        COALESCE(reviews_summary.reviews_count, 0) as reviews_count,
        COALESCE(reviews_summary.rating, 0) as rating
    FROM
        masters
    LEFT JOIN (
        SELECT
            master_id,
            COUNT(id) as reviews_count,
            AVG(rating) as rating
        FROM
            reviews
        GROUP BY
            master_id
    ) as reviews_summary ON reviews_summary.master_id = masters.id
    LEFT JOIN (
        SELECT DISTINCT master_id
        FROM appointments
        WHERE start_time <= DATE_ADD(NOW(), INTERVAL 1 HOUR) AND end_time >= DATE_ADD(NOW(), INTERVAL 1 HOUR)
    ) as busy_appointments ON busy_appointments.master_id = masters.id
    WHERE
        latitude BETWEEN :min_lat AND :max_lat
        AND longitude BETWEEN :min_lng AND :max_lng
    ';
        $queryParams = [
            'distance_lat' => $lat,
            'distance_lng' => $lng,
            'distance_lat2' => $lat,
            'min_lat' => $lat - $latDelta,
            'max_lat' => $lat + $latDelta,
            'min_lng' => $lng - $lngDelta,
            'max_lng' => $lng + $lngDelta,
            'max_distance' => $maxDistance,
        ];

        // Додатково застосовуємо фільтри, якщо потрібно
        MasterFilterService::applyFilters($filters, $query, $queryParams);
        $query .= '
        HAVING
        distance <= :max_distance
    ORDER BY
        available DESC, distance ASC
    ';
        $query .= " LIMIT {$perPage} OFFSET {$offset}";

        return DB::select($query, $queryParams);
    }

    private static function calculateSearchRadius(int $zoom): float
    {
        $earthRadiusKm = 20037.5;

        return $earthRadiusKm / $zoom;
    }
}
