<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Reservation extends Model
{
    use HasFactory;

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'tel_number',
        'res_date',
        'guest_number',
        'table_id',
    ];

    public function table(): BelongsTo
    {
        return $this->belongsTo(related: Table::class);
    }

}
