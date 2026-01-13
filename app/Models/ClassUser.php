<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class ClassUser extends Pivot
{
    protected $table = 'class_user';

    protected $casts = [
        'joined_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}