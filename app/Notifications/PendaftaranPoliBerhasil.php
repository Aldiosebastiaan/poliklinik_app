<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;

class PendaftaranPoliBerhasil extends Notification
{
    public function __construct(public int $nomorAntrian) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'message' => "Pendaftaran poli Anda berhasil. Nomor antrian Anda: #{$this->nomorAntrian}.",
            'type' => 'pendaftaran_poli_berhasil',
        ];
    }

    public function toDatabase(object $notifiable): array
    {
        return $this->toArray($notifiable);
    }
}
