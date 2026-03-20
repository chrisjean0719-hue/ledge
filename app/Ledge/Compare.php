<?php

namespace App\Ledge;

class Compare
{
    /**
     * @param  array<string, int|string>  $left
     * @param  array<string, int|string>  $right
     * @return array{timberLengthMm: array{0: int, 1: int}, soilLitres: array{0: int, 1: int}}
     */
    public static function beds(array $left, array $right): array
    {
        return [
            'timberLengthMm' => [$left['timberLengthMm'], $right['timberLengthMm']],
            'soilLitres' => [$left['soilLitres'], $right['soilLitres']],
        ];
    }

    /**
     * @param  array<string, int|string>  $left
     * @param  array<string, int|string>  $right
     * @param  array{timberLengthMm: array{0: int, 1: int}, soilLitres: array{0: int, 1: int}}  $diff
     */
    public static function format(array $left, array $right, array $diff): string
    {
        return implode("\n", [
            $left['name'].' vs '.$right['name'],
            'timber: '.$diff['timberLengthMm'][0].' mm vs '.$diff['timberLengthMm'][1].' mm',
            'soil: '.$diff['soilLitres'][0].' L vs '.$diff['soilLitres'][1].' L',
        ]);
    }
}
