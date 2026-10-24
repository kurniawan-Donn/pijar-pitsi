<?php

namespace App\Services;

use App\Models\BukaKunciLogbook;
use App\Models\HariLibur;
use App\Models\LogbookHarian;
use App\Models\PengaturanMagang;
use App\Models\PesertaMagang;
use App\Models\TarifUangSaku;
use Carbon\CarbonImmutable;
use Carbon\CarbonPeriod;
use DateTimeInterface;
use Illuminate\Support\Collection;

/**
 * Tabel logbook satu periode (seperti sheet bulanan) beserta aturan akses
 * tanggal dan rekap kehadiran & uang saku.
 *
 * Akses tanggal untuk peserta:
 * - bisa        : hari ini atau mundur sampai batas isi (Pengaturan Magang),
 *                 atau tanggal yang dibuka admin
 * - lewat_batas : tanggal lampau di luar batas
 * - belum_tiba  : tanggal setelah hari ini
 * - di_luar     : di luar masa magang peserta
 */
class KalenderLogbook
{
    public const BISA = 'bisa';

    public const LEWAT_BATAS = 'lewat_batas';

    public const BELUM_TIBA = 'belum_tiba';

    public const DI_LUAR = 'di_luar';

    public function __construct(private PeriodeMagang $periode) {}

    /**
     * @return array<int, array{kunci: string, label: string, mulai: CarbonImmutable, selesai: CarbonImmutable}>
     */
    public function daftarPeriode(PesertaMagang $peserta): array
    {
        return $this->periode->untukPeserta($peserta);
    }

    /**
     * Periode yang dipilih (bila valid), periode berjalan, atau periode
     * terdekat bila hari ini di luar masa magang.
     *
     * @return array{kunci: string, label: string, mulai: CarbonImmutable, selesai: CarbonImmutable}|null
     */
    public function pilihPeriode(PesertaMagang $peserta, ?string $kunci = null): ?array
    {
        $daftar = collect($this->daftarPeriode($peserta));

        if ($daftar->isEmpty()) {
            return null;
        }

        $kunciHariIni = $this->periode->kunciUntuk(CarbonImmutable::today());

        return $daftar->firstWhere('kunci', $kunci)
            ?? $daftar->firstWhere('kunci', $kunciHariIni)
            ?? ($kunciHariIni < $daftar->first()['kunci'] ? $daftar->first() : $daftar->last());
    }

    /**
     * Kunci periode untuk suatu tanggal.
     */
    public function kunciPeriode(DateTimeInterface $tanggal): string
    {
        return $this->periode->kunciUntuk($tanggal);
    }

    /**
     * Status akses satu tanggal untuk peserta.
     */
    public function akses(PesertaMagang $peserta, DateTimeInterface $tanggal): string
    {
        $tanggal = CarbonImmutable::instance($tanggal)->startOfDay();
        $hariIni = CarbonImmutable::today();

        if (! $this->dalamMasaMagang($peserta, $tanggal)) {
            return self::DI_LUAR;
        }

        if ($tanggal->gt($hariIni)) {
            return self::BELUM_TIBA;
        }

        $batas = PengaturanMagang::ambil()->batas_isi_mundur_hari;

        if ($tanggal->gte($hariIni->subDays($batas))) {
            return self::BISA;
        }

        $dibuka = BukaKunciLogbook::where('peserta_magang_id', $peserta->id)
            ->whereDate('tanggal', $tanggal->toDateString())
            ->whereDate('berlaku_sampai', '>=', $hariIni->toDateString())
            ->exists();

        return $dibuka ? self::BISA : self::LEWAT_BATAS;
    }

    public function dalamMasaMagang(PesertaMagang $peserta, DateTimeInterface $tanggal): bool
    {
        if (! $peserta->periode_mulai || ! $peserta->periode_selesai) {
            return false;
        }

        $tanggal = CarbonImmutable::instance($tanggal)->toDateString();

        return $tanggal >= $peserta->periode_mulai->toDateString()
            && $tanggal <= $peserta->periode_selesai->toDateString();
    }

    /**
     * Baris tabel untuk setiap tanggal dalam periode.
     *
     * @param  array{mulai: CarbonImmutable, selesai: CarbonImmutable}  $periode
     * @return array<int, array{tanggal: CarbonImmutable, logbook: ?LogbookHarian, libur: ?string, akses: string, dibuka: bool}>
     */
    public function hari(PesertaMagang $peserta, array $periode): array
    {
        $mulai = $periode['mulai']->toDateString();
        $selesai = $periode['selesai']->toDateString();

        $logbook = LogbookHarian::with(['buktis', 'izin'])
            ->where('peserta_magang_id', $peserta->id)
            ->whereDate('tanggal', '>=', $mulai)
            ->whereDate('tanggal', '<=', $selesai)
            ->get()
            ->keyBy(fn (LogbookHarian $item) => $item->tanggal->toDateString());

        $liburNasional = $this->hariLibur($mulai, $selesai);

        $dibuka = BukaKunciLogbook::where('peserta_magang_id', $peserta->id)
            ->whereDate('tanggal', '>=', $mulai)
            ->whereDate('tanggal', '<=', $selesai)
            ->whereDate('berlaku_sampai', '>=', CarbonImmutable::today()->toDateString())
            ->get()
            ->map(fn (BukaKunciLogbook $buka) => $buka->tanggal->toDateString())
            ->all();

        $hariIni = CarbonImmutable::today();
        $batas = $hariIni->subDays(PengaturanMagang::ambil()->batas_isi_mundur_hari);
        $baris = [];

        foreach (CarbonPeriod::create($periode['mulai'], $periode['selesai']) as $hari) {
            $tanggal = CarbonImmutable::instance($hari)->startOfDay();
            $kunci = $tanggal->toDateString();
            $terbuka = in_array($kunci, $dibuka, true);

            $akses = match (true) {
                ! $this->dalamMasaMagang($peserta, $tanggal) => self::DI_LUAR,
                $tanggal->gt($hariIni) => self::BELUM_TIBA,
                $tanggal->gte($batas) || $terbuka => self::BISA,
                default => self::LEWAT_BATAS,
            };

            $baris[] = [
                'tanggal' => $tanggal,
                'logbook' => $logbook->get($kunci),
                'libur' => $liburNasional[$kunci] ?? ($tanggal->isWeekend() ? $tanggal->locale('id')->translatedFormat('l') : null),
                'akses' => $akses,
                'dibuka' => $terbuka,
            ];
        }

        return $baris;
    }

