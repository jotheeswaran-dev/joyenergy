<?php

namespace Tests\Feature;

use Tests\TestCase;

class MeterReadingControllerTest extends TestCase
{
    public function test_getReadings()
    {
        $response = $this->get('/readings/read/{smartMeterId}');

        $response->assertStatus(200);
    }

    public function test_storeReadings()
    {
        $response = $this->post('/readings/store', ['smartMeterId' => 'smart-meter-1',
            'electricityReadings' => ['time' => '6:00', 'readings' => 0.5656]]);

        $response->assertStatus(201);
    }
}
