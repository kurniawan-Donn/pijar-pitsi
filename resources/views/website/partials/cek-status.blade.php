{{-- Bagian cek status pengajuan (kode pengajuan + email). --}}
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
                <p>Masukkan kode pengajuan (contoh MG-000012) dan email yang dipakai saat mendaftar untuk melihat status terbaru serta catatan dari admin.</p>

                <div class="status-legend" aria-label="Keterangan status">
                    <span class="status-pill status-menunggu">Menunggu</span>
                    <span class="status-pill status-disetujui">Disetujui</span>
                    <span class="status-pill status-ditolak">Ditolak</span>
                </div>
            </div>

            <div>
                <form method="POST" action="{{ route('pengajuan.status') }}" class="status-form" id="form-status">
                    @csrf

                    <x-website.field name="status_kode" label="Kode Pengajuan" required maxlength="20" placeholder="Contoh: MG-000012" autocomplete="off" />
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

                                @if (! empty($hasil['dokumen_url']))
                                    <p class="status-note">
                                        <a href="{{ $hasil['dokumen_url'] }}" target="_blank" rel="noopener" class="text-link">Lihat surat pengantar <span>→</span></a>
                                        <small>(tautan berlaku {{ \App\Http\Controllers\Website\PendaftaranController::MASA_BERLAKU_DOKUMEN }} menit)</small>
                                    </p>
                                @endif

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
