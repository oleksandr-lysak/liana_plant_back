<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Storage;

class Master extends Model
{
    use HasFactory;

    protected $hidden = [
        'created_at',
        'updated_at',
        'password',
        'photo',
    ];

    protected $appends = [
        'image',
        'rating',
        'formattedAddress'
    ];

    public function getImageAttribute(): string
    {
        return env('APP_URL').Storage::url($this->photo);
    }

    public function getRatingAttribute(): int
    {
        return rand(0,5);
    }

    public function getFormattedAddressAttribute(): string
    {
        try {
            return json_decode($this->address)->results[1]->formatted_address;
        } catch (\Exception $e) {
            return '';
        }
    }

    public function speciality(): HasOne
    {
        return $this->hasOne(Speciality::class,'id','speciality_id');
    }
}
