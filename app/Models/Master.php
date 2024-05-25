<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;
use Propaganistas\LaravelPhone\Casts\RawPhoneNumberCast;

/**
 * @method static find($id)
 * @method static firstOrCreate(array $searchByData, array $data)
 * @method static updateOrCreate(array $searchByData, array $data)
 * @property mixed $photo
 * @property String $address
 */
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

    protected $casts = [
        'address' => 'json',
        'phone' => RawPhoneNumberCast::class.':INTERNATIONAL',
    ];

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'address',
        'latitude',
        'longitude',
        'description',
        'age',
        'photo',
        'speciality_id'
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

    public function specialities(): BelongsToMany
    {
        return $this->belongsToMany(Speciality::class, 'master_speciality');
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }
}
