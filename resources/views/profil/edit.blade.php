{{-- resources/views/profil/edit.blade.php --}}

@extends('layouts.dashboard')

@section('title', 'Profil Saya')
@section('page_title', 'Profil Saya')
@section('page_subtitle', 'Ubah nama, email, dan password akun Anda')

@push('styles')
<style>
    .content {
        padding: 30px 32px 40px;
    }

    .grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 18px;
        align-items: start;

        max-width: 1000px;
    }

    .panel {
        padding: 24px;

        background: #ffffff;

        border: 1px solid #e5eaf0;
        border-radius: 13px;

        box-shadow: 0 4px 15px rgba(15, 40, 65, .04);
    }

    .panel h3 {
        color: #17324d;
        font-size: 16px;
    }

    .panel > p {
        margin: 4px 0 20px;

        color: #7b8794;
        font-size: 12px;
        line-height: 1.6;
    }

    .actions {
        display: flex;
        justify-content: flex-end;
    }

    .akun-info {
        margin-bottom: 18px;
        padding: 12px 14px;

        border-radius: 10px;

        background: #f7f9fc;

        color: #4b5d70;
        font-size: 12px;
    }

    @media (max-width: 900px) {
        .grid {
            grid-template-columns: 1fr;
        }
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

    <x-alert type="success" :message="session('success')" />
    <x-alert type="warning" :message="session('warning')" />

    @if ($user->wajib_ganti_password)
        <x-alert type="info" message="Akun Anda masih memakai password sementara dari admin. Ganti password di panel Ganti Password untuk membuka halaman lain." />
    @endif

    <div class="grid">

        <section class="panel">
            <h3>Data Akun</h3>
            <p>Nama ditampilkan di topbar dan log aktivitas. Email dipakai untuk login.</p>

            <div class="akun-info">
                Role: <strong>{{ $user->labelRole() }}</strong>
                · terdaftar {{ $user->created_at?->translatedFormat('d F Y') }}
            </div>

            <form method="POST" action="{{ route('profil.update') }}">
                @csrf
                @method('PUT')

                <x-form.input name="name" label="Nama" :value="$user->name" maxlength="255" required autocomplete="name" />
                <x-form.input name="email" type="email" label="Email" :value="$user->email" maxlength="255" required autocomplete="email" />

                <div class="actions">
                    <x-button>Simpan Profil</x-button>
                </div>
            </form>
        </section>

        <section class="panel">
            <h3>Ganti Password</h3>
            <p>Minimal 8 karakter dan berisi huruf serta angka. Masukkan password saat ini untuk konfirmasi.</p>

            <form method="POST" action="{{ route('profil.password') }}">
                @csrf
                @method('PUT')

                <x-form.input name="current_password" type="password" label="Password Saat Ini" required autocomplete="current-password" />
                <x-form.input name="password" type="password" label="Password Baru" required autocomplete="new-password" />
                <x-form.input name="password_confirmation" type="password" label="Konfirmasi Password Baru" required autocomplete="new-password" />

                <div class="actions">
                    <x-button>Ganti Password</x-button>
                </div>
            </form>
        </section>

    </div>

</div>

@endsection
