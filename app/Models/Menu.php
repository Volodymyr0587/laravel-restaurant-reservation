<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Menu extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'image',
        'price',
    ];

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class);
    }

    protected static function boot()
    {
        parent::boot();

        static::updating(function ($menu) {
            if (request()->hasFile('image')) {
                if ($menu->getOriginal('image')) {
                    Storage::disk('public')->delete($menu->getOriginal('image'));
                }
            }
        });

        static::deleting(function ($menu) {
            if ($menu->image) {
                Storage::disk('public')->delete($menu->image);
            }
        });
    }
}
