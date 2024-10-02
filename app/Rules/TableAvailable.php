<?php

namespace App\Rules;

use Carbon\Carbon;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Contracts\Validation\DataAwareRule;
use App\Models\Reservation;

class TableAvailable implements ValidationRule, DataAwareRule
{
    protected $data = [];

    public function setData($data)
    {
        $this->data = $data;
        return $this;
    }

    /**
     * Run the validation rule.
     *
     * @param string $attribute
     * @param mixed $value
     * @param \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $requestedDateTime = Carbon::parse($value);

        $tableId = $this->data['table_id'] ?? null;

        if (!$tableId) {
            $fail('A table must be selected to check availability.');
            return;
        }

        // Assume each reservation lasts 2 hours
        $reservationDuration = 120; // minutes

        $conflictingReservation = Reservation::where('table_id', $tableId)
            ->where(function ($query) use ($requestedDateTime, $reservationDuration) {
                $query->where(function ($q) use ($requestedDateTime, $reservationDuration) {
                    $q->where('res_date', '<=', $requestedDateTime)
                      ->where('res_date', '>', $requestedDateTime->copy()->subMinutes($reservationDuration));
                })->orWhere(function ($q) use ($requestedDateTime, $reservationDuration) {
                    $q->where('res_date', '>=', $requestedDateTime)
                      ->where('res_date', '<', $requestedDateTime->copy()->addMinutes($reservationDuration));
                });
            })
            ->exists();

        if ($conflictingReservation) {
            $fail('The selected time is not available for this table.');
        }
    }
}
