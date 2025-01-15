<?php
namespace App\Http\Services\Master;

class MasterFilterService
{
    public static function applyFilters(array $filters, string &$query, array &$queryParams): void
    {
        $whereClauses = [];

        if (!empty($filters['name'])) {
            $whereClauses[] = 'masters.name LIKE :name';
            $queryParams['name'] = '%' . $filters['name'] . '%';
        }

        if (!empty($filters['service_id'])) {
            $whereClauses[] = 'masters.service_id = :service_id';
            $queryParams['service_id'] = $filters['service_id'];
        }

        if (!empty($filters['rating'])) {
            $whereClauses[] = 'AVG(reviews.rating) >= :rating';
            $queryParams['rating'] = $filters['rating'];
        }

        if (!empty($filters['available'])) {
            $whereClauses[] = 'COUNT(time_slots.id) > 0';
        }

        if (!empty($whereClauses)) {
            $query .= ' AND ' . implode(' AND ', $whereClauses);
        }
    }
}
