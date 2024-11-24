<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class Event extends Model
{
    protected $fillable = [
        'title',
        'event_date',
        'start_time',
        'end_time',
        'content',
        'zoom_url',
        'recurring',
        'holiday',
        'recurring_type',
        'user_id',
        'image_path', // イメージ画像のパスを追加
    ];

    // ユーザーとのリレーション
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // 開催中かどうかを判定するメソッド
    public function isOngoing()
    {
        $current = Carbon::now();
        $eventStart = Carbon::parse($this->event_date . ' ' . $this->start_time);
        $eventEnd = Carbon::parse($this->event_date . ' ' . $this->end_time);

        return $current->between($eventStart, $eventEnd);
    }

    // 開催前かどうかを判定するメソッド
    public function isUpcoming()
    {
        $current = Carbon::now();
        $eventStart = Carbon::parse($this->event_date . ' ' . $this->start_time);

        return $current->lt($eventStart);
    }

    // 次回の日程を取得
    public function getNextEventDate()
    {
        if (!$this->recurring || $this->holiday) {
            return null;
        }

        $eventDate = Carbon::parse($this->event_date);

        switch ($this->recurring_type) {
            case 'weekly':
                $nextEventDate = $eventDate->addWeek();
                break;
            case 'biweekly':
                $nextEventDate = $eventDate->addWeeks(2);
                break;
            case 'monthly':
                $nextEventDate = $eventDate->addMonth();
                break;
            default:
                return null; // 正しくない場合はnullを返す
        }

        // 次回の開催日時が現在の日時よりも過去であれば、さらに繰り返し
        while ($nextEventDate->lt(Carbon::now())) {
            switch ($this->recurring_type) {
                case 'weekly':
                    $nextEventDate->addWeek();
                    break;
                case 'biweekly':
                    $nextEventDate->addWeeks(2);
                    break;
                case 'monthly':
                    $nextEventDate->addMonth();
                    break;
            }
        }

        return $nextEventDate->toDateString();
    }

    // 表示用の開催日を取得
    public function getDisplayEventDate()
    {
        if ($this->isOngoing() || $this->isUpcoming()) {
            return $this->event_date;
        }

        // 次回の日程が設定されていれば、その日程をセット
        $nextEventDate = $this->getNextEventDate();
        if ($nextEventDate) {
            $this->event_date = $nextEventDate;
            $this->save(); // 日付を保存
        }

        return $this->event_date;
    }

    // イメージURLを取得するメソッド
    public function getImageUrl()
    {
        if ($this->image_path) {
            return asset('storage/' . $this->image_path);
        }

        return null;
    }
}
