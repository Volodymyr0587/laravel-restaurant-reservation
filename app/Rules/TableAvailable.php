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
    protected $ignoreReservationId = null;

    public function __construct($ignoreReservationId = null)
    {
        $this->ignoreReservationId = $ignoreReservationId;
    }

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

        // Check for existing reservations on the same date
        $query = Reservation::where('table_id', $tableId)
            ->whereDate('res_date', $requestedDateTime->toDateString());

        // Exclude the current reservation if we're updating
        if ($this->ignoreReservationId) {
            $query->where('id', '!=', $this->ignoreReservationId);
        }

        $conflictingReservation = $query->exists();

        if ($conflictingReservation) {
            $fail('The selected table is not available on this date.');
        }
    }
}
