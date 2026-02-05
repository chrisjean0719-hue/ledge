<?php

namespace App\Ledge;

class BedPlanner
{
    /**
     * @return array<string, int|string>
     */
    public static function plan(mixed $bed): array
    {
        $filled = self::withDefaults($bed);
        $errors = BedValidator::validate($filled);
        if ($errors !== []) {
            throw new BedException($errors);
        }

        $length = $filled['lengthMm'];
        $width = $filled['widthMm'];
        $thickness = $filled['thicknessMm'];
        $courseHeight = $filled['courseHeightMm'];
        $courses = $filled['courses'];
        $liner = (bool) ($filled['liner'] ?? false);
        $drainage = $filled['drainageMm'] ?? 0;
        $set = $filled['setMm'] ?? 0;
        [$innerLength, $innerWidth] = Layout::innerSize($length, $width, $thickness);
        $height = $courses * $courseHeight;
        $soilHeight = $height - $drainage;
        $soilVolume = Layout::soilVolumeMm3($innerLength, $innerWidth, $soilHeight);
        $gravelVolume = $drainage === 0 ? 0 : Layout::soilVolumeMm3($innerLength, $innerWidth, $drainage);
        $stakeLength = $set === 0 ? 0 : Layout::stakeLengthMm($height, $set);
        $stakeWood = $set === 0 ? 0 : Layout::stakeWoodMm($stakeLength);

        return [
            'name' => trim($filled['name']),
            'lengthMm' => $length,
            'widthMm' => $width,
            'thicknessMm' => $thickness,
            'courseHeightMm' => $courseHeight,
            'courses' => $courses,
            'heightMm' => $height,
            'innerLengthMm' => $innerLength,
            'innerWidthMm' => $innerWidth,
            'liner' => $liner,
            'linerAreaMm2' => $liner ? Layout::linerAreaMm2($innerLength, $innerWidth, $height) : 0,
            'drainageMm' => $drainage,
            'soilHeightMm' => $soilHeight,
            'gravelVolumeMm3' => $gravelVolume,
            'gravelLitres' => Layout::soilLitres($gravelVolume),
            'setMm' => $set,
            'stakeCount' => $set === 0 ? 0 : 4,
            'stakeLengthMm' => $stakeLength,
            'timberLengthMm' => Layout::timberLengthMm($length, $width, $courses) + $stakeWood,
            'soilVolumeMm3' => $soilVolume,
            'soilLitres' => Layout::soilLitres($soilVolume),
        ];
    }

    private static function withDefaults(mixed $bed): mixed
    {
        if (! is_array($bed) || array_is_list($bed)) {
            return $bed;
        }
        $filled = $bed;
        $filled['thicknessMm'] ??= Sizes::DEFAULT_THICKNESS_MM;
        $filled['courseHeightMm'] ??= Sizes::DEFAULT_COURSE_HEIGHT_MM;
        $filled['courses'] ??= Sizes::DEFAULT_COURSES;
        if (! array_key_exists('liner', $filled) || $filled['liner'] === null) {
            $filled['liner'] = false;
        }

        return $filled;
    }
}
