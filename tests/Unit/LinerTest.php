<?php

namespace Tests\Unit;

use App\Ledge\BedException;
use App\Ledge\BedFormatter;
use App\Ledge\BedPlanner;
use App\Ledge\Layout;
use App\Ledge\SampleBed;
use PHPUnit\Framework\TestCase;

class LinerTest extends TestCase
{
    public function test_a_liner_covers_the_floor_and_the_walls(): void
    {
        $this->assertSame(3_800_000, Layout::linerAreaMm2(2200, 1000, 250));
        $plan = BedPlanner::plan([...SampleBed::kitchen(), 'liner' => true]);
        $this->assertTrue($plan['liner']);
        $this->assertSame(3_800_000, $plan['linerAreaMm2']);
        $this->assertSame(14400, $plan['timberLengthMm']);
        $this->assertSame(550, $plan['soilLitres']);
        $this->assertStringContainsString('liner: 3800000 mm²', BedFormatter::format($plan));
    }

    public function test_a_non_boolean_liner_is_rejected(): void
    {
        try {
            BedPlanner::plan([...SampleBed::kitchen(), 'liner' => 'yes']);
            $this->fail('A non-boolean liner should be rejected.');
        } catch (BedException $exception) {
            $this->assertSame(['liner: liner must be true or false'], $exception->errors);
        }
    }
}
