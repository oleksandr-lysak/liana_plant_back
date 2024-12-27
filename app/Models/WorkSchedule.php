<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WorkSchedule extends Model
{
    use HasFactory;

    protected $table = 'work_schedule';

    protected $fillable = [
        'master_id',
        'day_of_week',
        'start_time',
        'end_time',
    ];

    public function master(): BelongsTo
    {
        return $this->belongsTo(Master::class);
    }
}
