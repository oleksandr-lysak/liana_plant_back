<?php

namespace App\Http\Services;

use App\Models\Speciality;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class SpecialityService
{
    /**
     * @param Request $request
     * @return Collection|array|LengthAwarePaginator
     */
    public function getSpecialities(Request $request): Collection|array|LengthAwarePaginator
    {
        $query = Speciality::query();

        $page = $request->input('page');
        return $query->paginate(100, ['*'], 'page', $page);

    }
}