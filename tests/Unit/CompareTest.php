<?php

namespace Tests\Unit;

use App\Ledge\BedPlanner;
use App\Ledge\Compare;
use App\Ledge\SampleBed;
use PHPUnit\Framework\TestCase;

class CompareTest extends TestCase
{
    public function test_the_sample_has_more_timber_and_more_soil_than_a_herb_strip(): void
    {
        $left = BedPlanner::plan(SampleBed::kitchen());
        $right = BedPlanner::plan([
            'name' => 'Herb strip',
            'lengthMm' => 1200,
            'widthMm' => 800,
        ]);
        $diff = Compare::beds($left, $right);
        $this->assertSame(['timberLengthMm' => [14400, 8000], 'soilLitres' => [550, 150]], $diff);
        $this->assertSame(
            "Sample kitchen ledge vs Herb strip\ntimber: 14400 mm vs 8000 mm\nsoil: 550 L vs 150 L",
            Compare::format($left, $right, $diff),
        );
    }

    public function test_stakes_are_included_in_the_timber_being_compared(): void
    {
        $left = BedPlanner::plan(SampleBed::kitchen());
        $right = BedPlanner::plan([...SampleBed::kitchen(), 'name' => 'Staked ledge', 'setMm' => 400]);
        $diff = Compare::beds($left, $right);
        $this->assertSame([14400, 17000], $diff['timberLengthMm']);
        $this->assertSame([550, 550], $diff['soilLitres']);
    }
}
