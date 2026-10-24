<?php

namespace App\Http\Controllers\Peserta;

use App\Http\Controllers\Controller;
use App\Http\Requests\SimpanLogbookHarianRequest;
use App\Models\BiodataPeserta;
use App\Models\IzinPeserta;
use App\Models\LogbookBukti;
use App\Models\LogbookHarian;
use App\Models\PesertaMagang;
use App\Services\CetakLogbook;
use App\Services\KalenderLogbook;
use Carbon\CarbonImmutable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;

/**
 * Logbook harian milik peserta yang sedang login.
 */
class LogbookController extends Controller
{
    public function __construct(private KalenderLogbook $kalender) {}

    /**
     * Tabel satu periode (28–27) seperti sheet bulanan, beserta rekap.
     */
    public function index(Request $request): View
    {
        $peserta = $this->peserta($request);
        $periode = $this->kalender->pilihPeriode($peserta, $request->query('periode'));
        $hari = $periode ? $this->kalender->hari($peserta, $periode) : [];

        return view('peserta.logbook.index', [
            'peserta' => $peserta,
            'daftarPeriode' => $this->kalender->daftarPeriode($peserta),
            'periode' => $periode,
            'hari' => $hari,
            'rekap' => $this->kalender->rekap($hari),
            'tarif' => $periode ? $this->kalender->tarifPeriode($periode) : null,
            'izins' => $periode ? $this->izinDalam($peserta, $periode) : collect(),
            'biodata' => BiodataPeserta::untuk($peserta),
        ]);
    }

    public function edit(Request $request, string $tanggal): View|RedirectResponse
    {
        $peserta = $this->peserta($request);
        $hari = $this->tanggal($tanggal);

        if ($tolak = $this->tolakAkses($peserta, $hari)) {
            return $tolak;
        }

        $logbook = $this->logbook($peserta, $hari);

        if ($logbook && ! $logbook->bisaDiubahPeserta()) {
            return $this->keIndex($hari)->with('error', 'Logbook tanggal ini sudah diterima atau bagian dari izin/sakit, sehingga tidak dapat diubah.');
        }

        return view('peserta.logbook.form', [
            'peserta' => $peserta,
            'tanggal' => $hari,
            'logbook' => $logbook ?? new LogbookHarian([
                'kehadiran' => $hari->isWeekend() ? LogbookHarian::LIBUR : LogbookHarian::HADIR,
                'lokasi' => $this->lokasiTerakhir($peserta),
            ]),
            'saranLokasi' => $this->saranLokasi($peserta),
        ]);
    }

    public function update(SimpanLogbookHarianRequest $request, string $tanggal): RedirectResponse
    {
        $peserta = $this->peserta($request);
        $hari = $this->tanggal($tanggal);

        if ($tolak = $this->tolakAkses($peserta, $hari)) {
            return $tolak;
        }

        $logbook = $this->logbook($peserta, $hari) ?? new LogbookHarian([
            'peserta_magang_id' => $peserta->id,
            'tanggal' => $hari->toDateString(),
        ]);

        if ($logbook->exists && ! $logbook->bisaDiubahPeserta()) {
            return $this->keIndex($hari)->with('error', 'Logbook tanggal ini sudah diterima atau bagian dari izin/sakit, sehingga tidak dapat diubah.');
        }

        $hapus = $logbook->exists
            ? $logbook->buktis()->whereIn('id', $request->input('hapus_bukti', []))->get()
            : collect();
        $jumlahBukti = ($logbook->exists ? $logbook->buktis()->count() : 0) - $hapus->count() + count($request->file('bukti', []));

        if ($jumlahBukti > SimpanLogbookHarianRequest::MAKS_BUKTI) {
            return back()->withInput()->withErrors(['bukti' => 'Maksimal '.SimpanLogbookHarianRequest::MAKS_BUKTI.' file bukti per hari. Hapus bukti lama lebih dulu.']);
        }

        $hadir = $request->input('kehadiran') === LogbookHarian::HADIR;
        $data = $request->safe()->only(['kehadiran', 'activity', 'output', 'lokasi']);

        foreach (LogbookHarian::KOLOM_JAM as $kolom) {
            $data[$kolom] = $hadir ? $request->input($kolom) : null;
        }

        DB::transaction(function () use ($logbook, $data, $hapus, $request, $peserta) {
            // Setiap perubahan kembali menunggu verifikasi admin.
            $logbook->fill($data + ['status_verifikasi' => LogbookHarian::MENUNGGU])->save();

            foreach ($hapus as $bukti) {
                Storage::disk('local')->delete($bukti->path);
                $bukti->delete();
            }

            foreach ($request->file('bukti', []) as $file) {
                $logbook->buktis()->create([
                    'path' => $file->store("logbook/{$peserta->id}", 'local'),
                    'nama_asli' => mb_substr($file->getClientOriginalName(), 0, 255),
                    'mime' => $file->getMimeType() ?? 'application/octet-stream',
                    'ukuran' => $file->getSize(),
                ]);
            }
        });

        return $this->keIndex($hari)->with('success', 'Logbook '.$hari->translatedFormat('l, d F Y').' berhasil disimpan.');
    }

