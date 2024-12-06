<?php

namespace App\Models;

use App\Enums\JobName;
use Illuminate\Database\Eloquent\Model;

class JobLog extends Model
{
    protected $guarded = [];

    protected $casts = [
        'job' => JobName::class,
    ];
}
