<?php

namespace App\Notifications;

use App\Models\Stand;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class StandApproved extends Notification
{
    public function __construct(public Stand $stand) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Pengajuan stand Anda diterima')
            ->greeting('Halo, '.$notifiable->name.'!')
            ->line('Stand "'.$this->stand->name.'" sudah diterima admin dan tampil di katalog CD Jajan.')
            ->action('Masuk dan kelola stand', route('login'))
            ->line('Silakan lengkapi menu dan harga jajanan Anda.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Pengajuan diterima',
            'message' => 'Stand "'.$this->stand->name.'" sudah tampil di katalog. Silakan lengkapi menu Anda.',
        ];
    }
}
