<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MembershipClass extends Model
{
    protected $table = 'membership_classes';

    protected $fillable = [
        'm_name',
    ];
}

