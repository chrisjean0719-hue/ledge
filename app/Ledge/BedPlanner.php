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
        [$innerLength, $innerWidth] = Layout::innerSize($length, $width, $thickness);
        $height = $courses * $courseHeight;
        $volume = Layout::soilVolumeMm3($innerLength, $innerWidth, $height);

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
            'timberLengthMm' => Layout::timberLengthMm($length, $width, $courses),
            'soilVolumeMm3' => $volume,
            'soilLitres' => Layout::soilLitres($volume),
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

        return $filled;
    }
}
