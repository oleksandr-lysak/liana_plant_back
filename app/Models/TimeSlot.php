<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TimeSlot extends Model
{
    use HasFactory;

    protected $fillable = [
        'master_id',
        'date',
        'time',
        'is_booked',
        'client_name',
        'service',
    ];

    public function master()
    {
        return $this->belongsTo(Master::class);
    }
}