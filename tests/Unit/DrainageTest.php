<?php

namespace Tests\Unit;

use App\Ledge\BedException;
use App\Ledge\BedFormatter;
use App\Ledge\BedPlanner;
use App\Ledge\SampleBed;
use PHPUnit\Framework\TestCase;

class DrainageTest extends TestCase
{
    public function test_a_50_mm_drainage_layer_shrinks_the_soil_and_adds_gravel(): void
    {
        $plan = BedPlanner::plan([...SampleBed::kitchen(), 'drainageMm' => 50]);
        $this->assertSame(50, $plan['drainageMm']);
        $this->assertSame(200, $plan['soilHeightMm']);
        $this->assertSame(110_000_000, $plan['gravelVolumeMm3']);
        $this->assertSame(110, $plan['gravelLitres']);
        $this->assertSame(440_000_000, $plan['soilVolumeMm3']);
        $this->assertSame(440, $plan['soilLitres']);
        $this->assertSame(14400, $plan['timberLengthMm']);
        $this->assertStringContainsString('drainage: 50 mm, gravel 110 L', BedFormatter::format($plan));
    }

    public function test_a_liner_still_covers_the_full_height_with_drainage(): void
    {
        $plan = BedPlanner::plan([...SampleBed::kitchen(), 'liner' => true, 'drainageMm' => 50]);
        $this->assertSame(3_800_000, $plan['linerAreaMm2']);
        $this->assertSame(440, $plan['soilLitres']);
    }

    public function test_a_shallow_layer_and_a_layer_that_uses_up_the_soil_are_rejected(): void
    {
        try {
            BedPlanner::plan([...SampleBed::kitchen(), 'drainageMm' => 20]);
            $this->fail('A shallow drainage layer should be rejected.');
        } catch (BedException $exception) {
            $this->assertSame(['drainageMm: drainageMm must be an integer from 40 to 150'], $exception->errors);
        }

        try {
            BedPlanner::plan([...SampleBed::kitchen(), 'courses' => 1, 'drainageMm' => 50]);
            $this->fail('A drainage layer that leaves short soil should be rejected.');
        } catch (BedException $exception) {
            $this->assertSame(['drainageMm: the drainage layer leaves the soil under 100 mm'], $exception->errors);
        }
    }
}
