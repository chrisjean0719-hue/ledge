<?php

namespace App\Http\Controllers;

use App\Ledge\BedException;
use App\Ledge\BedPlanner;
use App\Ledge\Compare;
use App\Ledge\Elevation;
use App\Ledge\SampleBed;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

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

    public function sampleElevation(): Response
    {
        return $this->svg(BedPlanner::plan(SampleBed::kitchen()));
    }

    public function store(Request $request): JsonResponse
    {
        $payload = $this->payload($request);
        if ($payload instanceof JsonResponse) {
            return $payload;
        }

        try {
            return response()->json(BedPlanner::plan($payload));
        } catch (BedException $exception) {
            return response()->json(['errors' => $exception->errors], 400);
        }
    }

    public function elevation(Request $request): JsonResponse|Response
    {
        $payload = $this->payload($request);
        if ($payload instanceof JsonResponse) {
            return $payload;
        }

        try {
            return $this->svg(BedPlanner::plan($payload));
        } catch (BedException $exception) {
            return response()->json(['errors' => $exception->errors], 400);
        }
    }

    public function compare(Request $request): JsonResponse
    {
        $payload = $this->payload($request);
        if ($payload instanceof JsonResponse) {
            return $payload;
        }

        $errors = [];
        $left = $this->side('left', $payload['left'] ?? null, $errors);
        $right = $this->side('right', $payload['right'] ?? null, $errors);
        if ($left === null || $right === null) {
            return response()->json(['errors' => $errors], 400);
        }

        $diff = Compare::beds($left, $right);

        return response()->json([
            'left' => $left['name'],
            'right' => $right['name'],
            'timberLengthMm' => $diff['timberLengthMm'],
            'soilLitres' => $diff['soilLitres'],
            'text' => Compare::format($left, $right, $diff),
        ]);
    }

    /**
     * @param  list<string>  $errors
     * @return array<string, int|string>|null
     */
    private function side(string $side, mixed $bed, array &$errors): ?array
    {
        if (! is_array($bed) || array_is_list($bed)) {
            $errors[] = $side.': Compare needs a '.$side.' bed';

            return null;
        }

        try {
            return BedPlanner::plan($bed);
        } catch (BedException $exception) {
            foreach ($exception->errors as $message) {
                $errors[] = $this->label($side, $message);
            }

            return null;
        }
    }

    private function label(string $side, string $message): string
    {
        $index = strpos($message, ':');
        if ($index === false) {
            return $side.': '.$message;
        }

        return $side.'.'.substr($message, 0, $index).substr($message, $index);
    }

    /**
     * @param  array<string, int|string>  $plan
     */
    private function svg(array $plan): Response
    {
        return response(Elevation::svg($plan), 200, [
            'Content-Type' => 'image/svg+xml; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="'.Elevation::fileName($plan['name']).'"',
        ]);
    }

    private function payload(Request $request): array|JsonResponse
    {
        $payload = json_decode($request->getContent(), true);
        if (! is_array($payload) || array_is_list($payload)) {
            return response()->json(['errors' => ['body: the body must be a JSON object']], 400);
        }

        return $payload;
    }
}
