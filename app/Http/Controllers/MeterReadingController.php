<?php

namespace App\Http\Controllers;

use App\Exceptions\InvalidMeterIdException;
use App\Services\MeterReadingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MeterReadingController extends Controller
{

    private $meterReadingService;

    public function __construct(MeterReadingService $meterReadingService)
    {
        $this->meterReadingService = $meterReadingService;
    }

    public function getReading($smartMeterId): JsonResponse
    {
        try {
            $electricityReadings = $this->meterReadingService->getReadings($smartMeterId);
            return response()->json($electricityReadings);
        } catch (InvalidMeterIdException $exception) {
            return response()->json($exception->getMessage());
        }
    }

    public function storeReadings(Request $request): JsonResponse
    {
        $this->meterReadingService->storeReadings($request->all()["smartMeterId"], $request->all()["electricityReadings"]);

        return response()->json("Readings inserted sucessfully", 201);

    }
}
