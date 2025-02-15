<?php

namespace App\Http\Requests;

use Closure;
use Carbon\Carbon;
use App\Models\Table;
use App\Rules\DateBetween;
use App\Rules\TimeBetween;
use App\Models\Reservation;
use App\Rules\TableAvailable;
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
                new DateBetween(),
                new TimeBetween(),
                new TableAvailable(),
            ],
            'guest_number' => 'required|integer|min:1',
            'table_id' => [
                'required',
                'exists:tables,id', // Make sure the selected table exists
            ],
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $table = Table::find($this->table_id);
            if ($table && $this->guest_number > $table->guest_number) {
                $validator->errors()->add('guest_number', 'The number of guests exceeds the table capacity. Please choose a table based on the number of guests.');
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
