<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $table = 'reviews';

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function master()
    {
        return $this->belongsTo(Master::class);
    }
    public function getUserNameAttribute()
    {
        return $this->user->name ?? '';
    }
}
