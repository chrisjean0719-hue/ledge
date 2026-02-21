<?php

namespace Tests\Feature;

use App\Ledge\SampleBed;
use Tests\TestCase;

class BedApiTest extends TestCase
{
    public function test_health_and_the_sample_bed(): void
    {
        $this->getJson('/api/health')->assertOk()->assertExactJson(['status' => 'ok']);

        $sample = $this->getJson('/api/sample');
        $sample->assertOk();
        $sample->assertJsonPath('name', 'Sample kitchen ledge');
        $sample->assertJsonPath('timberLengthMm', 14400);
        $sample->assertJsonPath('soilLitres', 550);
        $sample->assertJsonPath('liner', false);
        $sample->assertJsonPath('linerAreaMm2', 0);
        $sample->assertJsonPath('drainageMm', 0);
        $sample->assertJsonPath('gravelLitres', 0);
        $sample->assertJsonPath('stakeCount', 0);
    }

    public function test_a_posted_bed_matches_the_sample_and_a_bad_bed_is_a_400(): void
    {
        $ok = $this->postJson('/api/beds', SampleBed::kitchen());
        $ok->assertOk();
        $ok->assertJsonPath('innerLengthMm', 2200);
        $ok->assertJsonPath('timberLengthMm', 14400);

        $lined = $this->postJson('/api/beds', [...SampleBed::kitchen(), 'liner' => true]);
        $lined->assertOk();
        $lined->assertJsonPath('liner', true);
        $lined->assertJsonPath('linerAreaMm2', 3_800_000);
        $lined->assertJsonPath('soilLitres', 550);

        $drained = $this->postJson('/api/beds', [...SampleBed::kitchen(), 'drainageMm' => 50]);
        $drained->assertOk();
        $drained->assertJsonPath('soilHeightMm', 200);
        $drained->assertJsonPath('gravelLitres', 110);
        $drained->assertJsonPath('soilLitres', 440);

        $staked = $this->postJson('/api/beds', [...SampleBed::kitchen(), 'setMm' => 400]);
        $staked->assertOk();
        $staked->assertJsonPath('stakeCount', 4);
        $staked->assertJsonPath('stakeLengthMm', 650);
        $staked->assertJsonPath('timberLengthMm', 17000);

        $bad = $this->postJson('/api/beds', [
            'name' => '',
            'lengthMm' => 2400,
            'widthMm' => 1200,
        ]);
        $bad->assertStatus(400);
        $bad->assertExactJson(['errors' => ['name: Bed needs a name']]);

        $elevation = $this->get('/api/sample/elevation');
        $elevation->assertOk();
        $elevation->assertHeader('content-type', 'image/svg+xml; charset=utf-8');
        $this->assertStringContainsString(
            'sample-kitchen-ledge-elevation.svg',
            (string) $elevation->headers->get('content-disposition'),
        );
        $elevation->assertSee('viewBox="0 0 2400 266"', false);

        $badDrawing = $this->postJson('/api/beds/elevation', [
            'name' => '',
            'lengthMm' => 2400,
            'widthMm' => 1200,
        ]);
        $badDrawing->assertStatus(400);
        $badDrawing->assertExactJson(['errors' => ['name: Bed needs a name']]);
    }
}