    /**
     * Rekap kehadiran & uang saku satu periode.
     *
     * Uang saku: hari hadir yang tidak ditolak; hari penuh / setengah hari
     * memakai tarif yang berlaku pada tanggal masing-masing.
     *
     * @param  array<int, array{tanggal: CarbonImmutable, logbook: ?LogbookHarian, libur: ?string, akses: string}>  $hari
     * @return array{hari_penuh: int, setengah_hari: int, tidak_lengkap: int, izin: int, sakit: int, libur: int, belum_diisi: int, ditolak: int, menunggu: int, uang_saku: int, tarif: array<int, array{berlaku_mulai: string, hari_penuh: int, setengah_hari: int}>}
     */
    public function rekap(array $hari): array
    {
        $rekap = [
            'hari_penuh' => 0,
            'setengah_hari' => 0,
            'tidak_lengkap' => 0,
            'izin' => 0,
            'sakit' => 0,
            'libur' => 0,
            'belum_diisi' => 0,
            'ditolak' => 0,
            'menunggu' => 0,
            'uang_saku' => 0,
            'tarif' => [],
        ];

        $tarifs = TarifUangSaku::orderBy('berlaku_mulai')->get();
        $hariIni = CarbonImmutable::today();

        foreach ($hari as $baris) {
            $logbook = $baris['logbook'];

            if ($baris['akses'] === self::DI_LUAR) {
                continue;
            }

            if (! $logbook) {
                if ($baris['libur']) {
                    $rekap['libur']++;
                } elseif ($baris['tanggal']->lte($hariIni)) {
                    $rekap['belum_diisi']++;
                }

                continue;
            }

            if ($logbook->status_verifikasi === LogbookHarian::MENUNGGU) {
                $rekap['menunggu']++;
            }

            if ($logbook->status_verifikasi === LogbookHarian::DITOLAK) {
                $rekap['ditolak']++;

                continue;
            }

            if ($logbook->kehadiran !== LogbookHarian::HADIR) {
                $rekap[$logbook->kehadiran]++;

                continue;
            }

            $jenis = $logbook->jenisKehadiran();
            $rekap[$jenis]++;

            if ($jenis === 'tidak_lengkap') {
                continue;
            }

            $tarif = $tarifs->last(fn (TarifUangSaku $item) => $item->berlaku_mulai->toDateString() <= $baris['tanggal']->toDateString())
                ?? TarifUangSaku::berlakuPada($baris['tanggal']);

            $rekap['uang_saku'] += $jenis === 'hari_penuh' ? $tarif->tarif_hari_penuh : $tarif->tarif_setengah_hari;
            $rekap['tarif'][$tarif->berlaku_mulai->toDateString()] = [
                'berlaku_mulai' => $tarif->berlaku_mulai->toDateString(),
                'hari_penuh' => $tarif->tarif_hari_penuh,
                'setengah_hari' => $tarif->tarif_setengah_hari,
            ];
        }

        $rekap['tarif'] = array_values($rekap['tarif']);

        return $rekap;
    }

    /**
     * Tarif yang berlaku di akhir periode (untuk keterangan bila belum ada hari dibayar).
     */
    public function tarifPeriode(array $periode): TarifUangSaku
    {
        return TarifUangSaku::berlakuPada($periode['selesai']);
    }

    /**
     * @return array<string, string> [Y-m-d => keterangan]
     */
    private function hariLibur(string $mulai, string $selesai): array
    {
        return HariLibur::whereDate('tanggal', '>=', $mulai)
            ->whereDate('tanggal', '<=', $selesai)
            ->get()
            ->mapWithKeys(fn (HariLibur $libur) => [$libur->tanggal->toDateString() => $libur->keterangan])
            ->all();
    }

    /**
     * Rentang tanggal sebagai daftar Y-m-d.
     *
     * @return Collection<int, string>
     */
    public static function tanggalAntara(DateTimeInterface $mulai, DateTimeInterface $selesai): Collection
    {
        return collect(CarbonPeriod::create($mulai, $selesai))
            ->map(fn ($tanggal) => $tanggal->toDateString());
    }
}
