<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeviceAssignmentHistory extends Model
{
    protected $table = 'device_assignments_history';

    protected $fillable = [
        'user_id',
        'device_id',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function device()
    {
        return $this->belongsTo(Device::class);
    }
}
