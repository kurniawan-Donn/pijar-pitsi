<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\BiodataPeserta;
use App\Models\BukaKunciLogbook;
use App\Models\IzinPeserta;
use App\Models\LogAktivitas;
use App\Models\LogbookBukti;
use App\Models\LogbookHarian;
use App\Models\PengaturanMagang;
use App\Models\PesertaMagang;
use App\Services\CetakLogbook;
use App\Services\KalenderLogbook;
use Carbon\CarbonImmutable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;

/**
 * Logbook harian peserta (database): verifikasi per hari & izin/sakit,
 * buka kunci tanggal, dan cetak rekap periode.
 */
class LogbookHarianController extends Controller
{
    public function __construct(private KalenderLogbook $kalender) {}

    public function index(Request $request, PesertaMagang $pesertaMagang): View
    {
        $pesertaMagang->load(['biodata', 'penempatans.divisi', 'pengajuanMagang.mahasiswa', 'user']);
        $periode = $this->kalender->pilihPeriode($pesertaMagang, $request->query('periode'));
        $hari = $periode ? $this->kalender->hari($pesertaMagang, $periode) : [];

        return view('superadmin.peserta-magang.logbook-harian', [
            'pesertaMagang' => $pesertaMagang,
            'daftarPeriode' => $this->kalender->daftarPeriode($pesertaMagang),
            'periode' => $periode,
            'hari' => $hari,
            'rekap' => $this->kalender->rekap($hari),
            'tarif' => $periode ? $this->kalender->tarifPeriode($periode) : null,
            'biodata' => BiodataPeserta::untuk($pesertaMagang),
            'penempatan' => $periode ? $pesertaMagang->penempatanUntuk($periode['kunci']) : null,
            'jumlahImpor' => LogbookHarian::where('peserta_magang_id', $pesertaMagang->id)->where('sumber', 'impor')->count(),
            'izins' => $periode
                ? IzinPeserta::where('peserta_magang_id', $pesertaMagang->id)
                    ->whereDate('tanggal_mulai', '<=', $periode['selesai']->toDateString())
                    ->whereDate('tanggal_selesai', '>=', $periode['mulai']->toDateString())
                    ->orderBy('tanggal_mulai')
                    ->get()
                : collect(),
        ]);
    }

    /**
     * Ubah status beberapa hari sekaligus (Diterima / Ditolak / Review).
     * Hari izin/sakit diverifikasi lewat izinnya.
     */
    public function verifikasi(Request $request, PesertaMagang $pesertaMagang): RedirectResponse
    {
        $validated = $request->validate([
            'logbook' => ['required', 'array', 'min:1'],
            'logbook.*' => ['integer'],
            'status' => ['required', Rule::in([LogbookHarian::DITERIMA, LogbookHarian::DITOLAK, LogbookHarian::REVIEW])],
            'catatan_verifikasi' => ['nullable', 'required_if:status,ditolak', 'string', 'max:1000'],
            'periode' => ['nullable', 'date_format:Y-m'],
        ], [
            'logbook.required' => 'Pilih minimal satu hari yang akan diverifikasi.',
            'catatan_verifikasi.required_if' => 'Tulis alasan bila logbook ditolak.',
        ]);

        $jumlah = LogbookHarian::where('peserta_magang_id', $pesertaMagang->id)
            ->whereIn('id', $validated['logbook'])
            ->whereNull('izin_peserta_id')
            ->update([
                'status_verifikasi' => $validated['status'],
                'catatan_verifikasi' => $validated['catatan_verifikasi'] ?? null,
                'diverifikasi_oleh' => $request->user()->id,
                'diverifikasi_pada' => now(),
            ]);

        LogAktivitas::catat('verifikasi_logbook_harian', $pesertaMagang, "{$jumlah} hari logbook {$pesertaMagang->nama} ditandai ".LogbookHarian::STATUS_VERIFIKASI[$validated['status']], [
            'status' => $validated['status'],
            'jumlah' => $jumlah,
        ]);

        return $this->kembali($pesertaMagang, $validated['periode'] ?? null)
            ->with('success', "{$jumlah} hari berhasil ditandai ".LogbookHarian::STATUS_VERIFIKASI[$validated['status']].'.');
    }

