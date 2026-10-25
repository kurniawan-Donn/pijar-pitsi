{{-- resources/views/superadmin/faq/index.blade.php --}}

@extends('layouts.dashboard')

@section('title', 'FAQ')
@section('page_title', 'FAQ')
@section('page_subtitle', 'Pertanyaan yang sering diajukan di website publik')

@push('styles')
<style>
    .content {
        padding: 30px 32px 40px;
    }

    .panel {
        max-width: 920px;
        margin-bottom: 18px;
        padding: 22px 24px;

        background: #ffffff;

        border: 1px solid #e5eaf0;
        border-radius: 13px;

        box-shadow: 0 4px 15px rgba(15, 40, 65, .04);
    }

    .panel h3 {
        margin-bottom: 4px;

        color: #17324d;
        font-size: 15px;
    }

    .panel > p {
        margin-bottom: 16px;

        color: #7b8794;
        font-size: 13px;
        line-height: 1.6;
    }

    .fields {
        display: grid;
        grid-template-columns: minmax(0, 2fr) minmax(0, 1fr);
        gap: 0 14px;
    }

    .field {
        margin-bottom: 14px;
    }

    .field-penuh {
        grid-column: 1 / -1;
    }

    .field label {
        display: block;

        margin-bottom: 6px;

        color: #174a7e;
        font-size: 12px;
        font-weight: 700;
    }

    .field input[type="text"],
    .field textarea {
        width: 100%;

        padding: 10px 12px;

        border: 1px solid #d7e0eb;
        border-radius: 7px;

        background: #ffffff;
        color: #243f5c;

        font-family: inherit;
        font-size: 13px;
        line-height: 1.6;
    }

    .field textarea {
        min-height: 90px;
        resize: vertical;
    }

    .baris-aksi {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
    }

    .check {
        display: inline-flex;
        align-items: center;
        gap: 7px;

        color: #34495e;
        font-size: 13px;
        font-weight: 600;
    }

    .daftar-faq {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    .faq-item {
        border: 1px solid #e5eaf0;
        border-radius: 11px;

        background: #ffffff;
    }

    .faq-item.tersembunyi {
        background: #f7f9fc;
    }

    .faq-item[open] {
        border-color: #b9d4ee;
    }

    .faq-item summary {
        display: flex;
        align-items: center;
        gap: 10px;

        padding: 12px 14px;

        cursor: pointer;
        list-style: none;
    }

    .faq-item summary::-webkit-details-marker {
        display: none;
    }

    .nomor {
        flex-shrink: 0;

        width: 26px;

        color: #8a99a8;
        font-size: 12px;
        font-weight: 800;
    }

    .judul {
        flex: 1;
        min-width: 0;

        color: #17324d;
        font-size: 13.5px;
        font-weight: 700;
    }

    .faq-item.tersembunyi .judul {
        color: #8a99a8;
    }

    .badge {
        display: inline-block;

        padding: 2px 8px;

        border-radius: 999px;

        font-size: 10.5px;
        font-weight: 800;
        white-space: nowrap;
    }

    .b-biru { background: #eaf3fb; color: #175c95; }
    .b-abu { background: #eef2f6; color: #52646e; }

    .geser {
        display: flex;
        gap: 4px;
    }

    .geser form {
        margin: 0;
    }

    .geser button {
        width: 30px;
        height: 30px;

        border: 1px solid #d7e0eb;
        border-radius: 7px;

        background: #ffffff;
        color: #52646e;

        cursor: pointer;
    }

    .geser button:disabled {
        opacity: .35;
        cursor: default;
    }

    .faq-isi {
        padding: 4px 14px 14px 50px;
    }

    .link-hapus {
        border: none;

        background: none;
        color: #c62828;

        font-family: inherit;
        font-size: 12px;
        font-weight: 700;

        cursor: pointer;
    }

    .empty {
        padding: 24px;

        color: #9aa7b4;
        text-align: center;
    }

    @media (max-width: 800px) {
        .content {
            padding: 22px 16px 30px;
        }

        .fields {
            grid-template-columns: 1fr;
        }

        .faq-isi {
            padding-left: 14px;
        }
    }
</style>
@endpush

@section('content')

<div class="content">

    <x-flash-messages :validation="false" />

    <datalist id="daftar-kategori">
        @foreach ($kategoris as $kategori)
            <option value="{{ $kategori }}">
        @endforeach
    </datalist>

    {{-- TAMBAH FAQ --}}
    <section class="panel">
        <h3>Tambah Pertanyaan</h3>
        <p>FAQ tampil di beranda website sebagai daftar yang bisa dibuka-tutup, dikelompokkan per kategori bila diisi.</p>

        <x-alert type="error" title="FAQ belum ditambahkan:" :messages="$errors->baru->all()" />

        <form method="POST" action="{{ route('superadmin.faq.store') }}">
            @csrf

            <div class="fields">
                <div class="field">
                    <label for="pertanyaan-baru">Pertanyaan *</label>
                    <input type="text" id="pertanyaan-baru" name="pertanyaan" value="{{ $errors->baru->any() ? old('pertanyaan') : '' }}" maxlength="255" required>
                </div>
                <div class="field">
                    <label for="kategori-baru">Kategori (opsional)</label>
                    <input type="text" id="kategori-baru" name="kategori" value="{{ $errors->baru->any() ? old('kategori') : '' }}" maxlength="100" list="daftar-kategori" placeholder="mis. Pendaftaran">
                </div>
                <div class="field field-penuh">
                    <label for="jawaban-baru">Jawaban *</label>
                    <textarea id="jawaban-baru" name="jawaban" maxlength="5000" required>{{ $errors->baru->any() ? old('jawaban') : '' }}</textarea>
                </div>
            </div>

            <div class="baris-aksi">
                <label class="check">
                    <input type="checkbox" name="tampil" value="1" @checked(! $errors->baru->any() || old('tampil'))>
                    Tampilkan di website
                </label>
                <x-button>+ Tambah FAQ</x-button>
            </div>
        </form>
    </section>

    {{-- DAFTAR FAQ --}}
    <section class="panel">
        <h3>Daftar FAQ ({{ $faqs->count() }})</h3>
        <p>Klik pertanyaan untuk mengubah. Gunakan tombol ▲ ▼ untuk mengatur urutan tampil.</p>

        @if ($faqs->isEmpty())
            <div class="empty">Belum ada FAQ. Tambahkan pertanyaan pertama di atas.</div>
        @else
            <div class="daftar-faq">
                @foreach ($faqs as $faq)
                    @php
                        $bag = 'faq'.$faq->id;
                        $gagal = $errors->{$bag}->any();
                    @endphp

                    <details class="faq-item {{ $faq->tampil ? '' : 'tersembunyi' }}" id="faq-{{ $faq->id }}" @if ($gagal) open @endif>
                        <summary>
                            <span class="nomor">{{ $loop->iteration }}.</span>
                            <span class="judul">{{ $faq->pertanyaan }}</span>
                            @if ($faq->kategori)
                                <span class="badge b-biru">{{ $faq->kategori }}</span>
                            @endif
                            @unless ($faq->tampil)
                                <span class="badge b-abu">Disembunyikan</span>
                            @endunless
                            <span class="geser">
                                <form method="POST" action="{{ route('superadmin.faq.urutan', $faq) }}">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="arah" value="naik">
                                    <button type="submit" title="Naikkan" aria-label="Naikkan urutan" @disabled($loop->first)>▲</button>
                                </form>
                                <form method="POST" action="{{ route('superadmin.faq.urutan', $faq) }}">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="arah" value="turun">
                                    <button type="submit" title="Turunkan" aria-label="Turunkan urutan" @disabled($loop->last)>▼</button>
                                </form>
                            </span>
                        </summary>

                        <div class="faq-isi">
                            <x-alert type="error" title="FAQ belum disimpan:" :messages="$errors->{$bag}->all()" />

                            <form method="POST" action="{{ route('superadmin.faq.update', $faq) }}">
                                @csrf
                                @method('PUT')

                                <div class="fields">
                                    <div class="field">
                                        <label for="pertanyaan-{{ $faq->id }}">Pertanyaan *</label>
                                        <input type="text" id="pertanyaan-{{ $faq->id }}" name="pertanyaan" value="{{ $gagal ? old('pertanyaan') : $faq->pertanyaan }}" maxlength="255" required>
                                    </div>
                                    <div class="field">
                                        <label for="kategori-{{ $faq->id }}">Kategori (opsional)</label>
                                        <input type="text" id="kategori-{{ $faq->id }}" name="kategori" value="{{ $gagal ? old('kategori') : $faq->kategori }}" maxlength="100" list="daftar-kategori">
                                    </div>
                                    <div class="field field-penuh">
                                        <label for="jawaban-{{ $faq->id }}">Jawaban *</label>
                                        <textarea id="jawaban-{{ $faq->id }}" name="jawaban" maxlength="5000" required>{{ $gagal ? old('jawaban') : $faq->jawaban }}</textarea>
                                    </div>
                                </div>

                                <div class="baris-aksi">
                                    <label class="check">
                                        <input type="checkbox" name="tampil" value="1" @checked($gagal ? old('tampil') : $faq->tampil)>
                                        Tampilkan di website
                                    </label>
                                    <x-button variant="secondary">Simpan</x-button>
                                </div>
                            </form>

                            <form method="POST" action="{{ route('superadmin.faq.destroy', $faq) }}" onsubmit="return confirm('Hapus FAQ ini?')" style="margin-top: 10px;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="link-hapus">Hapus FAQ</button>
                            </form>
                        </div>
                    </details>
                @endforeach
            </div>
        @endif
    </section>

</div>

@endsection
