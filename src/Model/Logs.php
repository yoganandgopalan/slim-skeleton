<?php

namespace App\Model;

use Cartalyst\Sentinel\Users\EloquentUser;

class Logs extends EloquentUser
{
    protected $table = 'logs';

    protected $primaryKey = 'id';

    protected $fillable = [
        'message'
    ];
}
