<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Review extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected $table = 'reviews';

//    public function model(): MorphTo
//    {
//        return $this->morphTo();
//    }
//
//    public function user(): MorphTo
//    {
//        return $this->morphTo();
//    }
}
