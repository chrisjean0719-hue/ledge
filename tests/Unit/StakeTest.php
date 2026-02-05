<?php

namespace Tests\Unit;

use App\Ledge\BedException;
use App\Ledge\BedFormatter;
use App\Ledge\BedPlanner;
use App\Ledge\Layout;
use App\Ledge\SampleBed;
use PHPUnit\Framework\TestCase;

class StakeTest extends TestCase
{
    public function test_a_400_mm_set_places_four_stakes_below_the_bed(): void
    {
        $this->assertSame(650, Layout::stakeLengthMm(250, 400));
        $this->assertSame(2600, Layout::stakeWoodMm(650));
        $plan = BedPlanner::plan([...SampleBed::kitchen(), 'setMm' => 400]);
        $this->assertSame(4, $plan['stakeCount']);
        $this->assertSame(650, $plan['stakeLengthMm']);
        $this->assertSame(17000, $plan['timberLengthMm']);
        $this->assertSame(550, $plan['soilLitres']);
        $this->assertStringContainsString('stakes: 4 at 650 mm, set 400 mm', BedFormatter::format($plan));
    }

    public function test_a_set_of_200_mm_is_rejected(): void
    {
        try {
            BedPlanner::plan([...SampleBed::kitchen(), 'setMm' => 200]);
            $this->fail('A short set should be rejected.');
        } catch (BedException $exception) {
            $this->assertSame(['setMm: setMm must be an integer from 300 to 900'], $exception->errors);
        }
    }
}
