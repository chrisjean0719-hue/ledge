<?php

namespace Tests\Unit;

use App\Ledge\Elevation;
use App\Ledge\BedPlanner;
use App\Ledge\SampleBed;
use PHPUnit\Framework\TestCase;

class ElevationTest extends TestCase
{
    public function test_the_sample_elevation_puts_the_soil_between_the_courses(): void
    {
        $svg = Elevation::svg(BedPlanner::plan(SampleBed::kitchen()));
        $this->assertStringStartsWith('<?xml version="1.0" encoding="UTF-8"?>', $svg);
        $this->assertStringContainsString('<title>Sample kitchen ledge</title>', $svg);
        $this->assertStringContainsString('viewBox="0 0 2400 266"', $svg);
        $this->assertStringContainsString('x="100" y="16" width="2200" height="250" fill="#c4b48a"', $svg);
        $this->assertStringContainsString('x="0" y="16" width="100" height="125" fill="#b08968"', $svg);
        $this->assertStringContainsString('x="0" y="141" width="100" height="125" fill="#8d6b4a"', $svg);
        $this->assertStringContainsString('x="2300" y="16" width="100" height="125" fill="#b08968"', $svg);
        $this->assertSame(2, substr_count($svg, 'fill="#b08968"'));
        $this->assertSame(2, substr_count($svg, 'fill="#8d6b4a"'));
        $this->assertSame('sample-kitchen-ledge-elevation.svg', Elevation::fileName('Sample kitchen ledge'));
    }

    public function test_drainage_sits_under_the_soil_and_stakes_hang_below(): void
    {
        $drained = Elevation::svg(BedPlanner::plan([...SampleBed::kitchen(), 'drainageMm' => 50]));
        $this->assertStringContainsString('x="100" y="16" width="2200" height="200" fill="#c4b48a"', $drained);
        $this->assertStringContainsString('x="100" y="216" width="2200" height="50" fill="#b7ad9e"', $drained);

        $lined = Elevation::svg(BedPlanner::plan([...SampleBed::kitchen(), 'liner' => true]));
        $this->assertSame(4, substr_count($lined, 'fill="#5f8a6a"'));
        $this->assertStringContainsString('x="100" y="16" width="2200" height="8" fill="#5f8a6a"', $lined);

        $staked = Elevation::svg(BedPlanner::plan([...SampleBed::kitchen(), 'setMm' => 400]));
        $this->assertStringContainsString('viewBox="0 0 2480 666"', $staked);
        $this->assertStringContainsString('x="0" y="16" width="40" height="650" fill="#6e5344"', $staked);
        $this->assertStringContainsString('x="2440" y="16" width="40" height="650" fill="#6e5344"', $staked);
        $this->assertStringContainsString('x="40" y="16" width="100" height="125" fill="#b08968"', $staked);
        $this->assertStringContainsString('x="140" y="16" width="2200" height="250" fill="#c4b48a"', $staked);
    }
}
