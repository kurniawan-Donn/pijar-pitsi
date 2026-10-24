{{-- resources/views/superadmin/peserta-magang/logbook.blade.php --}}

@extends('layouts.dashboard')

@use('App\Http\Controllers\SuperAdmin\LogbookController')

@section('title', 'Logbook ' . $pesertaMagang->nama)
@section('page_title', 'Logbook Kegiatan')
@section('page_subtitle', $pesertaMagang->nama . ' · ' . $bulanDipilih->translatedFormat('F Y'))

@php
    $warnaAction = [
        'Diterima' => 'disetujui',
        'Ditolak' => 'ditolak',
        'Review' => 'menunggu',
    ];
@endphp

@push('styles')
<style>
    .content {
        padding: 30px 32px 40px;
    }

    .page-actions {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 12px;

        margin-bottom: 20px;
    }

    .back-link {
        color: #1779c9;
        font-size: 13px;
        font-weight: 700;
    }

    .month-form {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .month-form label {
        color: #4b5d70;
        font-size: 12px;
        font-weight: 800;
    }

    .month-form select {
        min-width: 190px;
        height: 40px;

        padding: 0 12px;

        border: 1px solid #d7e0eb;
        border-radius: 8px;

        background: #ffffff;

        font-family: inherit;
        font-size: 13px;
        font-weight: 700;

        cursor: pointer;
    }

    .stat-grid {
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        gap: 14px;

        margin-bottom: 20px;
    }

    .panel {
        background: #ffffff;

        border: 1px solid #e5eaf0;
        border-radius: 13px;

        box-shadow: 0 4px 15px rgba(15, 40, 65, .04);

        overflow: hidden;
    }

    .bulk-bar {
        position: sticky;
        top: 0;
        z-index: 5;

        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 12px;

        padding: 14px 20px;

        border-bottom: 1px solid #edf1f5;

        background: #ffffff;
    }

    .bulk-info {
        color: #4b5d70;
        font-size: 13px;
    }

    .bulk-info strong {
        color: #1779c9;
    }

    .bulk-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
    }

    .bulk-actions .x-btn {
        min-width: 0;
    }

    .x-btn-review {
        background: #fff6e8;
        border-color: #f6d3a4;
        color: #b4630c;
    }

    .x-btn-review:hover {
        background: #ffecd2;
    }

    .x-btn[disabled] {
        opacity: .45;
        cursor: not-allowed;
    }

    .select-shortcuts {
        display: flex;
        gap: 12px;

        font-size: 12px;
    }

    .select-shortcuts button {
        border: 0;
        background: none;
        color: #1779c9;

        font-family: inherit;
        font-weight: 700;

        cursor: pointer;
    }

    .table-wrapper {
        overflow-x: auto;
    }

    table {
        width: 100%;
        border-collapse: collapse;
    }

    th {
        padding: 12px 16px;

        background: #f8fafc;
        color: #6b7c8d;

        font-size: 11px;
        font-weight: 800;
        letter-spacing: .4px;
        text-align: left;
        text-transform: uppercase;
        white-space: nowrap;
    }

    td {
        padding: 14px 16px;

        border-top: 1px solid #edf1f5;

        color: #33475b;

        font-size: 13px;
        line-height: 1.55;
        vertical-align: top;
    }

    tbody tr {
        cursor: pointer;

        transition: background .15s ease;
    }

    tbody tr:hover {
        background: #fafcff;
    }

    tbody tr.selected {
        background: #f0f7fd;
    }

    .check {
        width: 18px;
        height: 18px;

        accent-color: #1779c9;
    }

    .tanggal {
        color: #17324d;
        font-weight: 700;
        white-space: nowrap;
    }

    .activity {
        min-width: 260px;
        white-space: pre-line;
    }

    .muted {
        color: #9aa7b3;
    }

    .x-badge-belum {
        background: #f1f4f7;
        color: #7b8794;
    }

    .alert-sheet {
        margin-bottom: 18px;
        padding: 14px 16px;

        border: 1px solid #ffd1d1;
        border-radius: 10px;

        background: #fff0f0;
        color: #c62828;

        font-size: 13px;
        font-weight: 600;
    }

    .empty-state {
        padding: 50px 20px;

        color: #7b8794;
        text-align: center;
    }

    .empty-state strong {
        display: block;

        margin-bottom: 6px;

        color: #17324d;
        font-size: 15px;
    }

    @media (max-width: 1100px) {
        .stat-grid {
            grid-template-columns: repeat(3, 1fr);
        }
    }

    @media (max-width: 800px) {
        .content {
            padding: 22px 20px 30px;
        }

        .stat-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }
</style>
@endpush

@section('content')

