<?php

namespace App\Models;

use App\Enums\TimeSlotStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TimeSlot extends Model
{
    use HasFactory;

    public mixed $master_id;
    public mixed $start_time;
    public mixed $end_time;
    protected $fillable = [
        'start_time',
        'end_time',
        'master_id',
        'date',
        'status',
        'client_id',
        'service_id',
        'comment',
    ];

    protected $casts = [
        'status' => TimeSlotStatus::class,
    ];


    public function master()
    {
        return $this->belongsTo(Master::class);
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }


}
