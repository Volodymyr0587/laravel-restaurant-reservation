<?php

namespace App\Rules;

use Carbon\Carbon;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class TimeBetween implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $date = Carbon::parse($value);
        $time = Carbon::createFromTime($date->hour, $date->minute, $date->second);
        // when the restaraunt is open
        $earliestTime = Carbon::createFromTimeString('17:00:00');
        $lastTime = Carbon::createFromTimeString('23:00:00');

        if (!$time->between($earliestTime, $lastTime)) {
            $fail("Please choose the time between 17:00 and 23:00");
        }
    }
}
