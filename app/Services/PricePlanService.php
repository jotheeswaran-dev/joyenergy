<?php

namespace App\Services;

use App\Models\MeterReadingsInitialize;
use App\Models\PricePlan;

class PricePlanService
{
    private $meterReadingService;
    private $meterReadingInitializer;

    public function __construct(MeterReadingService $meterReadingService, MeterReadingsInitialize $meterReadingInitializer)
    {
        $this->meterReadingService = $meterReadingService;
        $this->meterReadingInitializer = $meterReadingInitializer;
    }

    public function getConsumptionCostOfElectricityReadingsForEachPricePlan($smartMeterId)
    {
        $electricityReadings = $this->meterReadingService->getReadings($smartMeterId);

        if (is_null($electricityReadings)) {
            return $electricityReadings;
        }

        $getCostForAllPlans = [];
        $pricePlans = $this->meterReadingInitializer->getPricePlans();
        foreach ($pricePlans as $pricePlan) {
            $getCostForAllPlans[] = array('key' => $pricePlan->supplier, 'value' => $this->calculateCost($electricityReadings, $pricePlan));
        }

        return $getCostForAllPlans;
    }

    public function getCostPlanForAllSuppliersWithCurrentSupplierDetails($smartMeterId)
    {
        $costPricePerPlans = $this->getConsumptionCostOfElectricityReadingsForEachPricePlan($smartMeterId);
        $currentAvailableSupplierIds = $this->meterReadingInitializer->getSmartMeterToPricePlanAccounts();

        $currentSupplierIdForSmartMeterID = [];
        foreach ($currentAvailableSupplierIds as $currentAvailableSupplierId) {
            if ($currentAvailableSupplierId['id'] = $smartMeterId) {
                $currentSupplierIdForSmartMeterID = ['Current Supplier' => $currentAvailableSupplierId['value'], "SmartmeterId" => $currentAvailableSupplierId['id']];
            }
        }
        array_push($costPricePerPlans, $currentSupplierIdForSmartMeterID);

        return $costPricePerPlans;
    }

    private function calculateCost($electricityReadings, PricePlan $pricePlan)
    {
        $average = $this->calculateAverageReading($electricityReadings);
        $timeElapsed = $this->calculateTimeElapsed($electricityReadings);
        $averagedCost = $average / $timeElapsed;
        return $averagedCost * $pricePlan->unitrate;
    }

    private function calculateAverageReading($electricityReadings)
    {
        $newSummedReadings = 0;
        foreach (array($electricityReadings) as $electricityReading) {
            foreach ($electricityReading as ["readings" => $reading]) {
                $newSummedReadings += $reading;
            }
        }

        return $newSummedReadings / count($electricityReadings);
    }

    private function calculateTimeElapsed($electricityReadings)
    {
        $readingHours = [];
        foreach (array($electricityReadings) as $electricityReading) {
            foreach ($electricityReading as ["time" => $time]) {
                $readingHours[] = $time;
            }
        }
        $minimumElectricityReading = strtotime(min($readingHours));
        $maximumElectricityReading = strtotime(max($readingHours));
        $timeElapsedInHours = abs($maximumElectricityReading - $minimumElectricityReading) / (60 * 60);
        return $timeElapsedInHours;
    }

}
