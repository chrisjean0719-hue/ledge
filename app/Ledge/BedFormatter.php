<?php

namespace App\Ledge;

class BedFormatter
{
    /**
     * @param  array<string, int|string>  $plan
     */
    public static function format(array $plan): string
    {
        return implode("\n", [
            $plan['name'],
            "bed: {$plan['lengthMm']} mm by {$plan['widthMm']} mm",
            "courses: {$plan['courses']} at {$plan['courseHeightMm']} mm, height {$plan['heightMm']} mm",
            "inner: {$plan['innerLengthMm']} mm by {$plan['innerWidthMm']} mm",
            $plan['liner'] ? "liner: {$plan['linerAreaMm2']} mm²" : 'liner: none',
            $plan['drainageMm'] === 0
                ? 'drainage: none'
                : "drainage: {$plan['drainageMm']} mm, gravel {$plan['gravelLitres']} L",
            "timber: {$plan['timberLengthMm']} mm",
            "soil: {$plan['soilLitres']} L",
        ]);
    }
}