    public function destroy(Request $request, string $tanggal): RedirectResponse
    {
        $peserta = $this->peserta($request);
        $hari = $this->tanggal($tanggal);

        if ($tolak = $this->tolakAkses($peserta, $hari)) {
            return $tolak;
        }

        $logbook = $this->logbook($peserta, $hari);

        if (! $logbook || ! $logbook->bisaDiubahPeserta()) {
            return $this->keIndex($hari)->with('error', 'Logbook tanggal ini tidak dapat dihapus.');
        }

        foreach ($logbook->buktis as $bukti) {
            Storage::disk('local')->delete($bukti->path);
        }

        $logbook->delete();

        return $this->keIndex($hari)->with('success', 'Isian logbook '.$hari->translatedFormat('d F Y').' dihapus.');
    }

    public function bukti(LogbookBukti $logbookBukti): Response
    {
        Gate::authorize('view', $logbookBukti->logbookHarian);

        abort_unless(Storage::disk('local')->exists($logbookBukti->path), 404);

        return Storage::disk('local')->response($logbookBukti->path, $logbookBukti->nama_asli);
    }

    /**
     * Rekap periode dalam format sheet bulanan (PDF).
     */
    public function cetak(Request $request, CetakLogbook $cetak): Response
    {
        $peserta = $this->peserta($request);
        $periode = $this->kalender->pilihPeriode($peserta, $request->query('periode'));

        abort_unless($periode, 404);

        return $cetak->pdf($peserta, $periode);
    }

    private function peserta(Request $request): PesertaMagang
    {
        return $request->user()->pesertaMagang;
    }

    private function tanggal(string $tanggal): CarbonImmutable
    {
        abort_unless(preg_match('/^\d{4}-\d{2}-\d{2}$/', $tanggal) && strtotime($tanggal), 404);

        return CarbonImmutable::createFromFormat('!Y-m-d', $tanggal);
    }

    /**
     * Redirect dengan pesan bila tanggal tidak boleh diisi peserta.
     */
    private function tolakAkses(PesertaMagang $peserta, CarbonImmutable $hari): ?RedirectResponse
    {
        $pesan = match ($this->kalender->akses($peserta, $hari)) {
            KalenderLogbook::BISA => null,
            KalenderLogbook::BELUM_TIBA => 'Tanggal tersebut belum tiba.',
            KalenderLogbook::DI_LUAR => 'Tanggal tersebut di luar masa magang Anda.',
            default => 'Tanggal tersebut sudah melewati batas pengisian. Minta admin membuka kunci tanggal ini.',
        };

        return $pesan ? $this->keIndex($hari)->with('error', $pesan) : null;
    }

    private function keIndex(CarbonImmutable $hari): RedirectResponse
    {
        return redirect()->to(route('peserta.logbook.index', ['periode' => $this->kalender->kunciPeriode($hari)]).'#hari-'.$hari->toDateString());
    }

    private function logbook(PesertaMagang $peserta, CarbonImmutable $hari): ?LogbookHarian
    {
        return LogbookHarian::with('buktis')
            ->where('peserta_magang_id', $peserta->id)
            ->whereDate('tanggal', $hari->toDateString())
            ->first();
    }

    private function lokasiTerakhir(PesertaMagang $peserta): ?string
    {
        return LogbookHarian::where('peserta_magang_id', $peserta->id)
            ->whereNotNull('lokasi')
            ->orderByDesc('tanggal')
            ->value('lokasi');
    }

    /**
     * @return array<int, string>
     */
    private function saranLokasi(PesertaMagang $peserta): array
    {
        return LogbookHarian::where('peserta_magang_id', $peserta->id)
            ->whereNotNull('lokasi')
            ->distinct()
            ->orderBy('lokasi')
            ->limit(20)
            ->pluck('lokasi')
            ->all();
    }

    /**
     * Izin/sakit yang beririsan dengan periode.
     *
     * @param  array{mulai: CarbonImmutable, selesai: CarbonImmutable}  $periode
     */
    private function izinDalam(PesertaMagang $peserta, array $periode)
    {
        return IzinPeserta::where('peserta_magang_id', $peserta->id)
            ->whereDate('tanggal_mulai', '<=', $periode['selesai']->toDateString())
            ->whereDate('tanggal_selesai', '>=', $periode['mulai']->toDateString())
            ->orderBy('tanggal_mulai')
            ->get();
    }
}
