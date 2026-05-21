<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Device extends Model
{
    use HasFactory;

    protected $fillable = [
        'type_device_id',
        'brand',
        'model',
        'status',
        'assigned_to',
    ];

    /**
     * Mutator para generar código automáticamente si no existe
     */
    protected static function boot()
    {
        parent::boot();
    }
    public function assignments()
    {
        return $this->hasMany(DeviceAssignmentHistory::class);
    }
    public function currentAssignment()
    {
    return $this->hasOne(DeviceAssignmentHistory::class)
        ->where('status', 1);
    }
    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }
}
