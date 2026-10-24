{{--
    Bagian pendaftaran magang: form website (multi-langkah) + Google Form.
    Variabel: $googleFormUrl (null bila belum diatur).
--}}
@php
    $berhasil = session('pendaftaran_berhasil');

    // Field per langkah, dipakai untuk membuka langkah yang berisi error.
    $langkahForm = [
        1 => ['nama', 'nim', 'email', 'no_hp'],
        2 => ['asal_kampus', 'program_studi', 'fakultas'],
        3 => ['posisi', 'tanggal_mulai', 'tanggal_selesai', 'keterangan', 'dokumen', 'persetujuan'],
    ];

    $langkahAwal = 1;

    foreach ($langkahForm as $langkah => $fields) {
        if ($errors->hasAny($fields)) {
            $langkahAwal = $langkah;
            break;
        }
    }

    $adaErrorForm = $errors->hasAny(array_merge(...array_values($langkahForm)));
@endphp

<section id="daftar" class="section">
    <div class="container">

        <div class="section-heading" data-reveal>
            <span class="section-label">Pendaftaran</span>
            <h2>Daftar Magang Sekarang</h2>
            <p>Isi formulir langsung di website ini dalam 3 langkah singkat{{ $googleFormUrl ? ', atau gunakan Google Form bila lebih nyaman' : '' }}.</p>
        </div>

        <div class="register-wrap">

            {{-- INFO SAMPING --}}
            <aside class="register-aside" data-reveal>
                <h3>Sebelum mulai, siapkan:</h3>
                <p>Proses pengisian sekitar 5 menit. Data tersimpan aman dan hanya dilihat oleh admin.</p>

                <ul class="aside-list">
                    <li>
                        <span class="ico">🪪</span>
                        <div>Data diri & NIM <small>Nama, email aktif, nomor WhatsApp</small></div>
                    </li>
                    <li>
                        <span class="ico">🎓</span>
                        <div>Data akademik <small>Kampus, program studi, fakultas</small></div>
                    </li>
                    <li>
                        <span class="ico">📄</span>
                        <div>Surat pengantar <small>PDF/JPG/PNG, maksimal 2 MB</small></div>
                    </li>
                </ul>

                @if ($googleFormUrl)
                    <div class="aside-gform">
                        <p>Lebih suka Google Form?</p>
                        <a href="{{ $googleFormUrl }}" target="_blank" rel="noopener noreferrer" class="btn">
                            Buka Google Form ↗
                        </a>
                    </div>
                @endif
            </aside>

            {{-- KARTU FORM --}}
            <div class="card register-card" data-reveal style="--delay: .1s">

                @if ($berhasil)

                    {{-- SUKSES --}}
                    <div class="success-state" role="status">
                        <div class="success-check">
                            <svg viewBox="0 0 52 52" aria-hidden="true"><path d="M14 27l8 8 16-17"/></svg>
                        </div>

                        <h3>Pendaftaran Terkirim!</h3>
                        <p>
                            Terima kasih, <strong>{{ $berhasil['nama'] }}</strong>. Pengajuan magang kamu sudah kami terima
                            dan akan segera diverifikasi oleh admin.
                        </p>

                        <div class="ticket">
                            <small>Nomor Pengajuan</small>
                            <strong>{{ $berhasil['nomor'] }}</strong>
                        </div>

                        <p>Simpan nomor ini. Cek status pengajuan kapan saja dengan NIM dan email <strong>{{ $berhasil['email'] }}</strong>.</p>

                        <div class="hero-actions" style="justify-content: center;">
                            <a href="#status" class="btn btn-primary">Cek Status Pengajuan</a>
                            <a href="{{ route('home') }}#daftar" class="btn btn-outline">Kirim Pengajuan Lain</a>
                        </div>
                    </div>

                @else

                    @if ($googleFormUrl)
                        <div class="method-tabs" role="tablist" aria-label="Pilih cara mendaftar">
                            <button type="button" class="method-tab" role="tab" id="tab-website" aria-controls="panel-website" aria-selected="true">
                                Form Website
                            </button>
                            <button type="button" class="method-tab" role="tab" id="tab-gform" aria-controls="panel-gform" aria-selected="false">
                                Google Form
                            </button>
                        </div>
                    @endif

                    {{-- PANEL: FORM WEBSITE --}}
                    <div class="method-panel" id="panel-website" role="tabpanel" aria-labelledby="tab-website">

                        @if ($adaErrorForm)
                            <div class="alert alert-error" role="alert">
                                <span>⚠</span>
                                <div>
                                    <strong>Ada data yang perlu diperbaiki.</strong>
                                    Periksa kolom yang ditandai merah.
                                    @if (! $errors->has('dokumen') && old('nim'))
                                        Surat pengantar perlu diunggah ulang.
                                    @endif
                                </div>
                            </div>
                        @endif

                        <ol class="stepper" aria-label="Langkah pendaftaran">
                            <li data-step-indicator="1"><span class="bullet">1</span><span class="label">Data Diri</span></li>
                            <li data-step-indicator="2"><span class="bullet">2</span><span class="label">Akademik</span></li>
                            <li data-step-indicator="3"><span class="bullet">3</span><span class="label">Rencana Magang</span></li>
                        </ol>

                        <form
                            id="form-pendaftaran"
                            method="POST"
                            action="{{ route('pengajuan.store') }}"
                            enctype="multipart/form-data"
                            data-start-step="{{ $langkahAwal }}"
                            novalidate
                        >
                            @csrf

                            {{-- LANGKAH 1 --}}
                            <fieldset class="form-step" data-step="1">
                                <legend class="sr-only">Data Diri</legend>
                                <h3>Data Diri</h3>
                                <p>Gunakan data asli sesuai kartu mahasiswa.</p>

                                <div class="field-grid">
                                    <x-website.field name="nama" label="Nama Lengkap" required full autocomplete="name" maxlength="255" placeholder="Contoh: Siti Rahmawati" />
                                    <x-website.field name="nim" label="NIM" required maxlength="30" placeholder="Contoh: 2201234567" pattern="[A-Za-z0-9.\-]+" />
                                    <x-website.field name="no_hp" type="tel" label="No. HP / WhatsApp" required autocomplete="tel" maxlength="20" placeholder="08xxxxxxxxxx" pattern="[0-9+\-\s]{8,20}" />
                                    <x-website.field name="email" type="email" label="Email Aktif" required full autocomplete="email" maxlength="255" placeholder="nama@email.com" hint="Dipakai untuk cek status pengajuan." />
                                </div>
                            </fieldset>

                            {{-- LANGKAH 2 --}}
                            <fieldset class="form-step" data-step="2" hidden>
                                <legend class="sr-only">Data Akademik</legend>
                                <h3>Data Akademik</h3>
                                <p>Informasi kampus asal kamu.</p>

                                <div class="field-grid">
                                    <x-website.field name="asal_kampus" label="Asal Kampus" required full maxlength="255" placeholder="Contoh: Universitas Negeri Jakarta" />
                                    <x-website.field name="program_studi" label="Program Studi" required maxlength="255" placeholder="Contoh: Sistem Informasi" />
                                    <x-website.field name="fakultas" label="Fakultas" maxlength="255" placeholder="Opsional" />
                                </div>
                            </fieldset>

                            {{-- LANGKAH 3 --}}
                            <fieldset class="form-step" data-step="3" hidden>
                                <legend class="sr-only">Rencana Magang</legend>
                                <h3>Rencana Magang</h3>
                                <p>Ceritakan bidang yang kamu minati dan periode magang.</p>

                                <div class="field-grid">
                                    <x-website.field name="posisi" label="Bidang / Posisi yang Diminati" required full maxlength="255" placeholder="Contoh: Web Developer, Administrasi, Desain Grafis" />

                                    <x-website.field name="tanggal_mulai" type="date" label="Tanggal Mulai" required :min="now()->toDateString()" />
                                    <x-website.field name="tanggal_selesai" type="date" label="Tanggal Selesai" required :min="now()->addDay()->toDateString()" />

                                    <div class="field full" style="margin-top: -8px;">
                                        <span class="duration-pill" id="durasi" hidden>⏱ <span></span></span>
                                    </div>

                                    <x-website.field name="keterangan" type="textarea" label="Motivasi / Keterangan" full maxlength="1000" placeholder="Ceritakan singkat alasan kamu ingin magang di sini (opsional)">
                                        <div class="field-meta">
                                            <span class="field-hint">Opsional, maksimal 1000 karakter.</span>
                                            <span class="field-hint" id="keterangan-counter">0/1000</span>
                                        </div>
                                    </x-website.field>

                                    <div @class(['field', 'full', 'has-error' => $errors->has('dokumen')])>
                                        <label for="dokumen">Surat Pengantar <span class="req" aria-hidden="true">*</span></label>

                                        <div class="dropzone" id="dropzone">
                                            <input
                                                type="file"
                                                id="dokumen"
                                                name="dokumen"
                                                accept=".pdf,.jpg,.jpeg,.png"
                                                required
                                                @error('dokumen') aria-invalid="true" aria-describedby="dokumen-error" @enderror
                                            >
                                            <span class="ico" aria-hidden="true">📎</span>
                                            <div>
                                                <strong id="dropzone-title">Klik atau seret file ke sini</strong>
                                                <small id="dropzone-sub">PDF, JPG, atau PNG · maksimal 2 MB</small>
                                            </div>
                                        </div>

                                        @error('dokumen')
                                            <div class="field-error" id="dokumen-error">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div @class(['field', 'full', 'has-error' => $errors->has('persetujuan')])>
                                        <label class="check-field">
                                            <input type="checkbox" name="persetujuan" value="1" required @checked(old('persetujuan'))>
                                            <span>Saya menyatakan data yang saya isi benar dan dapat dipertanggungjawabkan.</span>
                                        </label>

                                        @error('persetujuan')
                                            <div class="field-error">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </fieldset>

                            <div class="form-nav">
                                <span class="step-info" id="step-info">Langkah 1 dari 3</span>

                                <div class="btns">
                                    <button type="button" class="btn btn-outline" id="btn-prev" hidden>← Kembali</button>
                                    <button type="button" class="btn btn-primary" id="btn-next">Lanjut <span class="arrow">→</span></button>
                                    <button type="submit" class="btn btn-accent" id="btn-submit" hidden>Kirim Pendaftaran</button>
                                </div>
                            </div>
                        </form>

                    </div>

                    {{-- PANEL: GOOGLE FORM --}}
                    @if ($googleFormUrl)
                        <div class="method-panel" id="panel-gform" role="tabpanel" aria-labelledby="tab-gform" hidden>
                            <div class="gform-panel">
                                <div class="icon-box">📝</div>
                                <h3>Daftar lewat Google Form</h3>
                                <p>
                                    Form akan terbuka di tab baru. Pendaftaran lewat Google Form diproses manual oleh admin,
                                    jadi statusnya belum bisa dicek otomatis di website ini.
                                </p>
                                <a href="{{ $googleFormUrl }}" target="_blank" rel="noopener noreferrer" class="btn btn-accent">
                                    Buka Google Form <span class="arrow">↗</span>
                                </a>
                            </div>
                        </div>
                    @endif

                @endif

            </div>
        </div>

    </div>
</section>
