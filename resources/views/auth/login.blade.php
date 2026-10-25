{{-- resources/views/auth/login.blade.php — login satu pintu untuk admin, super admin, dan peserta. --}}
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#0b6a9c">
    <title>Login - {{ config('app.name') }}</title>

    <link rel="icon" type="image/png" href="{{ asset('images/logo-pitsi-128.png') }}">

    @include('partials.font')
    @include('partials.tema')

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            padding: 24px 16px;

            display: grid;
            place-items: center;

            background: var(--latar);
            color: var(--teks);

            font-family: var(--font);
            -webkit-font-smoothing: antialiased;
        }

        .login-box {
            width: 100%;
            max-width: 400px;
            padding: 32px 28px;

            border: 1px solid var(--garis);
            border-radius: 16px;

            background: var(--permukaan);

            box-shadow: 0 12px 32px rgba(17, 24, 39, .06);
        }

        .brand {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 10px;

            margin-bottom: 24px;

            text-align: center;
        }

        .brand img {
            width: 56px;
            height: 56px;

            object-fit: contain;
        }

        .brand h1 {
            margin: 0;

            color: var(--utama-gelap);

            font-size: 24px;
            letter-spacing: 2px;
        }

        .brand .tagline {
            margin: 0;

            color: var(--teks-sekunder);
            font-size: 13px;
        }

        .petunjuk {
            margin: 0 0 20px;

            color: var(--teks-sekunder);
            font-size: 13.5px;
            text-align: center;
        }

        .form-group {
            margin-bottom: 16px;
        }

        .form-group label {
            display: block;

            margin-bottom: 6px;

            font-size: 13px;
            font-weight: 600;
        }

        .form-group input {
            width: 100%;
            height: 44px;
            padding: 0 12px;

            border: 1px solid var(--garis-kuat);
            border-radius: var(--radius-kecil);

            color: var(--teks);

            font-family: inherit;
            font-size: 15px;

            outline: none;
        }

        .form-group input:focus {
            border-color: var(--utama);
            box-shadow: var(--fokus);
        }

        .btn {
            width: 100%;
            height: 44px;

            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;

            border: none;
            border-radius: var(--radius-kecil);

            background: var(--utama);
            color: #ffffff;

            font-family: inherit;
            font-size: 15px;
            font-weight: 700;

            cursor: pointer;
        }

        .btn:hover {
            background: var(--utama-gelap);
        }

        .btn:focus-visible {
            outline: 2px solid var(--utama-gelap);
            outline-offset: 2px;
        }

        .error {
            display: flex;
            gap: 8px;

            margin-bottom: 18px;
            padding: 11px 12px;

            border: 1px solid #fecdca;
            border-radius: var(--radius-kecil);

            background: var(--bahaya-muda);
            color: var(--bahaya);

            font-size: 13.5px;
        }

        .kembali {
            display: block;

            margin-top: 18px;

            color: var(--teks-sekunder);
            font-size: 13px;
            text-align: center;
            text-decoration: none;
        }

        .kembali:hover {
            color: var(--utama-gelap);
        }
    </style>
</head>

<body>

<main class="login-box">

    <div class="brand">
        <img src="{{ asset('images/logo-pitsi-128.png') }}" alt="Logo PT Pilar Teknotama Sinergi" width="56" height="56">
        <h1>{{ config('app.name') }}</h1>
        <p class="tagline">Tumbuh bersama, kokoh berkarya.</p>
    </div>

    <p class="petunjuk">Masuk sebagai admin atau peserta magang</p>

    @if ($errors->any())
        <div class="error" role="alert">
            <x-icon name="circle-alert" :size="18" />
            <span>{{ $errors->first() }}</span>
        </div>
    @endif

    <form action="{{ route('login.process') }}" method="POST">
        @csrf

        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" placeholder="Masukkan email" value="{{ old('email') }}" autocomplete="username" required autofocus>
        </div>

        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" placeholder="Masukkan password" autocomplete="current-password" required>
        </div>

        <button type="submit" class="btn">
            <x-icon name="log-in" :size="18" />
            Masuk
        </button>
    </form>

    <a href="{{ route('home') }}" class="kembali">Kembali ke beranda</a>

</main>

</body>

</html>
