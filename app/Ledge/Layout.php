<?php

namespace App\Ledge;

class Layout
{
    /**
     * @return array{0: int, 1: int}|null
     */
    public static function innerSize(int $lengthMm, int $widthMm, int $thicknessMm): ?array
    {
        $innerLength = $lengthMm - 2 * $thicknessMm;
        $innerWidth = $widthMm - 2 * $thicknessMm;
        if ($innerLength < Sizes::MIN_INNER_MM || $innerWidth < Sizes::MIN_INNER_MM) {
            return null;
        }

        return [$innerLength, $innerWidth];
    }

    public static function timberLengthMm(int $lengthMm, int $widthMm, int $courses): int
    {
        return $courses * 2 * ($lengthMm + $widthMm);
    }

    public static function soilVolumeMm3(int $innerLengthMm, int $innerWidthMm, int $soilHeightMm): int
    {
        return $innerLengthMm * $innerWidthMm * $soilHeightMm;
    }

    public static function soilLitres(int $volumeMm3): int
    {
        return intdiv($volumeMm3, 1_000_000);
    }

    public static function linerAreaMm2(int $innerLengthMm, int $innerWidthMm, int $heightMm): int
    {
        $floor = $innerLengthMm * $innerWidthMm;
        $walls = 2 * ($innerLengthMm + $innerWidthMm) * $heightMm;

        return $floor + $walls;
    }
}
