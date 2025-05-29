<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Master extends Model
{
    use HasFactory;

    protected $hidden = [
        'created_at',
        'updated_at',
        'password',
        'photo',
    ];

    protected $casts = [
        // 'address' => 'json',
        // 'phone' => CustomRawPhoneNumberCast::class.':INTERNATIONAL',
    ];

    protected $fillable = [
        'name',
        'password',
        'phone',
        'address',
        'latitude',
        'longitude',
        'description',
        'age',
        'photo',
        'service_id',
        'slug',
        'approved',
    ];

    public function services(): BelongsToMany
    {
        return $this->belongsToMany(Service::class, 'master_services');
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
