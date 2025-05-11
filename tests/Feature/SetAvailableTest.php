<?php

namespace Tests\Feature;

use App\Models\Master;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Redis;
use Tests\TestCase;

class SetAvailableTest extends TestCase
{
    //use RefreshDatabase;
    /**
     * A basic feature test example.
     */
    public function test_example(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    public function testSetAvailable()
    {
        // Greate fake master
        $master = Master::factory()->create();

        // input data
        $startTime = '2025-05-10 09:00:00';
        $duration = 120;

        // Call the endpoint to set the master as available
        $response = $this->postJson('/api/masters/' . $master->id . '/availability', [
            'start_time' => $startTime,
            'duration' => $duration,
        ]);

        // Check if the response is successful
        $response->assertStatus(200);
        $response->assertJson(['message' => 'Master is available']);

        // Check if the master is marked as available in Redis
        $busyIntervals = Redis::zrangebyscore(
            "master:{$master->id}:free-intervals",
            '-inf',
            '+inf',
            'WITHSCORES'
        );

        $this->assertNotEmpty($busyIntervals);
        $this->assertEquals(Carbon::parse($startTime)->timestamp, $busyIntervals[0]);
    }
}
