<?php

namespace App\Ledge;

class Elevation
{
    public const LABEL_BAND = 16;

    public const STAKE_FACE = 40;

    private const SOIL = '#c4b48a';

    private const GRAVEL = '#b7ad9e';

    private const TIMBER_EVEN = '#b08968';

    private const TIMBER_ODD = '#8d6b4a';

    private const STAKE = '#6e5344';

    private const LINER = '#5f8a6a';

    private const INK = '#2b241c';

    /**
     * @param  array<string, int|string>  $plan
     */
    public static function svg(array $plan): string
    {
        $length = $plan['lengthMm'];
        $height = $plan['heightMm'];
        $set = $plan['setMm'];
        $originX = $plan['stakeCount'] === 0 ? 0 : self::STAKE_FACE;
        $originY = self::LABEL_BAND;
        $pictureWidth = $originX + $length + ($plan['stakeCount'] === 0 ? 0 : self::STAKE_FACE);
        $pictureHeight = self::LABEL_BAND + $height + $set;
        $lines = [
            '<?xml version="1.0" encoding="UTF-8"?>',
            '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 '.$pictureWidth.' '.$pictureHeight.'" width="'.$pictureWidth.'mm" height="'.$pictureHeight.'mm">',
            '  <title>'.self::escape($plan['name']).'</title>',
            '  <text x="0" y="12" font-size="11" fill="'.self::INK.'">Ledge</text>',
        ];
        $innerX = $originX + $plan['thicknessMm'];
        $lines[] = self::rect($innerX, $originY, $plan['innerLengthMm'], $plan['soilHeightMm'], self::SOIL);
        if ($plan['drainageMm'] > 0) {
            $lines[] = self::rect($innerX, $originY + $plan['soilHeightMm'], $plan['innerLengthMm'], $plan['drainageMm'], self::GRAVEL);
        }
        if ($plan['liner']) {
            $lines = array_merge($lines, self::liner($plan, $originX, $originY));
        }
        $lines = array_merge($lines, self::courses($plan, $originX, $originY));
        if ($plan['stakeCount'] === 4) {
            $lines[] = self::rect(0, $originY, self::STAKE_FACE, $plan['stakeLengthMm'], self::STAKE);
            $lines[] = self::rect($originX + $length, $originY, self::STAKE_FACE, $plan['stakeLengthMm'], self::STAKE);
        }
        $lines[] = '</svg>';
        $lines[] = '';

        return implode("\n", $lines);
    }

    public static function fileName(string $name): string
    {
        $slug = strtolower(trim($name));
        $slug = preg_replace('/[^a-z0-9]+/', '-', $slug) ?? '';
        $slug = trim($slug, '-');

        return ($slug === '' ? 'bed' : $slug).'-elevation.svg';
    }

    /**
     * @param  array<string, int|string>  $plan
     * @return list<string>
     */
    private static function courses(array $plan, int $originX, int $originY): array
    {
        $lines = [];
        $rightX = $originX + $plan['lengthMm'] - $plan['thicknessMm'];
        for ($index = 0; $index < $plan['courses']; $index++) {
            $fill = $index % 2 === 0 ? self::TIMBER_EVEN : self::TIMBER_ODD;
            $y = $originY + $index * $plan['courseHeightMm'];
            $lines[] = self::rect($originX, $y, $plan['thicknessMm'], $plan['courseHeightMm'], $fill);
            $lines[] = self::rect($rightX, $y, $plan['thicknessMm'], $plan['courseHeightMm'], $fill);
        }

        return $lines;
    }

    /**
     * @param  array<string, int|string>  $plan
     * @return list<string>
     */
    private static function liner(array $plan, int $originX, int $originY): array
    {
        $band = 8;
        $innerX = $originX + $plan['thicknessMm'];
        $innerLength = $plan['innerLengthMm'];
        $height = $plan['heightMm'];

        return [
            self::rect($innerX, $originY, $innerLength, $band, self::LINER),
            self::rect($innerX, $originY + $height - $band, $innerLength, $band, self::LINER),
            self::rect($innerX, $originY, $band, $height, self::LINER),
            self::rect($innerX + $innerLength - $band, $originY, $band, $height, self::LINER),
        ];
    }

    private static function rect(int $x, int $y, int $width, int $height, string $fill): string
    {
        return '  <rect x="'.$x.'" y="'.$y.'" width="'.$width.'" height="'.$height.'" fill="'.$fill.'"/>';
    }

    private static function escape(string $value): string
    {
        return htmlspecialchars($value, ENT_XML1 | ENT_QUOTES, 'UTF-8');
    }
}
