<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MachineSlotItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'price',
        'quantity',
        'machine_slot_id',
        'image',
        'user_id',
    ];

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }
}
