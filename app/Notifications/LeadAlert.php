<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;

/**
 * An in-app alert shown under the bell: a title, a line of text and where tapping it goes.
 */
class LeadAlert extends Notification
{
    public function __construct(public string $title, public string $body, public string $url, public string $kind = 'info') {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return ['title' => $this->title, 'body' => $this->body, 'url' => $this->url, 'kind' => $this->kind];
    }
}
