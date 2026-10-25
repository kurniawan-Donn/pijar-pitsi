"""Ambil ulang screenshot buku panduan (public/images/panduan) dari server demo.

Langkah (lihat docs/panduan/README.md):
1. Buat database demo:  DB_DATABASE=database/panduan.sqlite PANDUAN_PASSWORD=... php artisan migrate:fresh --seeder=PanduanDemoSeeder
2. Jalankan server demo dari folder public dengan DB_DATABASE yang sama di port 8020:
   php -S 127.0.0.1:8020 ../vendor/laravel/framework/src/Illuminate/Foundation/resources/server.php
3. Set env PANDUAN_ADMIN_PASSWORD, PANDUAN_SUPERADMIN_PASSWORD (dari UserSeeder) dan PANDUAN_PASSWORD,
   lalu:  python ambil_screenshot.py <kode_verifikasi_surat_demo> [nama-gambar ...]
Butuh Python 3 + paket websockets, dan Google Chrome.
Setelah selesai, hapus database demo dan file foto demo di storage.
"""
import asyncio
import base64
import json
import os
import shutil
import subprocess
import sys
import time
import urllib.request

import websockets

sys.stdout.reconfigure(encoding='utf-8')

BASIS = 'http://127.0.0.1:8020'
PROYEK = os.path.abspath(os.path.join(os.path.dirname(__file__), '..', '..', '..'))
KELUAR = os.path.join(PROYEK, 'public', 'images', 'panduan')
PROFIL = os.path.join(os.environ.get('TEMP', '/tmp'), 'chrome-profil-panduan')
CHROME = os.environ.get('CHROME', r'C:\Program Files\Google\Chrome\Application\chrome.exe')
LEBAR, TINGGI = 1280, 800

# Akun di database demo (UserSeeder + PanduanDemoSeeder); password dari environment variable.
AKUN = {
    'admin': ('admin@magenta.local', os.environ.get('PANDUAN_ADMIN_PASSWORD', '')),
    'super_admin': ('superadmin@magenta.local', os.environ.get('PANDUAN_SUPERADMIN_PASSWORD', '')),
    'peserta': ('rina@contoh.test', os.environ.get('PANDUAN_PASSWORD', '')),
    # Peserta yang sudah selesai dan suratnya terbit (langkah unduh surat).
    'peserta_selesai': ('bayu@contoh.test', os.environ.get('PANDUAN_PASSWORD', '')),
}

# Halaman error tidak boleh ikut dipotret.
JS_CEK_ERROR = """
(() => {
    const teks = document.body ? document.body.innerText.slice(0, 400) : '';
    return /Internal Server Error|TypeError|ErrorException|QueryException|Not Found|Forbidden|Page Expired/.test(teks)
        || /^(403|404|419|500)\b/.test(document.title);
})()
"""

# Tampilkan semua animasi masuk; matikan scroll halus.
JS_SIAP = """
document.documentElement.style.scrollBehavior = 'auto';
document.querySelectorAll('[data-reveal]').forEach(e => e.classList.add('is-visible', 'revealed', 'show', 'in-view'));
document.querySelectorAll('[data-reveal]').forEach(e => { e.style.opacity = 1; e.style.transform = 'none'; });
"""

