<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Jobdesk extends Model
{
    protected $fillable = ['title', 'content'];

    public  function jobdeskable()
    {
        return $this->morphTo();
    }
}
