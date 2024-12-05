<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Matching extends Model
{
    use HasFactory;

    // テーブル名を指定
    protected $table = 'matchings';

    // 書き込み可能な属性を指定
    protected $fillable = [
        'requester_id',
        'supporter_id',
        'meetroom_id',
        'status',
        'cost',
        'time',
        'transportation_costs',
        'sonotacost1',
        'sonotacost2',
        'sonotacost3',
        'costkei',
        'remarks',
        'syousyu_flg',
        'matched_at',
        'closed_at',
    ];

    // リレーション設定
    public function requester()
    {
        return $this->belongsTo(User::class, 'requester_id');
    }

    public function supporter()
    {
        return $this->belongsTo(User::class, 'supporter_id');
    }

    public function meetRoom()
    {
        return $this->belongsTo(MeetRoom::class, 'meetroom_id');
    }
}
