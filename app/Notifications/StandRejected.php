<?php

namespace App\Notifications;

use App\Models\Stand;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class StandRejected extends Notification
{
    public function __construct(public Stand $stand) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Pengajuan stand belum dapat diterima')
            ->greeting('Halo, '.$notifiable->name.'!')
            ->line('Mohon maaf, pengajuan stand "'.$this->stand->name.'" belum dapat diterima admin.')
            ->line('Silakan hubungi pengelola CD untuk informasi lebih lanjut.');
    }
}
