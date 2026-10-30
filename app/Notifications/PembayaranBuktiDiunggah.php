<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;

class PembayaranBuktiDiunggah extends Notification
{
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'message' => 'Bukti pembayaran Anda berhasil diunggah dan sedang menunggu verifikasi admin.',
            'type' => 'pembayaran_bukti_diunggah',
        ];
    }

    public function toDatabase(object $notifiable): array
    {
        return $this->toArray($notifiable);
    }
}
