<?php

namespace App\Services;

use App\Exceptions\InvalidMeterIdException;
use App\Repository\PricePlanRepository;

class PricePlanService
{
    private $meterReadingService;
    private $pricePlanRepository;

    public function __construct(MeterReadingService $meterReadingService, PricePlanRepository $pricePlanRepository)
    {
        $this->meterReadingService = $meterReadingService;
        $this->pricePlanRepository = $pricePlanRepository;
    }

    public function getConsumptionCostOfElectricityReadingsForEachPricePlan($smartMeterId): ?array
    {
        $getCostForAllPlans = [];


        $readings = $this->meterReadingService->getReadings($smartMeterId);

        $pricePlans = $this->pricePlanRepository->getPricePlans();

        if (is_null($readings)) {
            return null;
        }

        foreach ($pricePlans as $pricePlan) {

            $getCostForAllPlans[] = array('key' => $pricePlan->supplier, 'value' => $this->calculateCost($readings, $pricePlan));
        }

        return $getCostForAllPlans;
    }

    public function getCostPlanForAllSuppliersWithCurrentSupplierDetails($smartMeterId): ?array
    {
        $costPricePerPlans = $this->getConsumptionCostOfElectricityReadingsForEachPricePlan($smartMeterId);
        $currentAvailableSupplierIds = $this->pricePlanRepository->getCurrentAvailableSupplierIds();


        $currentSupplierIdForSmartMeterID = [];
        foreach ($currentAvailableSupplierIds as $currentAvailableSupplierId) {
            if ($currentAvailableSupplierId->smartMeterId = $smartMeterId) {
                $currentSupplierIdForSmartMeterID = ['Current Supplier' => $currentAvailableSupplierId->supplier,
                    "SmartMeterId" => $currentAvailableSupplierId->smartMeterId];
            }
        }
        array_push($costPricePerPlans, $currentSupplierIdForSmartMeterID);

        return $costPricePerPlans;
    }

    private function calculateCost($electricityReadings, $pricePlan)
    {
        $average = $this->calculateAverageReading($electricityReadings);
        $timeElapsed = $this->calculateTimeElapsed($electricityReadings);
        $averagedCost = $average / $timeElapsed;

        return $averagedCost * $pricePlan->unitRate;
    }

    private function calculateAverageReading($electricityReadings)
    {

        if (count($electricityReadings) <= 0) {
            throw new InvalidMeterIdException("No readings available");
        }
        $newSummedReadings = 0;
        foreach ($electricityReadings as $electricityReading) {
            foreach ($electricityReading as $reading) {
                $newSummedReadings += (int)$reading;
            }
        }
        return $newSummedReadings / count($electricityReadings);
    }

    private function calculateTimeElapsed($electricityReadings)
    {
        $readingHours = [];
        foreach ($electricityReadings as $electricityReading) {
            foreach ($electricityReading as $time) {
                $readingHours[] = $time;
            }
        }
        $minimumElectricityReading = strtotime(min($readingHours));
        $maximumElectricityReading = strtotime(max($readingHours));
        return abs($maximumElectricityReading - $minimumElectricityReading) / (60 * 60);
    }
}
