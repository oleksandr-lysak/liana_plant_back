<?php

namespace App\Models;

use App\Http\Casts\CustomRawPhoneNumberCast;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;
use Tymon\JWTAuth\Contracts\JWTSubject;


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
        //'phone' => CustomRawPhoneNumberCast::class.':INTERNATIONAL',
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
        'service_id'
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

    public function services(): BelongsToMany
    {
        return $this->belongsToMany(Service::class, 'master_services');
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function time_slots(): HasMany
    {
        return $this->hasMany(TimeSlot::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function workSchedules(): HasMany
    {
        return $this->hasMany(WorkSchedule::class);
    }
}
