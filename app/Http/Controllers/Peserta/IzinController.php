<?php

namespace App\Http\Controllers\Peserta;

use App\Http\Controllers\Controller;
use App\Http\Requests\AjukanIzinRequest;
use App\Models\HariLibur;
use App\Models\IzinPeserta;
use App\Models\LogbookHarian;
use App\Models\PengaturanMagang;
use App\Models\PesertaMagang;
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
 * Izin / sakit beberapa hari dengan satu bukti. Sistem mengisi status
 * izin/sakit ke setiap hari kerja dalam rentang tanggal.
 */
class IzinController extends Controller
{
    /**
     * Izin boleh diajukan untuk tanggal mendatang sampai sekian hari.
     */
    private const MAKS_HARI_KE_DEPAN = 30;

    public function __construct(private KalenderLogbook $kalender) {}

    public function create(Request $request): View
    {
        $tanggal = $request->query('tanggal');
        $tanggal = is_string($tanggal) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $tanggal) ? $tanggal : now()->toDateString();

        return view('peserta.izin.create', [
            'peserta' => $request->user()->pesertaMagang,
            'tanggalAwal' => $tanggal,
            'batasMundur' => PengaturanMagang::ambil()->batas_isi_mundur_hari,
        ]);
    }

    public function store(AjukanIzinRequest $request): RedirectResponse
    {
        $peserta = $request->user()->pesertaMagang;
        $mulai = CarbonImmutable::parse($request->input('tanggal_mulai'))->startOfDay();
        $selesai = CarbonImmutable::parse($request->input('tanggal_selesai'))->startOfDay();

        if ($pesan = $this->cekRentang($peserta, $mulai, $selesai)) {
            return back()->withInput()->withErrors(['tanggal_mulai' => $pesan]);
        }

        // Hari kerja saja: Sabtu/Minggu & hari libur tetap tercatat libur.
        $libur = HariLibur::whereDate('tanggal', '>=', $mulai->toDateString())
            ->whereDate('tanggal', '<=', $selesai->toDateString())
            ->get()
            ->map(fn (HariLibur $hari) => $hari->tanggal->toDateString())
            ->all();

        $sudahAda = LogbookHarian::where('peserta_magang_id', $peserta->id)
            ->whereDate('tanggal', '>=', $mulai->toDateString())
            ->whereDate('tanggal', '<=', $selesai->toDateString())
            ->get()
            ->keyBy(fn (LogbookHarian $logbook) => $logbook->tanggal->toDateString());

        $dilewati = [];
        $tanggalIzin = [];

        foreach (KalenderLogbook::tanggalAntara($mulai, $selesai) as $tanggal) {
            $hari = CarbonImmutable::parse($tanggal);

            if ($hari->isWeekend() || in_array($tanggal, $libur, true)) {
                continue;
            }

            // Hari yang sudah diisi hadir tidak ditimpa.
            if ($sudahAda->get($tanggal)?->kehadiran === LogbookHarian::HADIR) {
                $dilewati[] = $hari->translatedFormat('d M');

                continue;
            }

            $tanggalIzin[] = $tanggal;
        }

        if (! $tanggalIzin) {
            return back()->withInput()->withErrors(['tanggal_mulai' => 'Tidak ada hari kerja yang bisa diisi izin/sakit pada rentang tersebut.']);
        }

        $izin = DB::transaction(function () use ($request, $peserta, $mulai, $selesai, $tanggalIzin, $sudahAda) {
            $file = $request->file('bukti');

            $izin = IzinPeserta::create([
                'peserta_magang_id' => $peserta->id,
                'jenis' => $request->input('jenis'),
                'tanggal_mulai' => $mulai->toDateString(),
                'tanggal_selesai' => $selesai->toDateString(),
                'keterangan' => $request->input('keterangan'),
                'bukti_path' => $file?->store("izin/{$peserta->id}", 'local'),
                'bukti_nama' => $file ? mb_substr($file->getClientOriginalName(), 0, 255) : null,
                'bukti_mime' => $file?->getMimeType(),
                'bukti_ukuran' => $file?->getSize(),
            ]);

            foreach ($tanggalIzin as $tanggal) {
                // Isian libur pada tanggal itu diganti status izin/sakit.
                $sudahAda->get($tanggal)?->delete();

                LogbookHarian::create([
                    'peserta_magang_id' => $peserta->id,
                    'tanggal' => $tanggal,
                    'kehadiran' => $izin->jenis,
                    'activity' => $izin->keterangan,
                    'status_verifikasi' => LogbookHarian::MENUNGGU,
                    'izin_peserta_id' => $izin->id,
                ]);
            }

            return $izin;
        });

        $pesan = $izin->labelJenis().' untuk '.count($tanggalIzin).' hari kerja berhasil diajukan.';

        if ($dilewati) {
            $pesan .= ' Tanggal yang sudah diisi hadir tidak diubah: '.implode(', ', $dilewati).'.';
        }

        return redirect()
            ->route('peserta.logbook.index', ['periode' => $this->kalender->kunciPeriode($mulai)])
            ->with($dilewati ? 'warning' : 'success', $pesan);
    }

    /**
     * Batalkan izin yang belum diverifikasi. Hari-harinya ikut terhapus.
     */
    public function destroy(Request $request, IzinPeserta $izinPeserta): RedirectResponse
    {
        Gate::authorize('view', $izinPeserta);

        if ($izinPeserta->status !== LogbookHarian::MENUNGGU) {
            return back()->with('error', 'Izin yang sudah diverifikasi admin tidak dapat dibatalkan.');
        }

        if ($izinPeserta->bukti_path) {
            Storage::disk('local')->delete($izinPeserta->bukti_path);
        }

        $kunci = $this->kalender->kunciPeriode($izinPeserta->tanggal_mulai);
        $izinPeserta->delete();

        return redirect()
            ->route('peserta.logbook.index', ['periode' => $kunci])
            ->with('success', 'Pengajuan izin/sakit dibatalkan.');
    }

    public function bukti(IzinPeserta $izinPeserta): Response
    {
        Gate::authorize('view', $izinPeserta);

        abort_unless($izinPeserta->bukti_path && Storage::disk('local')->exists($izinPeserta->bukti_path), 404);

        return Storage::disk('local')->response($izinPeserta->bukti_path, $izinPeserta->bukti_nama);
    }

    /**
     * Pesan kesalahan bila rentang tanggal tidak boleh diajukan.
     */
    private function cekRentang(PesertaMagang $peserta, CarbonImmutable $mulai, CarbonImmutable $selesai): ?string
    {
        $hariIni = CarbonImmutable::today();

        if ($mulai->diffInDays($selesai) + 1 > AjukanIzinRequest::MAKS_HARI) {
            return 'Rentang izin/sakit maksimal '.AjukanIzinRequest::MAKS_HARI.' hari.';
        }

        if (! $this->kalender->dalamMasaMagang($peserta, $mulai) || ! $this->kalender->dalamMasaMagang($peserta, $selesai)) {
            return 'Rentang tanggal harus di dalam masa magang Anda.';
        }

        if ($selesai->gt($hariIni->addDays(self::MAKS_HARI_KE_DEPAN))) {
            return 'Izin hanya bisa diajukan paling jauh '.self::MAKS_HARI_KE_DEPAN.' hari ke depan.';
        }

        if ($mulai->lt($hariIni) && $this->kalender->akses($peserta, $mulai) === KalenderLogbook::LEWAT_BATAS) {
            return 'Tanggal mulai sudah melewati batas pengisian. Minta admin membuka kunci tanggal tersebut.';
        }

        $bentrok = IzinPeserta::where('peserta_magang_id', $peserta->id)
            ->where('status', '!=', LogbookHarian::DITOLAK)
            ->whereDate('tanggal_mulai', '<=', $selesai->toDateString())
            ->whereDate('tanggal_selesai', '>=', $mulai->toDateString())
            ->exists();

        return $bentrok ? 'Sudah ada pengajuan izin/sakit pada rentang tanggal tersebut.' : null;
    }
}
