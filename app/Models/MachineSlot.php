<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MachineSlot extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'machine_id'
    ];

    public function items()
    {
        return $this->hasMany(MachineSlotItem::class, 'machine_slot_id', 'id');
    }
}
