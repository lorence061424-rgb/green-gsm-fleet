<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SecurityLogArchive extends Model
{
    use HasFactory;

    protected $table = 'security_log_archives';

    protected $fillable = [
        'original_log_id',
        'event_type',
        'email',
        'ip_address',
        'user_agent',
        'details',
        'original_created_at',
        'archived_at',
        'archived_by',
    ];

    protected $casts = [
        'original_created_at' => 'datetime',
        'archived_at' => 'datetime',
    ];
}
