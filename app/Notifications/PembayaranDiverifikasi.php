<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;

class PembayaranDiverifikasi extends Notification
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
            'message' => 'Pembayaran Anda berhasil diverifikasi oleh admin dan dinyatakan lunas.',
            'type' => 'pembayaran_diverifikasi',
        ];
    }

    public function toArray($notifiable)
    {
        return [
            'message' => 'Pembayaran Anda berhasil diverifikasi oleh admin dan dinyatakan lunas.',
            'type' => 'pembayaran_diverifikasi',
        ];
    }
}
