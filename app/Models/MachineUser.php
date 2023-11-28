<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MachineUser extends Model
{
    use HasFactory;

    protected $fillable = [
        'machine_id',
        'user_id',
    ];

    public function user()
    {
        return $this->hasMany(User::class, 'user_id', 'id');
    }

    public function machine()
    {
        return $this->hasOne(Machine::class, 'machine_id', 'id');
    }
}
