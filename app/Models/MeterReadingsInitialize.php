<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MeterReadingsInitialize extends Model
{
    public $smartMeterToPricePlanAccounts = [];
    public $pricePlans = [];
    public $electricityReadings;

    public function __construct()
    {
        $this->electricityReadings = $this->generateMeterElectricityReadings();
    }

    public function generateMeterElectricityReadings()
    {
        $readings = [];
        $smartMeterIds = $this->getSmartMeterToPricePlanAccounts();

        foreach ($smartMeterIds as ["id" => $smartId]) {
            $readings[] = ["smartMeterId" => $smartId, "electricityReadings" => $this->generate(2)];
        }

        return $readings;
    }

    public function getSmartMeterToPricePlanAccounts()
    {
        $this->smartMeterToPricePlanAccounts = [
            ['id' => 'smart-meter-0', 'value' => 'DrEvilsDarkEnergy'],
            ['id' => 'smart-meter-1', 'value' => 'TheGreenEcoSystem'],
            ['id' => 'smart-meter-2', 'value' => 'PowerForEveryone'],
            ['id' => 'smart-meter-3', 'value' => 'ATheGreenEco'],
        ];
        return $this->smartMeterToPricePlanAccounts;
    }

    public function generate($number)
    {
        $electricityReadings = [];
        for ($i = 0; $i < $number; $i++) {
            $reading = mt_rand(10, 100) / 550;
            $electricity = new ElectricityReading(date("Y-m-d H:i:s", time() - $i * 8000), $reading);
            array_push($electricityReadings, array('readings' => $electricity->getReading(), 'time' => $electricity->getTime()));
        }
        return $electricityReadings;
    }

    public function getPricePlans()
    {
        $this->pricePlans = [
            new PricePlan('DrEvilsDarkEnergy', 50),
            new PricePlan('TheGreenEcoSystem', 40),
            new PricePlan('PowerForEveryone', 30),
            new PricePlan('ATheGreenEco', 10)
        ];
        return $this->pricePlans;
    }
}
