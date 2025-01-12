<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SupportNotification extends Notification
{
    private $supporterName;
    private $url;

    public function __construct($supporterName, $url)
    {
        $this->supporterName = $supporterName;
        $this->url = $url;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'message' => "{$this->supporterName} さんがサポートを承認しました。",
            'url' => $this->url,
        ];
    }
}