# (nama file, role atau None, path, js tambahan, tinggi maksimum)
DAFTAR = [
    ('beranda', None, '/', '', 800),
    ('pendaftaran', None, '/', "document.querySelector('#daftar').scrollIntoView();", 800),
    ('cek-status', None, '/', "document.querySelector('#status').scrollIntoView();", 800),
    ('galeri-dokumentasi', None, '/', "document.querySelector('#dokumentasi').scrollIntoView(); document.querySelector('.galeri-item').classList.add('terbuka');", 800),
    ('login', None, '/login', '', 800),
    ('verifikasi-surat', None, '/verifikasi-surat/{kode}', '', 700),

    ('peserta-dashboard', 'peserta', '/peserta/dashboard', '', 1300),
    ('peserta-biodata', 'peserta', '/peserta/biodata', '', 1100),
    ('peserta-logbook', 'peserta', '/peserta/logbook', '', 1300),
    ('peserta-logbook-isi', 'peserta', '/peserta/logbook/2026-10-23', '', 1300),
    ('peserta-izin', 'peserta', '/peserta/izin/ajukan', '', 1000),
    ('peserta-rekap', 'peserta', '/peserta/rekap', '', 1100),
    ('peserta-feedback', 'peserta', '/peserta/feedback', '', 1100),
    ('profil', 'peserta', '/profil', '', 1000),

    ('peserta-surat', 'peserta_selesai', '/peserta/surat-keterangan', '', 800),
    # Halaman pertama PDF surat (diunduh dulu, lalu dibuka di penampil PDF Chrome).
    ('contoh-surat-keterangan', 'peserta_selesai', 'pdf:/peserta/surat-keterangan/unduh', '', 1100),

    ('admin-dashboard', 'admin', '/admin/dashboard', '', 1000),
    ('admin-pengajuan', 'admin', '/admin/pengajuan', '', 900),
    ('admin-pengajuan-detail', 'admin', '/admin/pengajuan/1', '', 1300),
    ('admin-peserta-detail', 'admin', '/superadmin/peserta-magang/1', '', 1500),
    ('admin-akun-peserta', 'admin', '/superadmin/peserta-magang/1', "document.querySelector('#akun').scrollIntoView();", 800),
    ('admin-biodata', 'admin', '/superadmin/peserta-magang/1/biodata', '', 1200),
    ('admin-logbook', 'admin', '/superadmin/peserta-magang/1/logbook-harian', '', 1300),
    ('admin-monitoring', 'admin', '/superadmin/monitoring', '', 1000),
    ('admin-penilaian', 'admin', '/superadmin/peserta-magang/2/penilaian', '', 1400),
    ('admin-surat', 'admin', '/superadmin/peserta-magang/2/surat-keterangan', '', 1100),
    ('admin-alumni', 'admin', '/superadmin/alumni', '', 900),
    ('admin-dokumentasi', 'admin', '/superadmin/dokumentasi', '', 900),
    ('admin-laporan', 'admin', '/superadmin/laporan', '', 1000),
    ('admin-hari-libur', 'admin', '/superadmin/pengaturan-magang', "document.querySelector('#libur').scrollIntoView();", 800),

    ('sa-dashboard', 'super_admin', '/superadmin/dashboard', '', 1000),
    ('sa-pengaturan', 'super_admin', '/superadmin/pengaturan-magang', '', 1400),
    ('sa-pengguna', 'super_admin', '/superadmin/pengguna', '', 800),
    ('sa-aspek', 'super_admin', '/superadmin/aspek-penilaian', '', 1100),
    ('sa-log', 'super_admin', '/superadmin/log-aktivitas', '', 900),
]


class Cdp:
    def __init__(self, ws):
        self.ws = ws
        self.n = 0
        self.events = []

    async def call(self, method, **params):
        self.n += 1
        nomor = self.n
        await self.ws.send(json.dumps({'id': nomor, 'method': method, 'params': params}))
        while True:
            pesan = json.loads(await self.ws.recv())
            if pesan.get('id') == nomor:
                if 'error' in pesan:
                    raise RuntimeError(f"{method}: {pesan['error']}")
                return pesan.get('result', {})
            self.events.append(pesan)

    async def tunggu_muat(self, batas=15):
        mulai = time.time()
        while time.time() - mulai < batas:
            try:
                pesan = json.loads(await asyncio.wait_for(self.ws.recv(), 1))
            except asyncio.TimeoutError:
                continue
            if pesan.get('method') == 'Page.loadEventFired':
                return
        print('  (batas waktu muat)')

    async def buka(self, url):
        self.events.clear()
        await self.call('Page.navigate', url=url)
        await self.tunggu_muat()
        await asyncio.sleep(0.8)

    async def js(self, kode):
        hasil = await self.call('Runtime.evaluate', expression=kode, awaitPromise=True, returnByValue=True)
        return hasil.get('result', {}).get('value')


async def login(cdp, role):
    await cdp.call('Network.clearBrowserCookies')
    await cdp.buka(BASIS + '/login')
    email, pw = AKUN[role]
    await cdp.js(f"""
        document.querySelector('#email').value = {json.dumps(email)};
        document.querySelector('#password').value = {json.dumps(pw)};
        document.querySelector('form').submit();
    """)
    await cdp.tunggu_muat()
    await asyncio.sleep(0.8)
    lokasi = await cdp.js('location.pathname')
    print(f'login {role} -> {lokasi}')
    if lokasi.startswith('/login'):
        raise SystemExit('login gagal: ' + role)


async def cek_error(cdp, nama):
    if await cdp.js(JS_CEK_ERROR):
        raise SystemExit(f'Halaman untuk {nama} berisi error; screenshot dihentikan.')


