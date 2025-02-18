<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    protected $fillable = ['name', 'email', 'phone', 'address', 'position_id', 'status', 'photo'];

    public function position()
    {
        return $this->belongsTo(Position::class, 'position_id');
    }

    public function jobdesks()
    {
        return $this->morphMany(Jobdesk::class, 'jobdeskable');
    }
}
