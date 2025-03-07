<?php

namespace App\Http\Services;

use Illuminate\Pagination\LengthAwarePaginator;

class PaginatorService
{
    public function paginate(array $items, int $total, int $perPage, int $page): LengthAwarePaginator
    {
        return new LengthAwarePaginator($items, $total, $perPage, $page);
    }
}
