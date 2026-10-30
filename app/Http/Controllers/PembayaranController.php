<?php

namespace App\Http\Controllers;

use App\Models\DaftarPoli;
use App\Models\PembayaranBukti;
use App\Notifications\PembayaranBuktiDiunggah;
use App\Notifications\PembayaranDitolak;
use App\Notifications\PembayaranDiverifikasi;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PembayaranController extends Controller
{
    /**
     * Display pembayaran page for pasien (upload bukti + tagihan).
     */
    public function index(Request $request): View
    {
        $user = $request->user();
        abort_unless($user !== null && $user->role === 'pasien', 403);

        $daftarPolis = DaftarPoli::query()
            ->where('id_pasien', $user->id)
            ->with(['jadwalPeriksa.dokter.poli', 'periksas'])
            ->latest('id')
            ->get();

        $pembayaranBuktis = PembayaranBukti::query()
            ->where('id_pasien', $user->id)
            ->latest()
            ->get();

        return view('pasien.pembayaran.index', compact('user', 'daftarPolis', 'pembayaranBuktis'));
    }

    /**
     * Handle bukti pembayaran upload from pasien.
     */
    public function store(Request $request)
    {
        $request->validate([
            'id_daftar_poli' => 'required|exists:daftar_poli,id',
            'bukti' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        DaftarPoli::query()
            ->where('id_pasien', $request->user()->id)
            ->findOrFail($request->id_daftar_poli);

        $file = $request->file('bukti');
        $fileName = 'bukti-'.time().'.'.$file->getClientOriginalExtension();
        $file->move(public_path('uploads/pembayaran'), $fileName);

        $filePath = 'uploads/pembayaran/'.$fileName;

        // Check if there's already a record for this daftar_poli
        $existing = PembayaranBukti::where('id_daftar_poli', $request->id_daftar_poli)->first();

        if ($existing) {
            $existing->update([
                'file_path' => $filePath,
                'status' => 'pending',
                'id_pasien' => auth()->user()->id,
                'catatan' => null,
            ]);
        } else {
            PembayaranBukti::create([
                'id_daftar_poli' => $request->id_daftar_poli,
                'id_pasien' => auth()->user()->id,
                'file_path' => $filePath,
                'status' => 'pending',
            ]);
        }

        $request->user()->notify(new PembayaranBuktiDiunggah);

        return back()->with('success', 'Bukti pembayaran berhasil diupload. Menunggu verifikasi admin.');
    }

    /**
     * Display daftar tagihan menunggu verifikasi for admin.
     */
    public function adminIndex()
    {
        $pendingPayments = PembayaranBukti::where('status', 'pending')
            ->with(['user', 'daftarPoli.jadwalPeriksa.dokter.poli'])
            ->latest('created_at')
            ->get();

        return view('admin.pembayaran.index', compact('pendingPayments'));
    }

    /**
     * Admin verifies/confirms a payment.
     */
    public function verify($id)
    {
        $payment = PembayaranBukti::findOrFail($id);
        $payment->update(['status' => 'lunas']);

        // Trigger notification to pasien that payment is verified
        $payment->user->notify(new PembayaranDiverifikasi);

        return back()->with('success', 'Pembayaran berhasil diverifikasi dan status menjadi lunas.');
    }

    /**
     * Admin rejects a payment.
     */
    public function reject($id)
    {
        $payment = PembayaranBukti::findOrFail($id);
        $payment->update(['status' => 'ditolak', 'catatan' => 'ditolak oleh admin']);

        // Trigger notification to pasien that payment is rejected
        $payment->user->notify(new PembayaranDitolak);

        return back()->with('success', 'Pembayaran ditolak dan notifikasi dikirim ke pasien.');
    }

    /**
     * Get unread count or AJAX data for pending payments.
     */
    public function ajaxPending()
    {
        $count = PembayaranBukti::where('status', 'pending')->count();
        $payments = PembayaranBukti::where('status', 'pending')
            ->with(['user', 'daftarPoli.jadwalPeriksa.dokter.poli'])
            ->latest('created_at')
            ->take(5)
            ->get();

        return response()->json([
            'count' => $count,
            'payments' => $payments,
        ]);
    }
}
