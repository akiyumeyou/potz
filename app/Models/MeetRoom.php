<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MeetRoom extends Model
{
    use HasFactory;

    protected $fillable = [
        'request_id',
    ];

    public function request()
    {
        return $this->belongsTo(Userrequest::class);
    }
}
