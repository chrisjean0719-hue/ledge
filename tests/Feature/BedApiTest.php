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

        $bad = $this->postJson('/api/beds', [
            'name' => '',
            'lengthMm' => 2400,
            'widthMm' => 1200,
        ]);
        $bad->assertStatus(400);
        $bad->assertExactJson(['errors' => ['name: Bed needs a name']]);
    }
}
