<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupporterProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'pref_photo',
        'ac_id',
        'self_introduction',
        'skill1',
        'skill2',
        'skill3',
        'skill4',
        'skill5',
        'latitude',
        'longitude',
    ];

    /**
     * ユーザーとのリレーション
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
