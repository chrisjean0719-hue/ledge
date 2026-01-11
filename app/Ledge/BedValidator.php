<?php

namespace App\Ledge;

class BedValidator
{
    /**
     * @return list<string>
     */
    public static function validate(mixed $bed): array
    {
        if (! is_array($bed) || array_is_list($bed)) {
            return ['bed: Bed must be an object'];
        }

        $errors = [];
        $name = $bed['name'] ?? null;
        if (! is_string($name) || trim($name) === '') {
            $errors[] = 'name: Bed needs a name';
        }
        if (! self::inRange($bed['lengthMm'] ?? null, Sizes::MIN_LENGTH_MM, Sizes::MAX_LENGTH_MM)) {
            $errors[] = 'lengthMm: lengthMm must be an integer from '.Sizes::MIN_LENGTH_MM.' to '.Sizes::MAX_LENGTH_MM;
        }
        if (! self::inRange($bed['widthMm'] ?? null, Sizes::MIN_WIDTH_MM, Sizes::MAX_WIDTH_MM)) {
            $errors[] = 'widthMm: widthMm must be an integer from '.Sizes::MIN_WIDTH_MM.' to '.Sizes::MAX_WIDTH_MM;
        }
        if (! self::inRange($bed['thicknessMm'] ?? null, Sizes::MIN_THICKNESS_MM, Sizes::MAX_THICKNESS_MM)) {
            $errors[] = 'thicknessMm: thicknessMm must be an integer from '.Sizes::MIN_THICKNESS_MM.' to '.Sizes::MAX_THICKNESS_MM;
        }
        if (! self::inRange($bed['courseHeightMm'] ?? null, Sizes::MIN_COURSE_HEIGHT_MM, Sizes::MAX_COURSE_HEIGHT_MM)) {
            $errors[] = 'courseHeightMm: courseHeightMm must be an integer from '.Sizes::MIN_COURSE_HEIGHT_MM.' to '.Sizes::MAX_COURSE_HEIGHT_MM;
        }
        if (! self::inRange($bed['courses'] ?? null, Sizes::MIN_COURSES, Sizes::MAX_COURSES)) {
            $errors[] = 'courses: courses must be an integer from '.Sizes::MIN_COURSES.' to '.Sizes::MAX_COURSES;
        }
        if ($errors !== []) {
            return $errors;
        }

        if (Layout::innerSize($bed['lengthMm'], $bed['widthMm'], $bed['thicknessMm']) === null) {
            $errors[] = 'thicknessMm: the sleepers leave an opening under '.Sizes::MIN_INNER_MM.' mm';
        }

        return $errors;
    }

    private static function inRange(mixed $value, int $min, int $max): bool
    {
        return is_int($value) && $value >= $min && $value <= $max;
    }
}
