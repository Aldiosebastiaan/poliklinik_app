<?php

namespace Tests\Feature;

use App\Models\DaftarPoli;
use App\Models\JadwalPeriksa;
use App\Models\Periksa;
use App\Models\Poli;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PasienDashboardTest extends TestCase
{
    use RefreshDatabase;

    private function createSetup(): array
    {
        $poli = Poli::create([
            'nama_poli' => 'Poli Gigi',
            'keterangan' => 'Pemeriksaan gigi',
        ]);

        $dokter = User::create([
            'nama' => 'Dr. Dokter',
            'email' => 'dokter@test.com',
            'password' => bcrypt('password'),
            'role' => 'dokter',
            'id_poli' => $poli->id,
        ]);

        $jadwal = JadwalPeriksa::create([
            'id_dokter' => $dokter->id,
            'hari' => 'Senin',
            'jam_mulai' => '08:00',
            'jam_selesai' => '09:00',
        ]);

        $pasien = User::create([
            'nama' => 'Pasien Budi',
            'email' => 'pasien@test.com',
            'password' => bcrypt('password'),
            'role' => 'pasien',
            'no_rm' => '202610-001',
        ]);

        return compact('poli', 'dokter', 'jadwal', 'pasien');
    }

    public function test_dashboard_displays_empty_state_when_no_active_queue(): void
    {
        $setup = $this->createSetup();

        $response = $this->actingAs($setup['pasien'])->get(route('pasien.dashboard'));
        $response->assertStatus(200);
        $response->assertSee('Tidak Ada Antrian Aktif');
        $response->assertSee('Daftar Jadwal Poliklinik');
        $response->assertSee('Poli Gigi');
    }

    public function test_dashboard_displays_active_queue_banner(): void
    {
        $setup = $this->createSetup();

        // Buat antrian
        $daftarPoli = DaftarPoli::create([
            'id_pasien' => $setup['pasien']->id,
            'id_jadwal' => $setup['jadwal']->id,
            'keluhan' => 'Sakit gigi berlubang',
            'no_antrian' => 5,
        ]);

        $response = $this->actingAs($setup['pasien'])->get(route('pasien.dashboard'));
        $response->assertStatus(200);
        $response->assertSee('ANTRIAN AKTIF ANDA');
        $response->assertSee('Poli Gigi');
        $response->assertSee('Dr. Dokter');
        $response->assertSee('Senin (08:00 - 09:00)');
        $response->assertSee('Nomor Anda');
        $response->assertSee('5');
        $response->assertSee('SEDANG DILAYANI');
    }

    public function test_api_antrian_status_returns_live_json(): void
    {
        $setup = $this->createSetup();

        DaftarPoli::create([
            'id_pasien' => $setup['pasien']->id,
            'id_jadwal' => $setup['jadwal']->id,
            'keluhan' => 'Sakit gigi',
            'no_antrian' => 3,
        ]);

        $response = $this->actingAs($setup['pasien'])->get(route('pasien.antrian.status'));
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'antrian_aktif' => ['id', 'no_antrian', 'sedang_dilayani'],
            'jadwal_status',
        ]);
        $response->assertJson([
            'antrian_aktif' => [
                'no_antrian' => 3,
                'sedang_dilayani' => 3,
            ],
        ]);
    }

    public function test_pasien_cannot_register_two_polis_simultaneously(): void
    {
        $setup = $this->createSetup();

        // Pendaftaran pertama (antrian aktif)
        DaftarPoli::create([
            'id_pasien' => $setup['pasien']->id,
            'id_jadwal' => $setup['jadwal']->id,
            'keluhan' => 'Pusing',
            'no_antrian' => 1,
        ]);

        // Coba mendaftar lagi
        $response = $this->actingAs($setup['pasien'])->post(route('pasien.daftar.submit'), [
            'id_pasien' => $setup['pasien']->id,
            'id_poli' => $setup['poli']->id,
            'id_jadwal' => $setup['jadwal']->id,
            'keluhan' => 'Mata minus',
        ]);

        // Harus gagal dan diredirect back dengan error
        $response->assertSessionHas('error');
        $this->assertDatabaseCount('daftar_poli', 1);
    }

    public function test_pasien_can_register_again_after_examination_finished(): void
    {
        $setup = $this->createSetup();

        // Pendaftaran pertama
        $daftarPoli = DaftarPoli::create([
            'id_pasien' => $setup['pasien']->id,
            'id_jadwal' => $setup['jadwal']->id,
            'keluhan' => 'Sakit gigi',
            'no_antrian' => 1,
        ]);

        // Dokter menyelesaikan pemeriksaan
        Periksa::create([
            'id_daftar_poli' => $daftarPoli->id,
            'tgl_periksa' => now(),
            'catatan' => 'Gigi sudah dicabut',
            'biaya_periksa' => 150000,
        ]);

        // Pasien mendaftar lagi untuk jadwal lain/baru
        $response = $this->actingAs($setup['pasien'])->post(route('pasien.daftar.submit'), [
            'id_pasien' => $setup['pasien']->id,
            'id_poli' => $setup['poli']->id,
            'id_jadwal' => $setup['jadwal']->id,
            'keluhan' => 'Pemeriksaan lanjutan',
        ]);

        $response->assertRedirect(route('pasien.dashboard'));
        $this->assertDatabaseCount('daftar_poli', 2);
    }
}
