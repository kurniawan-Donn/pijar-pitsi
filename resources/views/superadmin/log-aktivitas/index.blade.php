{{-- resources/views/superadmin/log-aktivitas/index.blade.php --}}

@extends('layouts.dashboard')

@use('App\Models\LogAktivitas')

@section('title', 'Log Aktivitas')
@section('page_title', 'Log Aktivitas')
@section('page_subtitle', 'Riwayat aksi penting: siapa mengubah apa dan kapan')

@push('styles')
<style>
    .content {
        padding: 30px 32px 40px;
    }

    .panel {
        background: #ffffff;

        border: 1px solid var(--garis);
        border-radius: 13px;

        box-shadow: 0 4px 15px rgba(15, 40, 65, .04);

        overflow: hidden;
    }

    .filter-form {
        display: flex;
        align-items: flex-end;
        flex-wrap: wrap;
        gap: 12px;

        padding: 18px 20px;

        border-bottom: 1px solid var(--garis);
    }

    .filter-form label {
        display: block;

        margin-bottom: 6px;

        color: var(--teks-sekunder);
        font-size: 11px;
        font-weight: 800;
        text-transform: uppercase;
    }

    .filter-form input,
    .filter-form select {
        height: 40px;

        padding: 0 11px;

        border: 1px solid var(--garis-kuat);
        border-radius: 7px;

        background: #ffffff;

        font-family: inherit;
        font-size: 13px;
    }

    .reset {
        align-self: center;

        color: var(--utama);
        font-size: 12px;
        font-weight: 700;
    }

    .table-wrapper {
        overflow-x: auto;
    }

    table {
        width: 100%;
        border-collapse: collapse;
    }

    th {
        padding: 11px 16px;

        background: var(--permukaan-redup);
        color: var(--teks-sekunder);

        font-size: 11px;
        font-weight: 800;
        text-align: left;
        text-transform: uppercase;
        white-space: nowrap;
    }

    td {
        padding: 12px 16px;

        border-top: 1px solid var(--garis);

        color: var(--teks);

        font-size: 13px;
        vertical-align: top;
    }

    .waktu {
        color: var(--utama-gelap);
        font-weight: 700;
        white-space: nowrap;
    }

    .sub {
        margin-top: 2px;

        color: var(--teks-sekunder);
        font-size: 11px;
    }

    .aksi {
        display: inline-block;

        padding: 4px 9px;

        border-radius: 999px;

        background: var(--utama-muda);
        color: var(--utama-gelap);

        font-size: 11px;
        font-weight: 800;
        white-space: nowrap;
    }

    .aksi.hapus {
        background: var(--bahaya-muda);
        color: var(--bahaya);
    }

    details summary {
        color: var(--utama);
        font-size: 12px;
        font-weight: 700;

        cursor: pointer;
    }

    details pre {
        max-width: 420px;

        margin-top: 6px;
        padding: 10px;

        border-radius: 8px;

        background: var(--permukaan-redup);

        font-size: 11px;
        white-space: pre-wrap;
        word-break: break-word;
    }

    .empty-state {
        padding: 46px 20px;

        color: var(--teks-sekunder);
        text-align: center;
    }

    @media (max-width: 800px) {
        .content {
            padding: 22px 20px 30px;
        }
    }
</style>
@endpush

@section('content')

<div class="content">

    <section class="panel">

        <form method="GET" action="{{ route('superadmin.log-aktivitas.index') }}" class="filter-form">
            <div>
                <label for="aksi">Aksi</label>
                <select id="aksi" name="aksi">
                    <option value="">Semua aksi</option>
                    @foreach (LogAktivitas::AKSI as $kode => $label)
                        <option value="{{ $kode }}" @selected(($filter['aksi'] ?? null) === $kode)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="user_id">Pengguna</label>
                <select id="user_id" name="user_id">
                    <option value="">Semua pengguna</option>
                    @foreach ($pengguna as $item)
                        <option value="{{ $item->id }}" @selected((int) ($filter['user_id'] ?? 0) === $item->id)>{{ $item->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="dari">Dari</label>
                <input type="date" id="dari" name="dari" value="{{ $filter['dari'] ?? '' }}">
            </div>

            <div>
                <label for="sampai">Sampai</label>
                <input type="date" id="sampai" name="sampai" value="{{ $filter['sampai'] ?? '' }}">
            </div>

            <x-button>Tampilkan</x-button>

            @if (array_filter($filter))
                <a href="{{ route('superadmin.log-aktivitas.index') }}" class="reset">Reset</a>
            @endif
        </form>

        @if ($logs->isEmpty())
            <div class="empty-state">Belum ada aktivitas yang tercatat.</div>
        @else
            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th>Waktu</th>
                            <th>Pengguna</th>
                            <th>Aksi</th>
                            <th>Keterangan</th>
                            <th>Detail</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($logs as $log)
                            <tr>
                                <td>
                                    <div class="waktu">{{ $log->created_at->translatedFormat('d M Y, H:i') }}</div>
                                    <div class="sub">{{ $log->created_at->diffForHumans() }}</div>
                                </td>
                                <td>
                                    {{ $log->nama_pengguna ?? 'Sistem' }}
                                    @if ($log->ip_address)
                                        <div class="sub">IP {{ $log->ip_address }}</div>
                                    @endif
                                </td>
                                <td>
                                    <span @class(['aksi', 'hapus' => str_starts_with($log->aksi, 'hapus_')])>{{ $log->labelAksi() }}</span>
                                </td>
                                <td>{{ $log->keterangan }}</td>
                                <td>
                                    @if ($log->data)
                                        <details>
                                            <summary>Lihat</summary>
                                            <pre>{{ json_encode($log->data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) }}</pre>
                                        </details>
                                    @else
                                        <span class="sub">-</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif

        <x-pagination :paginator="$logs" />

    </section>

</div>

@endsection
