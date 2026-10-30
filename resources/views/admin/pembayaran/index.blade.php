<x-layouts.app title="Daftar Tagihan Menunggu Verifikasi">
    <div class="bg-base-100 shadow-xl rounded-2xl p-6 mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-bold">Daftar Tagihan Menunggu Verifikasi</h2>
                <p class="text-base-content/60">Tolong verifikasi pembayaran dari pasien</p>
            </div>
            <div class="avatar">
                <div class="w-10 h-10 rounded-full bg-primary text-primary-content flex items-center justify-center">
                    <span class="text-sm font-semibold leading-none">
                        {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success mb-4">{{ session('success') }}</div>
    @endif

    @if($pendingPayments->isNotEmpty())
        <div class="bg-base-100 rounded-2xl overflow-x-auto shadow-sm">
            <table class="min-w-full">
                <thead>
                    <tr class="border-b border-base-300">
                        <th class="p-4 text-left text-sm font-semibold">No</th>
                        <th class="p-4 text-left text-sm font-semibold">Pasien</th>
                        <th class="p-4 text-left text-sm font-semibold">Pemeriksaan</th>
                        <th class="p-4 text-left text-sm font-semibold">Biaya</th>
                        <th class="p-4 text-left text-sm font-semibold">Bukti</th>
                        <th class="p-4 text-left text-sm font-semibold">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($pendingPayments as $index => $payment)
                        @php
                            $patientName = $payment->user->nama ?? 'Pasien';
                            $amount = number_format($payment->daftarPoli->periksas->last()?->biaya_periksa ?? 0, 0, ',', '.');
                        @endphp
                        <tr class="border-b border-base-200 hover:bg-base-100/50 transition-colors">
                            <td class="p-4 font-medium">{{ $index + 1 }}</td>
                            <td class="p-4">
                                <p class="font-medium">{{ $patientName }}</p>
                                <p class="text-xs text-base-content/60">{{ $payment->user->email }}</p>
                            </td>
                            <td class="p-4">
                                <p class="font-medium">{{ $payment->daftarPoli->jadwalPeriksa->dokter->poli->nama_poli ?? 'Poli' }}</p>
                                <p class="text-xs text-base-content/60">No Antrian: #{{ $payment->daftarPoli->no_antrian }}</p>
                            </td>
                            <td class="p-4"><p class="font-medium">Rp {{ $amount }}</p></td>
                            <td class="p-4">
                                <a href="{{ asset($payment->file_path) }}" target="_blank" rel="noopener noreferrer"
                                   class="inline-flex flex-col gap-1 text-xs font-semibold text-primary hover:underline">
                                    <img src="{{ asset($payment->file_path) }}"
                                         alt="Bukti pembayaran dari {{ $patientName }}"
                                         class="h-24 w-36 rounded-lg border border-base-300 object-cover">
                                    Lihat gambar ukuran penuh
                                </a>
                            </td>
                            <td class="p-4">
                                <div class="flex flex-wrap gap-2">
                                    <button type="button" class="btn btn-sm btn-success"
                                        data-verify-payment data-payment-id="{{ $payment->id }}"
                                        data-patient-name="{{ $patientName }}" data-amount="{{ $amount }}">
                                        <i class="fas fa-check me-1"></i>Konfirmasi
                                    </button>
                                    <button type="button" class="btn btn-sm btn-error inline-flex items-center gap-2 whitespace-nowrap"
                                        data-reject-payment data-payment-id="{{ $payment->id }}"
                                        data-patient-name="{{ $patientName }}" data-amount="{{ $amount }}">
                                        <i class="fas fa-times" aria-hidden="true"></i><span>Tolak</span>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="bg-base-100 rounded-2xl p-6 text-center text-base-content/60">
            <i class="fas fa-check-circle text-4xl mb-3"></i>
            <h3 class="font-semibold">Semua Tagihan Terverifikasi</h3>
            <p>Tidak ada pembayaran yang menunggu verifikasi.</p>
        </div>
    @endif

    <dialog id="verify-payment-modal" class="modal">
        <div class="modal-box max-w-md relative">
            <button type="button" class="btn btn-sm btn-circle btn-ghost absolute right-3 top-3"
                aria-label="Tutup" data-close-verification>&times;</button>
            <div class="flex items-start gap-4 pr-6">
                <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-success/10 text-success text-xl">
                    <i class="fas fa-check"></i>
                </div>
                <div>
                    <h3 class="text-lg font-bold">Verifikasi pembayaran?</h3>
                    <p class="mt-2 text-sm text-base-content/70">
                        Verifikasi pembayaran dari <strong id="verify-payment-patient"></strong> sebesar
                        <strong>Rp <span id="verify-payment-amount"></span></strong>? Statusnya akan diubah menjadi lunas.
                    </p>
                </div>
            </div>
            <div class="modal-action">
                <button type="button" class="btn btn-ghost" data-close-verification>Batalkan</button>
                <form id="verify-payment-form" method="POST" action="">
                    @csrf
                    <button type="submit" class="btn btn-success"><i class="fas fa-check"></i>Ya, verifikasi</button>
                </form>
            </div>
        </div>
        <form method="dialog" class="modal-backdrop"><button aria-label="Tutup dialog">Tutup</button></form>
    </dialog>

<dialog id="reject-payment-modal" class="modal">
        <div class="modal-box max-w-md relative">
            <!-- Header section with title and close button -->
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-bold">Tolak pembayaran?</h3>
                <button type="button" class="btn btn-sm btn-circle btn-ghost"
                    aria-label="Tutup" data-close-rejection>
                    <i class="fas fa-times text-2xl text-slate-400 hover:text-slate-600 transition"></i>
                </button>
            </div>
            <div class="prose max-w-none">
                <p class="text-sm text-base-content/70">
                    Tolak pembayaran dari <strong id="reject-patient-name"></strong> sebesar
                    <strong>Rp <span id="reject-payment-amount"></span></strong>?
                    Pasien dapat mengulang pembayaran kembali.
                </p>
            </div>
            <div class="modal-action">
                <button type="button" class="btn btn-ghost" data-close-rejection>Batalkan</button>
                <form id="reject-payment-form" method="POST" action="">
                    @csrf
                    <button type="submit" class="btn btn-error"><i class="fas fa-times"></i>Ya, tolak</button>
                </form>
            </div>
        </div>
        <form method="dialog" class="modal-backdrop"><button aria-label="Tutup dialog">Tutup</button></form>
    </dialog>

    <script>
        (() => {
            const verifyModal = document.getElementById('verify-payment-modal');
            const verifyForm = document.getElementById('verify-payment-form');
            const verifyUrlTemplate = @json(route('admin.pembayaran.verify', ['id' => '__PAYMENT_ID__']));
            const rejectModal = document.getElementById('reject-payment-modal');
            const rejectForm = document.getElementById('reject-payment-form');
            const rejectUrlTemplate = @json(route('admin.pembayaran.reject', ['id' => '__PAYMENT_ID__']));

            document.querySelectorAll('[data-verify-payment]').forEach((button) => {
                button.addEventListener('click', () => {
                    document.getElementById('verify-payment-patient').textContent = button.dataset.patientName;
                    document.getElementById('verify-payment-amount').textContent = button.dataset.amount;
                    verifyForm.action = verifyUrlTemplate.replace('__PAYMENT_ID__', button.dataset.paymentId);
                    verifyModal.showModal();
                });
            });

            document.querySelectorAll('[data-reject-payment]').forEach((button) => {
                button.addEventListener('click', () => {
                    document.getElementById('reject-patient-name').textContent = button.dataset.patientName;
                    document.getElementById('reject-payment-amount').textContent = button.dataset.amount;
                    rejectForm.action = rejectUrlTemplate.replace('__PAYMENT_ID__', button.dataset.paymentId);
                    rejectModal.showModal();
                });
            });

            document.querySelectorAll('[data-close-verification]').forEach((button) => {
                button.addEventListener('click', () => verifyModal.close());
            });
            document.querySelectorAll('[data-close-rejection]').forEach((button) => {
                button.addEventListener('click', () => rejectModal.close());
            });
        })();
    </script>
</x-layouts.app>
