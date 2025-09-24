<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Carbon\Carbon;
use DateTime;

class MultipleDateFormat implements Rule
{
    protected ?Carbon $parsedDate = null;

    public function passes($attribute, $value): bool
    {
        // Acceptable formats
        $formats = [
            'm/d/Y h:i A', // 12/25/2025 03:00 PM
            'm/d/Y',       // 12/25/2025
        ];

        // Strict regex whitelist
        $patterns = [
            '/^\d{2}\/\d{2}\/\d{4}$/',                     // date only
            '/^\d{2}\/\d{2}\/\d{4}\s\d{2}:\d{2}\s?(AM|PM)$/i', // date + time
        ];

        $validPattern = false;
        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $value)) {
                $validPattern = true;
                break;
            }
        }

        if (!$validPattern) {
            return false; // 🚫 immediately reject junk like "add..add"
        }

        // Try parsing with DateTime first
        foreach ($formats as $format) {
            try {
                    $dt = DateTime::createFromFormat($format, $value);
                $errors = DateTime::getLastErrors();

                if ($dt && empty($errors['error']) && empty($errors['warning'])) {
                    // Safe to re-parse with Carbon
                    $this->parsedDate = Carbon::createFromFormat($format, $value);
                    return true;
                }
            } catch (\Exception $e) {
                // Continue to next format
                continue;
            }
        }

        return false; // 🚫 not a valid date
    }

    public function message(): string
    {
        return 'The :attribute must be a valid date in format m/d/Y or m/d/Y h:i A.';
    }

    public function getDate(): ?Carbon
    {
        return $this->parsedDate;
    }

    
}