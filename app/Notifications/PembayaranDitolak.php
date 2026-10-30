<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;

class PembayaranDitolak extends Notification
{
    public function __construct()
    {
        //
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'message' => 'Bukti pembayaran Anda ditolak oleh admin. Silakan unggah ulang bukti pembayaran.',
            'type' => 'pembayaran_ditolak',
        ];
    }

    public function toArray($notifiable)
    {
        return [
            'message' => 'Bukti pembayaran Anda ditolak oleh admin. Silakan unggah ulang bukti pembayaran.',
            'type' => 'pembayaran_ditolak',
        ];
    }
}
