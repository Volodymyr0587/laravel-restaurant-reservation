<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'image',
    ];

    protected static function boot()
    {
        parent::boot();

        static::updating(function ($category) {
            if (request()->hasFile('image')) {
                if ($category->getOriginal('image')) {
                    Storage::disk('public')->delete($category->getOriginal('image'));
                }
            }
        });

        static::deleting(function ($category) {
            if ($category->image) {
                Storage::disk('public')->delete($category->image);
            }
        });
    }
}