    /**
     * Terima / tolak izin; status diteruskan ke semua hari dalam izin.
     */
    public function verifikasiIzin(Request $request, PesertaMagang $pesertaMagang, IzinPeserta $izinPeserta): RedirectResponse
    {
        abort_unless($izinPeserta->peserta_magang_id === $pesertaMagang->id, 404);

        $validated = $request->validate([
            'status' => ['required', Rule::in([LogbookHarian::DITERIMA, LogbookHarian::DITOLAK])],
            'catatan_verifikasi' => ['nullable', 'required_if:status,ditolak', 'string', 'max:1000'],
        ], [
            'catatan_verifikasi.required_if' => 'Tulis alasan bila izin/sakit ditolak.',
        ]);

        $data = [
            'catatan_verifikasi' => $validated['catatan_verifikasi'] ?? null,
            'diverifikasi_oleh' => $request->user()->id,
            'diverifikasi_pada' => now(),
        ];

        DB::transaction(function () use ($izinPeserta, $validated, $data) {
            $izinPeserta->update($data + ['status' => $validated['status']]);
            $izinPeserta->logbooks()->update($data + ['status_verifikasi' => $validated['status']]);
        });

        LogAktivitas::catat('verifikasi_izin', $pesertaMagang, "{$izinPeserta->labelJenis()} {$pesertaMagang->nama} ({$izinPeserta->rentang()}) ".LogbookHarian::STATUS_VERIFIKASI[$validated['status']], [
            'status' => $validated['status'],
        ]);

        return $this->kembali($pesertaMagang, $this->kalender->kunciPeriode($izinPeserta->tanggal_mulai))
            ->with('success', $izinPeserta->labelJenis().' '.$izinPeserta->rentang().' ditandai '.LogbookHarian::STATUS_VERIFIKASI[$validated['status']].'.');
    }

    /**
     * Izinkan peserta mengisi tanggal yang sudah melewati batas isi mundur.
     */
    public function bukaKunci(Request $request, PesertaMagang $pesertaMagang): RedirectResponse
    {
        $validated = $request->validate(['tanggal' => ['required', 'date_format:Y-m-d']]);
        $tanggal = CarbonImmutable::createFromFormat('!Y-m-d', $validated['tanggal']);

        if (! $this->kalender->dalamMasaMagang($pesertaMagang, $tanggal) || $tanggal->isFuture()) {
            return $this->kembali($pesertaMagang, $this->kalender->kunciPeriode($tanggal))
                ->with('error', 'Tanggal tidak bisa dibuka.');
        }

        // Berlaku selama batas isi mundur (minimal 1 hari) sejak dibuka.
        $berlakuSampai = now()->addDays(max(1, PengaturanMagang::ambil()->batas_isi_mundur_hari))->toDateString();

        $buka = BukaKunciLogbook::where('peserta_magang_id', $pesertaMagang->id)
            ->whereDate('tanggal', $tanggal->toDateString())
            ->first() ?? new BukaKunciLogbook(['peserta_magang_id' => $pesertaMagang->id, 'tanggal' => $tanggal->toDateString()]);

        $buka->fill(['berlaku_sampai' => $berlakuSampai, 'dibuat_oleh' => $request->user()->id])->save();

        LogAktivitas::catat('buka_kunci_logbook', $pesertaMagang, 'Logbook '.$tanggal->translatedFormat('d F Y')." {$pesertaMagang->nama} dibuka sampai ".CarbonImmutable::parse($berlakuSampai)->translatedFormat('d F Y'));

        return $this->kembali($pesertaMagang, $this->kalender->kunciPeriode($tanggal))
            ->with('success', 'Tanggal '.$tanggal->translatedFormat('d F Y').' dibuka untuk peserta sampai '.CarbonImmutable::parse($berlakuSampai)->translatedFormat('d F Y').'.');
    }

    public function cetak(Request $request, PesertaMagang $pesertaMagang, CetakLogbook $cetak): Response
    {
        $periode = $this->kalender->pilihPeriode($pesertaMagang, $request->query('periode'));

        abort_unless($periode, 404);

        return $cetak->pdf($pesertaMagang, $periode);
    }

    public function bukti(LogbookBukti $logbookBukti): Response
    {
        Gate::authorize('view', $logbookBukti->logbookHarian);

        abort_unless(Storage::disk('local')->exists($logbookBukti->path), 404);

        return Storage::disk('local')->response($logbookBukti->path, $logbookBukti->nama_asli);
    }

    public function buktiIzin(IzinPeserta $izinPeserta): Response
    {
        Gate::authorize('view', $izinPeserta);

        abort_unless($izinPeserta->bukti_path && Storage::disk('local')->exists($izinPeserta->bukti_path), 404);

        return Storage::disk('local')->response($izinPeserta->bukti_path, $izinPeserta->bukti_nama);
    }

    private function kembali(PesertaMagang $pesertaMagang, ?string $periode): RedirectResponse
    {
        return redirect()->route('superadmin.peserta-magang.logbook-harian', array_filter([
            'pesertaMagang' => $pesertaMagang,
            'periode' => $periode,
        ]));
    }
}
