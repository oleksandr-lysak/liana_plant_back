<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Resources\Web\MasterResource;
use App\Models\Master;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class MasterController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Welcome', [
            'masters' => [
                'data' => [],
                'prev_page_url' => '',
                'next_page_url' => '',
            ],
        ]);
    }

    public function show(Request $request, String $slug)
    {
        $master = Master::where('slug', $slug)->firstOrFail();
        $master->load([
            'reviews' ,
            'services',
        ]);

        return Inertia::render('Master', [
            'master' => new MasterResource($master),
        ]);
    }

    public function fetchMasters(Request $request)
    {
        $masters = Master::paginate(20);
        return response()->json([
            'masters' => [
                'data' => MasterResource::collection($masters),
                'current_page' => $masters->currentPage(),
                'last_page' => $masters->lastPage(),
                'prev_page_url' => $masters->previousPageUrl(),
                'next_page_url' => $masters->nextPageUrl(),
            ],
        ]);
    }
}
