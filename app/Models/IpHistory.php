<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IpHistory extends Model
{
    protected $fillable = ['user_id', 'ip', 'payload'];
    protected $casts = ['payload' => 'array'];
}
