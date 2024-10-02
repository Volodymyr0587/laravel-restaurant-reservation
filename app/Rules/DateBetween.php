<?php

namespace App\Rules;

use Closure;
use Carbon\Carbon;
use Illuminate\Contracts\Validation\ValidationRule;

class DateBetween implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $date = Carbon::parse($value);

        // Additional logic (optional): If you have business hours or need to enforce a minimum time before reservations
        if ($date->isPast()) {
            $fail('The reservation date must be in the future.');
        }

        // Example: Prevent reservations for dates more than 1 year in advance
        if ($date->greaterThan(Carbon::now()->addWeek())) {
            $fail('You cannot make a reservation for more than one week in advance.');
        }
    }
}
