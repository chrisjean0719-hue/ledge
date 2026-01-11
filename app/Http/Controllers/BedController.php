<?php

namespace App\Http\Controllers;

use App\Ledge\BedException;
use App\Ledge\BedPlanner;
use App\Ledge\SampleBed;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BedController extends Controller
{
    public function health(): JsonResponse
    {
        return response()->json(['status' => 'ok']);
    }

    public function sample(): JsonResponse
    {
        return response()->json(BedPlanner::plan(SampleBed::kitchen()));
    }

    public function store(Request $request): JsonResponse
    {
        $payload = json_decode($request->getContent(), true);
        if (! is_array($payload) || array_is_list($payload)) {
            return response()->json(['errors' => ['body: the body must be a JSON object']], 400);
        }

        try {
            return response()->json(BedPlanner::plan($payload));
        } catch (BedException $exception) {
            return response()->json(['errors' => $exception->errors], 400);
        }
    }
}
