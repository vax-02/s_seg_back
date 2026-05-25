<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'user_id',
        'device_id',
        'status',
        'type',      
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function assignedUser()
    {
        return $this->belongsTo(User::class, 'assigned_user_id');
    }
    public function device()
    {
        return $this->belongsTo(Device::class);
    }

    public function files()
    {
        return $this->hasMany(TicketFile::class);
    }
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }
    public function logs()
    {
        return $this->hasMany(TicketLog::class);
    }
}