async def potret_pdf(cdp, nama, path, tinggi):
    """Unduh PDF memakai sesi login yang aktif, lalu potret halaman pertamanya."""
    isi = await cdp.js(f"""
        fetch({json.dumps(BASIS + path)}, {{credentials: 'same-origin'}})
            .then(r => r.ok && r.headers.get('content-type').includes('pdf') ? r.arrayBuffer() : Promise.reject(r.status))
            .then(b => {{ let s = ''; new Uint8Array(b).forEach(x => s += String.fromCharCode(x)); return btoa(s); }})
    """)
    if not isi:
        raise SystemExit(f'PDF untuk {nama} gagal diunduh.')
    berkas = os.path.join(os.environ.get('TEMP', '/tmp'), nama + '.pdf')
    with open(berkas, 'wb') as f:
        f.write(base64.b64decode(isi))
    await cdp.call('Emulation.setDeviceMetricsOverride', width=LEBAR, height=tinggi, deviceScaleFactor=1, mobile=False)
    await cdp.buka('file:///' + berkas.replace(os.sep, '/') + '#toolbar=0&navpanes=0&view=FitH')
    await asyncio.sleep(2.5)
    hasil = await cdp.call('Page.captureScreenshot', format='jpeg', quality=82)
    await cdp.call('Emulation.setDeviceMetricsOverride', width=LEBAR, height=TINGGI, deviceScaleFactor=1, mobile=False)
    with open(os.path.join(KELUAR, nama + '.jpg'), 'wb') as f:
        f.write(base64.b64decode(hasil['data']))
    os.remove(berkas)
    print(f'  {nama}.jpg (PDF) {LEBAR}x{tinggi}')


async def potret(cdp, nama, js_tambahan, tinggi_maks):
    await cek_error(cdp, nama)
    await cdp.js(JS_SIAP)
    if js_tambahan:
        await cdp.js(js_tambahan)
    await asyncio.sleep(0.6)
    tinggi_dok = await cdp.js('Math.max(document.documentElement.scrollHeight, document.body.scrollHeight)')
    scroll_y = await cdp.js('window.scrollY')
    tinggi = min(tinggi_maks, max(TINGGI, tinggi_dok - scroll_y))
    # Layar setinggi gambar agar sidebar (tinggi 100vh) ikut penuh sampai bawah.
    await cdp.call('Emulation.setDeviceMetricsOverride', width=LEBAR, height=int(tinggi), deviceScaleFactor=1, mobile=False)
    await asyncio.sleep(0.4)
    await cdp.js(f'window.scrollTo(0, {scroll_y})')
    await asyncio.sleep(0.3)
    hasil = await cdp.call('Page.captureScreenshot', format='jpeg', quality=82)
    await cdp.call('Emulation.setDeviceMetricsOverride', width=LEBAR, height=TINGGI, deviceScaleFactor=1, mobile=False)
    path = os.path.join(KELUAR, nama + '.jpg')
    with open(path, 'wb') as f:
        f.write(base64.b64decode(hasil['data']))
    print(f'  {nama}.jpg {LEBAR}x{tinggi} {os.path.getsize(path) // 1024} KB')


async def main(kode_surat, hanya):
    os.makedirs(KELUAR, exist_ok=True)
    shutil.rmtree(PROFIL, ignore_errors=True)
    proses = subprocess.Popen([CHROME, '--headless=new', '--remote-debugging-port=9333', f'--user-data-dir={PROFIL}',
                               f'--window-size={LEBAR},{TINGGI}', '--hide-scrollbars', '--no-first-run',
                               '--no-default-browser-check', '--disable-extensions', 'about:blank'])
    try:
        for _ in range(40):
            try:
                target = [t for t in json.load(urllib.request.urlopen('http://127.0.0.1:9333/json')) if t['type'] == 'page'][0]
                break
            except Exception:
                time.sleep(0.25)
        async with websockets.connect(target['webSocketDebuggerUrl'], max_size=50_000_000) as ws:
            cdp = Cdp(ws)
            await cdp.call('Page.enable')
            await cdp.call('Network.enable')
            await cdp.call('Emulation.setDeviceMetricsOverride', width=LEBAR, height=TINGGI, deviceScaleFactor=1, mobile=False)
            await cdp.call('Emulation.setEmulatedMedia', features=[{'name': 'prefers-reduced-motion', 'value': 'reduce'}])

            role_aktif = 'belum'
            for nama, role, path, js_tambahan, tinggi_maks in DAFTAR:
                if hanya and nama not in hanya:
                    continue
                if role != role_aktif:
                    if role is None:
                        await cdp.call('Network.clearBrowserCookies')
                    else:
                        await login(cdp, role)
                    role_aktif = role
                if path.startswith('pdf:'):
                    await cdp.buka(BASIS + '/login')
                    await potret_pdf(cdp, nama, path[4:], tinggi_maks)
                    continue
                await cdp.buka(BASIS + path.replace('{kode}', kode_surat))
                await potret(cdp, nama, js_tambahan, tinggi_maks)
    finally:
        proses.terminate()
        time.sleep(1)
        shutil.rmtree(PROFIL, ignore_errors=True)


if __name__ == '__main__':
    asyncio.run(main(sys.argv[1], set(sys.argv[2:])))
