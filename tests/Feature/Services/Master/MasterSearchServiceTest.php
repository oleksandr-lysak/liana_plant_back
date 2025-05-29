<?php

namespace Tests\Feature\Services\Master\Services\Master;

use App\Http\Services\Master\MasterSearchService;
use Mockery;
use Tests\TestCase;

class MasterSearchServiceTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_getMastersOnDistance_calls_db_with_correct_query_and_params()
    {
        $dbMock = Mockery::mock();
        $filterServiceMock = Mockery::mock();
        //$service = new MasterSearchService($dbMock, $filterServiceMock);
        $service = new MasterSearchService();

        $lat = 50.0;
        $lng = 30.0;
        $zoom = 10.0;
        $filters = [];
        $perPage = 5;
        $page = 2;

        $dbMock->shouldReceive('select')->once()->withArgs(function ($query, $params) use ($lat, $lng, $zoom, $perPage, $page) {
            $this->assertStringContainsString('SELECT', $query);
            $this->assertEquals($lat, $params['distance_lat']);
            $this->assertEquals($lng, $params['distance_lng']);
            $this->assertEquals($lat, $params['distance_lat2']);
            $this->assertArrayHasKey('max_distance', $params);
            return true;
        })->andReturn([['id' => 1]]);

        $filterServiceMock->shouldReceive('applyFilters')->once();

        $result = $service->getMastersOnDistance($lat, $lng, $zoom, $filters, $perPage, $page);
        dd($result);
        $this->assertEquals([['id' => 1]], $result);
    }

    public function test_calculateSearchRadius_returns_expected_value()
    {
        $reflection = new \ReflectionClass(MasterSearchService::class);
        $method = $reflection->getMethod('calculateSearchRadius');
        $method->setAccessible(true);
        $zoom = 5;
        $expected = 20037.5 / $zoom;
        $this->assertEquals($expected, $method->invoke(null, $zoom));
    }

    public function test_getMastersOnDistance_applies_filters()
    {
        $dbMock = Mockery::mock();
        $filterServiceMock = Mockery::mock();
        $service = new MasterSearchService($dbMock, $filterServiceMock);

        $dbMock->shouldReceive('select')->andReturn([]);
        $filterServiceMock->shouldReceive('applyFilters')->once()->withArgs(function ($filters, &$query, &$params) {
            $filters['name'] = 'Test';
            return true;
        });

        $service->getMastersOnDistance(50, 30, 10, ['name' => 'Test'], 10, 1);
    }

    public function test_getMastersOnDistance_returns_empty_array_when_no_results()
    {
        $dbMock = Mockery::mock();
        $filterServiceMock = Mockery::mock();
        $service = new MasterSearchService($dbMock, $filterServiceMock);

        $dbMock->shouldReceive('select')->andReturn([]);
        $filterServiceMock->shouldReceive('applyFilters');

        $result = $service->getMastersOnDistance(50, 30, 10, [], 10, 1);
        $this->assertEquals([], $result);
    }
} 