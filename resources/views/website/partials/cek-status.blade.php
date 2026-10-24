{{-- Bagian cek status pengajuan (NIM + email). --}}
@php
    $hasilStatus = session('status_pengajuan');
    $tidakDitemukan = session('status_tidak_ditemukan');
@endphp

<section id="status" class="section section-light">
    <div class="container">

        <div class="card status-box" data-reveal>

            <div class="status-intro">
                <span class="section-label">Cek Status</span>
                <h2>Pantau pengajuan magangmu</h2>
                <p>Masukkan NIM dan email yang dipakai saat mendaftar lewat form website untuk melihat status terbaru serta catatan dari admin.</p>

                <div class="status-legend" aria-label="Keterangan status">
                    <span class="status-pill status-menunggu">Menunggu</span>
                    <span class="status-pill status-disetujui">Disetujui</span>
                    <span class="status-pill status-ditolak">Ditolak</span>
                </div>
            </div>

            <div>
                <form method="POST" action="{{ route('pengajuan.status') }}" class="status-form" id="form-status">
                    @csrf

                    <x-website.field name="status_nim" label="NIM" required maxlength="30" placeholder="NIM saat mendaftar" />
                    <x-website.field name="status_email" type="email" label="Email" required maxlength="255" placeholder="Email saat mendaftar" autocomplete="email" />

                    <button type="submit" class="btn btn-primary btn-block">
                        Cek Status <span class="arrow">→</span>
                    </button>
                </form>

                @if ($tidakDitemukan)
                    <div class="alert alert-info" role="status" style="margin-top: 20px; margin-bottom: 0;">
                        <span>ℹ</span>
                        <div>{{ $tidakDitemukan }}</div>
                    </div>
                @endif

                @if ($hasilStatus)
                    <div class="status-results" role="status" aria-live="polite">
                        @foreach ($hasilStatus as $hasil)
                            <article class="status-result">
                                <div class="status-result-head">
                                    <strong>{{ $hasil['nomor'] }}</strong>
                                    <span class="status-pill status-{{ $hasil['status'] }}">{{ $hasil['status_label'] }}</span>
                                </div>

                                <dl>
                                    <div>
                                        <dt>Bidang</dt>
                                        <dd>{{ $hasil['posisi'] }}</dd>
                                    </div>
                                    <div>
                                        <dt>Periode</dt>
                                        <dd>{{ $hasil['periode'] }}</dd>
                                    </div>
                                    <div>
                                        <dt>Diajukan</dt>
                                        <dd>{{ $hasil['diajukan'] }}</dd>
                                    </div>
                                </dl>

                                @if ($hasil['catatan'])
                                    <div class="status-note"><strong>Catatan admin:</strong> {{ $hasil['catatan'] }}</div>
                                @elseif ($hasil['status'] === 'menunggu')
                                    <div class="status-note">Pengajuan sedang diverifikasi. Silakan cek kembali secara berkala.</div>
                                @endif
                            </article>
                        @endforeach
                    </div>
                @endif
            </div>

        </div>

    </div>
</section>
