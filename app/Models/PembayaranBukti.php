<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PembayaranBukti extends Model
{
    protected $table = 'pembayaran_bukti';

    protected $fillable = [
        'id_daftar_poli',
        'id_pasien',
        'file_path',
        'status',
        'catatan',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_pasien');
    }

    public function daftarPoli()
    {
        return $this->belongsTo(DaftarPoli::class, 'id_daftar_poli');
    }
}