<div class="content">

    <div class="page-actions">
        <a href="{{ route('superadmin.peserta-magang.show', ['pesertaMagang' => $pesertaMagang, 'bulan' => $bulanDipilih->format('Y-m')]) }}" class="back-link">
            ← Kembali ke detail peserta
        </a>

        <form method="GET" action="{{ route('superadmin.peserta-magang.logbook', $pesertaMagang) }}" class="month-form">
            <label for="bulan">Bulan</label>
            <select id="bulan" name="bulan" onchange="this.form.submit()">
                @foreach ($pilihanBulan as $nilai => $label)
                    <option value="{{ $nilai }}" @selected($nilai === $bulanDipilih->format('Y-m'))>{{ $label }}</option>
                @endforeach
            </select>
            <noscript><x-button variant="secondary">Tampilkan</x-button></noscript>
        </form>
    </div>

    <x-flash-messages />

    @if ($sheetError)
        <div class="alert-sheet">{{ $sheetError }}</div>
    @endif

    <div class="stat-grid">
        <x-stat-card label="Hari Berisi Kegiatan" :value="$ringkasan['total']" icon="📒" />
        <x-stat-card label="Diterima" :value="$ringkasan['Diterima']" icon="✓" />
        <x-stat-card label="Review" :value="$ringkasan['Review']" icon="⏳" />
        <x-stat-card label="Ditolak" :value="$ringkasan['Ditolak']" icon="✕" />
        <x-stat-card label="Belum Diperiksa" :value="$ringkasan['belum']" icon="•" />
    </div>

    <section class="panel">

        @if (empty($logbook))
            <div class="empty-state">
                <strong>Belum ada kegiatan tercatat</strong>
                @if (! $sheetError)
                    Kolom Activity/Output pada tab {{ $tabAktif ?? $bulanDipilih->translatedFormat('F Y') }} masih kosong.
                @endif
            </div>
        @else
            <form method="POST" action="{{ route('superadmin.peserta-magang.logbook.update', $pesertaMagang) }}" id="form-logbook">
                @csrf
                @method('PUT')
                <input type="hidden" name="bulan" value="{{ $bulanDipilih->format('Y-m') }}">

                <div class="bulk-bar">
                    <div>
                        <div class="bulk-info"><strong id="jumlah-dipilih">0</strong> hari dipilih</div>
                        <div class="select-shortcuts">
                            <button type="button" data-pilih="semua">Pilih semua</button>
                            <button type="button" data-pilih="belum">Pilih yang belum diperiksa</button>
                            <button type="button" data-pilih="tidak">Batal pilih</button>
                        </div>
                    </div>

                    <div class="bulk-actions">
                        <x-button name="action" value="Diterima" disabled data-bulk>✓ Terima</x-button>
                        <x-button name="action" value="Review" variant="review" disabled data-bulk>⏳ Review</x-button>
                        <x-button name="action" value="Ditolak" variant="danger" disabled data-bulk>✕ Tolak</x-button>
                    </div>
                </div>

                <div class="table-wrapper">
                    <table>
                        <thead>
                            <tr>
                                <th style="width: 40px;"></th>
                                <th>Tanggal</th>
                                <th>Activity</th>
                                <th>Output</th>
                                <th>Lokasi</th>
                                <th>Action</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($logbook as $row)
                                @php($action = LogbookController::actionBaku($row['action']))
                                <tr data-belum="{{ $action ? '0' : '1' }}">
                                    <td>
                                        <input
                                            type="checkbox"
                                            class="check"
                                            name="baris[]"
                                            value="{{ $row['sheet_row'] }}"
                                            aria-label="Pilih {{ $row['tanggal'] ?: 'hari ke-' . $row['no'] }}"
                                            @checked(in_array((string) $row['sheet_row'], old('baris', []), true))
                                        >
                                    </td>
                                    <td class="tanggal">{{ $row['tanggal'] ?: '-' }}</td>
                                    <td class="activity">{{ $row['activity'] ?: '-' }}</td>
                                    <td>{!! $row['output'] ? e($row['output']) : '<span class="muted">-</span>' !!}</td>
                                    <td>{!! $row['lokasi'] ? e($row['lokasi']) : '<span class="muted">-</span>' !!}</td>
                                    <td>
                                        @if ($action)
                                            <x-status-badge :status="$warnaAction[$action]" :label="$action" />
                                        @else
                                            <x-status-badge status="belum" label="Belum diperiksa" />
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </form>
        @endif

    </section>

</div>

@endsection

@push('scripts')
<script>
(() => {
    const form = document.getElementById('form-logbook');

    if (!form) {
        return;
    }

    const checks = [...form.querySelectorAll('input[name="baris[]"]')];
    const counter = document.getElementById('jumlah-dipilih');
    const buttons = [...form.querySelectorAll('[data-bulk]')];

    const refresh = () => {
        const jumlah = checks.filter((check) => check.checked).length;
        counter.textContent = jumlah;
        buttons.forEach((button) => { button.disabled = jumlah === 0; });
        checks.forEach((check) => check.closest('tr').classList.toggle('selected', check.checked));
    };

    // Klik di mana pun pada baris ikut mencentang.
    form.querySelectorAll('tbody tr').forEach((row) => {
        row.addEventListener('click', (event) => {
            if (event.target.matches('input, a, button')) {
                return;
            }
            const check = row.querySelector('input[type="checkbox"]');
            check.checked = !check.checked;
            refresh();
        });
    });

    checks.forEach((check) => check.addEventListener('change', refresh));

    form.querySelectorAll('[data-pilih]').forEach((button) => {
        button.addEventListener('click', () => {
            const mode = button.dataset.pilih;
            checks.forEach((check) => {
                check.checked = mode === 'semua' || (mode === 'belum' && check.closest('tr').dataset.belum === '1');
            });
            refresh();
        });
    });

    form.addEventListener('submit', (event) => {
        const action = event.submitter?.value;
        if (action === 'Ditolak' && !confirm('Tandai hari terpilih sebagai "Ditolak"?')) {
            event.preventDefault();
        }
    });

    refresh();
})();
</script>
@endpush
