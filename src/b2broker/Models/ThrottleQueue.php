<?php

namespace B2Broker\Models;

use Illuminate\Database\Eloquent\Model;

class ThrottleQueue extends Model
{
    protected $fillable = [
        'model_id',
        'model_class'
    ];
}
