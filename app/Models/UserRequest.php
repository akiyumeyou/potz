<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserRequest extends Model
{
    use HasFactory;

    protected $table = 'requests';

    protected $fillable = [
    'status_id',
    'supporter_id',
    'category3_id',
    'contents',
    'date',
    'time_start',
    'time',
    'spot',
    'address',
    'latitude',
    'longitude',
    'parking',
    'cost',
    'estimate',
    'requester_id',
    'distance',
    'confirmed_by',
    'is_liked',
    ];



    // ユーザーとのリレーション
    public function user()
    {
        return $this->belongsTo(User::class, 'requester_id');
    }
    public function getStatusNameAttribute()
    {
        return match ($this->status_id) {
            1 => '準備中',
            2 => '調整中',
            3 => '確定',
            4 => '完了',
            6 => 'キャンセル',
            9 => '削除',
            default => '不明',
        };
    }

    public function category3()
    {
    return $this->belongsTo(Category3::class, 'category3_id');
    }

   public function meetRoom()
   {
       return $this->hasOne(MeetRoom::class, 'request_id');
   }

//    public function requester()
//    {
//        return $this->belongsTo(User::class, 'requester_id');
//    }

   public function supporter()
   {
       return $this->belongsTo(User::class, 'supporter_id');
   }



}
