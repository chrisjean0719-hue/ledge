<?php

namespace Tests\Unit;

use App\Ledge\BedException;
use App\Ledge\BedFormatter;
use App\Ledge\BedPlanner;
use App\Ledge\SampleBed;
use PHPUnit\Framework\TestCase;

class PlanTest extends TestCase
{
    public function test_the_sample_kitchen_ledge(): void
    {
        $plan = BedPlanner::plan(SampleBed::kitchen());
        $this->assertSame(250, $plan['heightMm']);
        $this->assertSame(2200, $plan['innerLengthMm']);
        $this->assertSame(1000, $plan['innerWidthMm']);
        $this->assertSame(14400, $plan['timberLengthMm']);
        $this->assertSame(550_000_000, $plan['soilVolumeMm3']);
        $this->assertSame(550, $plan['soilLitres']);
        $text = BedFormatter::format($plan);
        $this->assertStringStartsWith('Sample kitchen ledge', $text);
        $this->assertStringContainsString('timber: 14400 mm', $text);
        $this->assertStringContainsString('soil: 550 L', $text);
    }

    public function test_missing_thickness_height_and_courses_keep_the_defaults(): void
    {
        $plan = BedPlanner::plan([
            'name' => ' Herb strip ',
            'lengthMm' => 1200,
            'widthMm' => 800,
        ]);
        $this->assertSame('Herb strip', $plan['name']);
        $this->assertSame(100, $plan['thicknessMm']);
        $this->assertSame(125, $plan['courseHeightMm']);
        $this->assertSame(2, $plan['courses']);
        $this->assertSame(1000, $plan['innerLengthMm']);
        $this->assertSame(600, $plan['innerWidthMm']);
        $this->assertSame(8000, $plan['timberLengthMm']);
        $this->assertSame(150, $plan['soilLitres']);
    }

    public function test_a_nameless_bed_and_a_tight_sleeper_are_rejected(): void
    {
        try {
            BedPlanner::plan([...SampleBed::kitchen(), 'name' => '  ']);
            $this->fail('A blank name should be rejected.');
        } catch (BedException $exception) {
            $this->assertSame(['name: Bed needs a name'], $exception->errors);
        }

        try {
            BedPlanner::plan([
                'name' => 'Tight',
                'lengthMm' => 800,
                'widthMm' => 600,
                'thicknessMm' => 150,
                'courseHeightMm' => 125,
                'courses' => 1,
            ]);
            $this->fail('A tight sleeper should be rejected.');
        } catch (BedException $exception) {
            $this->assertSame(['thicknessMm: the sleepers leave an opening under 400 mm'], $exception->errors);
        }
    }
}
