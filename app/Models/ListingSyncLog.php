<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ListingSyncLog extends Model
{
    protected $fillable = [
        'action', 'external_id', 'status_code', 'latency_ms',
        'request_body', 'response_body', 'error',
    ];

    protected $casts = [
        'request_body'  => 'array',
        'response_body' => 'array',
    ];
}
