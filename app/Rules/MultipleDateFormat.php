<?php

namespace App\Rules;

use Closure;

use Illuminate\Contracts\Validation\Rule;
use Carbon\Carbon;
/* use Carbon\Exceptions\InvalidFormatException;
use InvalidArgumentException;
use Illuminate\Contracts\Validation\ValidationRule; */

class MultipleDateFormat implements Rule
{
    public function passes($attribute, $value): bool
    {
        /* // 1. Allow only digits, slash, colon, space, and AM/PM
        if (!preg_match('/^[0-9\/:\sAPMapm]+$/', $value)) {
            return false; // 🚫 contains invalid characters
        } */

        // ✅ 1. Hard regex whitelist
        // Match either m/d/Y h:i AM|PM OR m/d/Y
        $dateOnlyPattern   = '/^\d{2}\/\d{2}\/\d{4}$/';
        $dateTimePattern   = '/^\d{2}\/\d{2}\/\d{4}\s\d{2}:\d{2}\s?(AM|PM)$/i';

        if (!preg_match($dateOnlyPattern, $value) && !preg_match($dateTimePattern, $value)) {
            return false; // 🚫 invalid structure, don't send to Carbon
        }

        $formats = [
            'm/d/Y h:i A', // e.g. 12/25/2025 02:00 PM
            'm/d/Y',       // e.g. 12/25/2025
        ];

        foreach ($formats as $format) {
            try {
                $date = Carbon::createFromFormat($format, $value);

                $errors = Carbon::getLastErrors();
                if ($date !== false && empty($errors['error']) && empty($errors['warning'])) {
                    return true; // ✅ valid date
                }
            } catch (\Exception $e) {
                continue; // try next format
            }
        }

        return false; // ❌ no format matched
    }

    public function message(): string
    {
        return 'The :attribute must be a valid date in format m/d/Y or m/d/Y h:i A.';
    }
}