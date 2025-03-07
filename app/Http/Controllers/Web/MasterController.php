<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Resources\MasterResource;
use App\Models\Master;
use Illuminate\Http\Request;
use Inertia\Inertia;

class MasterController extends Controller
{
    public function index(Request $request)
    {
        $masters = Master::paginate(10);

        return Inertia::render('Welcome', [
            'masters' => [
                'data' => MasterResource::collection($masters),
                'prev_page_url' => $masters->previousPageUrl(),
                'next_page_url' => $masters->nextPageUrl(),
            ],
        ]);
    }

    public function fetchMasters(Request $request)
    {
        $masters = Master::paginate(20);

        return response()->json([
            'masters' => [
                'data' => MasterResource::collection($masters),
                'prev_page_url' => $masters->previousPageUrl(),
                'next_page_url' => $masters->nextPageUrl(),
            ],
        ]);
    }
}
