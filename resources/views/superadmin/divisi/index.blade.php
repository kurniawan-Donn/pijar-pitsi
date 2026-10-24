{{-- resources/views/superadmin/divisi/index.blade.php --}}

@extends('layouts.dashboard')

@section('title', 'Divisi')
@section('page_title', 'Divisi')
@section('page_subtitle', 'Daftar divisi untuk penempatan dan rotasi peserta magang')

@push('styles')
<style>
    .content {
        padding: 30px 32px 40px;
    }

    .panel {
        max-width: 860px;

        padding: 22px 24px;

        background: #ffffff;

        border: 1px solid #e5eaf0;
        border-radius: 13px;

        box-shadow: 0 4px 15px rgba(15, 40, 65, .04);
    }

    .panel > p {
        margin-bottom: 16px;

        color: #7b8794;
        font-size: 13px;
        line-height: 1.6;
    }

    .form-baru {
        display: flex;
        flex-wrap: wrap;
        align-items: flex-start;
        gap: 0 12px;

        margin-bottom: 18px;
        padding: 14px 16px 0;

        border-radius: 10px;

        background: #f7f9fc;
    }

    .form-baru .x-form-group {
        flex: 1 1 260px;
    }

    .form-baru .x-btn {
        margin-top: 24px;
    }

    table {
        width: 100%;
        border-collapse: collapse;
    }

    th {
        padding: 10px 12px;

        background: #f8fafc;
        color: #6b7c8d;

        font-size: 11px;
        font-weight: 800;
        letter-spacing: .4px;
        text-align: left;
        text-transform: uppercase;
    }

    td {
        padding: 10px 12px;

        border-top: 1px solid #edf1f5;

        font-size: 13px;
        vertical-align: middle;
    }

    tr.nonaktif td {
        opacity: .55;
    }

    td input[type="text"] {
        width: 100%;
        height: 38px;

        padding: 0 11px;

        border: 1px solid #d7e0eb;
        border-radius: 7px;

        font-family: inherit;
        font-size: 13px;
    }

    td select {
        height: 38px;

        padding: 0 8px;

        border: 1px solid #d7e0eb;
        border-radius: 7px;

        font-family: inherit;
        font-size: 13px;
    }

    .aksi {
        display: flex;
        gap: 8px;
        justify-content: flex-end;
    }

    .aksi form {
        margin: 0;
    }

    .x-btn {
        min-height: 36px;
    }

    .muted {
        color: #9aa7b4;
        font-size: 12px;
    }

    .empty {
        padding: 18px;

        color: #9aa7b4;
        text-align: center;
    }

    @media (max-width: 800px) {
        .content {
            padding: 22px 16px 30px;
        }

        .table-scroll {
            overflow-x: auto;
        }

        table {
            min-width: 560px;
        }
    }
</style>
@endpush

@section('content')

<div class="content">

    <x-flash-messages />

    <section class="panel">
        <p>
            Divisi dipilih saat menentukan penempatan peserta per periode. Divisi yang sudah dipakai
            tidak bisa dihapus; nonaktifkan bila tidak dipakai lagi.
        </p>

        <x-alert type="error" title="Divisi belum ditambahkan:" :messages="$errors->baru->all()" />

        <form method="POST" action="{{ route('superadmin.divisi.store') }}" class="form-baru">
            @csrf
            <x-form.input name="nama" label="Nama Divisi Baru" maxlength="100" placeholder="mis. Divisi Konsultan" required />
            <x-button>+ Tambah Divisi</x-button>
        </form>

        <div class="table-scroll">
            <table>
                <thead>
                    <tr>
                        <th>Nama Divisi</th>
                        <th style="width: 130px;">Status</th>
                        <th style="width: 110px;">Dipakai</th>
                        <th style="width: 170px;"></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($divisis as $divisi)
                        <tr @class(['nonaktif' => ! $divisi->aktif])>
                            <td>
                                <input type="text" name="nama" value="{{ $divisi->nama }}" maxlength="100" required form="divisi-{{ $divisi->id }}" aria-label="Nama divisi">
                            </td>
                            <td>
                                <select name="aktif" form="divisi-{{ $divisi->id }}" aria-label="Status {{ $divisi->nama }}">
                                    <option value="1" @selected($divisi->aktif)>Aktif</option>
                                    <option value="0" @selected(! $divisi->aktif)>Nonaktif</option>
                                </select>
                            </td>
                            <td class="muted">{{ $divisi->penempatans_count }} penempatan</td>
                            <td>
                                <div class="aksi">
                                    <form method="POST" action="{{ route('superadmin.divisi.update', $divisi) }}" id="divisi-{{ $divisi->id }}">
                                        @csrf
                                        @method('PUT')
                                        <x-button variant="secondary">Simpan</x-button>
                                    </form>

                                    @if ($divisi->penempatans_count === 0)
                                        <form method="POST" action="{{ route('superadmin.divisi.destroy', $divisi) }}" onsubmit="return confirm('Hapus divisi ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <x-button variant="danger">Hapus</x-button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="empty">Belum ada divisi. Tambahkan divisi pertama di atas.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

</div>

@endsection
