<?php

namespace App\Ledge;

class SampleBed
{
    /**
     * @return array{name: string, lengthMm: int, widthMm: int, thicknessMm: int, courseHeightMm: int, courses: int}
     */
    public static function kitchen(): array
    {
        return [
            'name' => 'Sample kitchen ledge',
            'lengthMm' => 2400,
            'widthMm' => 1200,
            'thicknessMm' => 100,
            'courseHeightMm' => 125,
            'courses' => 2,
        ];
    }
}
