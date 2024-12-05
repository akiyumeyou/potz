<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\SupporterProfile;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'real_name',
        'real_name_kana',
        'prefecture',
        'address1',
        'address2',
        'tel',
        'birthday',
        'membership_id',
        'gender',
    ];

    // SupporterProfile リレーションを定義
    public function supporterProfile()
    {
        return $this->hasOne(SupporterProfile::class, 'user_id', 'id');
    }
}
