<?php

namespace Tests\Unit;

use App\Ledge\Layout;
use PHPUnit\Framework\TestCase;

class LayoutTest extends TestCase
{
    public function test_the_sample_opening_and_the_timber_ring(): void
    {
        $this->assertSame([2200, 1000], Layout::innerSize(2400, 1200, 100));
        $this->assertSame(14400, Layout::timberLengthMm(2400, 1200, 2));
        $this->assertSame(550_000_000, Layout::soilVolumeMm3(2200, 1000, 250));
        $this->assertSame(550, Layout::soilLitres(550_000_000));
    }

    public function test_a_thick_sleeper_can_close_the_opening(): void
    {
        $this->assertNull(Layout::innerSize(800, 600, 150));
    }
}
