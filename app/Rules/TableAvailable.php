<?php

namespace App\Rules;

use Closure;
use App\Models\Reservation;
use Illuminate\Contracts\Validation\ValidationRule;

class TableAvailable implements ValidationRule
{
    public function __construct(protected $reservationDate)
    {
        $this->reservationDate = $reservationDate;
    }

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // Check if the table is already reserved on the given date
        $reservationExists = Reservation::where('table_id', $value)
            ->whereDate('res_date', $this->reservationDate)
            ->exists();

        if ($reservationExists) {
            $fail('The selected table is not available on this date.');
        }
    }
}
