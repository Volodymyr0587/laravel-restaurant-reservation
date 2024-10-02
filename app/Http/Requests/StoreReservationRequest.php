<?php

namespace App\Http\Requests;

use Carbon\Carbon;
use App\Models\Table;
use App\Models\Reservation;
use Illuminate\Foundation\Http\FormRequest;

class StoreReservationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'first_name' => 'required|string|min:2|max:256',
            'last_name' => 'required|string|min:2|max:256',
            'email' => 'required|email',
            'tel_number' => 'required|string',
            'res_date' => [
                'required',
                'date',
                'after_or_equal:today', // Date must be in the future or today
                function ($attribute, $value, $fail) {
                    $date = Carbon::parse($value);

                    // Additional logic (optional): If you have business hours or need to enforce a minimum time before reservations
                    if ($date->isPast()) {
                        $fail('The reservation date must be in the future.');
                    }

                    // Example: Prevent reservations for dates more than 1 year in advance
                    if ($date->greaterThan(Carbon::now()->addYear())) {
                        $fail('You cannot make a reservation for more than one year in advance.');
                    }
                }
            ],
            'guest_number' => 'required|integer|min:1',
            'table_id' => [
                'required',
                'exists:tables,id', // Make sure the selected table exists
                function ($attribute, $value, $fail) {
                    // Check if the table is already reserved on the given date
                    $reservationExists = Reservation::where('table_id', $value)
                        ->whereDate('reservation_date', $this->reservation_date)
                        ->exists();

                    if ($reservationExists) {
                        $fail('The selected table is not available on this date.');
                    }
                }
            ],
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $table = Table::find($this->table_id);
            if ($table && $this->guest_number > $table->guest_number) {
                $validator->errors()->add('guest_number', 'The number of guests exceeds the table capacity.');
            }
        });
    }

    public function messages()
    {
        return [
            'table_id.required' => 'Please select a table for the reservation.',
            'res_date.required' => 'Please select a valid reservation date.',
            'res_date.after_or_equal' => 'The reservation date must be today or in the future.',
        ];
    }
}
