<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Str;
use League\CommonMark\GithubFlavoredMarkdownConverter;

/**
 * Buku panduan di dalam aplikasi. Sumbernya file Markdown di docs/panduan
 * (sama dengan yang dibaca di GitHub), dirender menjadi HTML untuk menu Panduan.
 */
class BukuPanduan
{
    /**
     * Bagian panduan: slug => [judul, file Markdown].
     *
     * @var array<string, array{string, string}>
     */
    public const BAGIAN = [
        'peserta' => ['Panduan Peserta', 'panduan-peserta.md'],
        'admin' => ['Panduan Admin', 'panduan-admin.md'],
        'super-admin' => ['Panduan Super Admin', 'panduan-super-admin.md'],
        'alur' => ['Buku Alur Magang', 'alur-magang.md'],
    ];

    /**
     * Bagian yang boleh dibaca role tertentu, berurutan (yang pertama dibuka bawaan).
     *
     * @return array<int, string>
     */
    public function bagianUntuk(User $user): array
    {
        return match (true) {
            $user->isPeserta() => ['peserta', 'alur'],
            $user->isSuperAdmin() => ['super-admin', 'admin', 'alur'],
            default => ['admin', 'alur'],
        };
    }

    /**
     * HTML satu bagian beserta daftar isinya (judul tingkat 2).
     *
     * @param  array<int, string>  $bagianBoleh  bagian yang boleh ditautkan dari isi panduan
     * @return array{html: string, daftarIsi: array<int, array{id: string, judul: string}>}
     */
    public function render(string $bagian, array $bagianBoleh): array
    {
        $markdown = (string) file_get_contents(base_path('docs/panduan/'.self::BAGIAN[$bagian][1]));

        $html = (new GithubFlavoredMarkdownConverter([
            'html_input' => 'strip',
            'allow_unsafe_links' => false,
        ]))->convert($markdown)->getContent();

        $html = $this->gambar($html);
        $html = $this->tautanPanduan($html, $bagianBoleh);
        $html = $this->diagram($html);

        $daftarIsi = [];
        $html = preg_replace_callback('#<h2>(.*?)</h2>#s', function (array $m) use (&$daftarIsi) {
            $judul = trim(strip_tags($m[1]));
            $id = Str::slug($judul);
            $daftarIsi[] = ['id' => $id, 'judul' => $judul];

            return '<h2 id="'.$id.'">'.$m[1].'</h2>';
        }, $html);

        return ['html' => $html, 'daftarIsi' => $daftarIsi];
    }

    /**
     * Gambar: path relatif dari docs/panduan diarahkan ke public/, dimuat malas, dan bisa diklik.
     */
    private function gambar(string $html): string
    {
        return preg_replace_callback(
            '#<img src="\.\./\.\./public/images/panduan/([A-Za-z0-9._-]+)" alt="([^"]*)"\s*/?>#',
            function (array $m) {
                $url = e(asset('images/panduan/'.$m[1]));

                return '<a href="'.$url.'" target="_blank" rel="noopener" class="gambar-panduan">'
                    .'<img src="'.$url.'" alt="'.$m[2].'" loading="lazy">'
                    .'<span>'.$m[2].'</span></a>';
            },
            $html
        );
    }

    /**
     * Tautan ke file panduan lain menjadi tautan menu Panduan; bagian yang tidak boleh dibuka
     * role ini cukup ditampilkan sebagai teks.
     *
     * @param  array<int, string>  $bagianBoleh
     */
    private function tautanPanduan(string $html, array $bagianBoleh): string
    {
        $fileKeBagian = collect(self::BAGIAN)->mapWithKeys(fn (array $data, string $slug) => [$data[1] => $slug]);

        return preg_replace_callback('#<a href="([a-z-]+\.md)">(.*?)</a>#s', function (array $m) use ($fileKeBagian, $bagianBoleh) {
            $bagian = $fileKeBagian[$m[1]] ?? null;

            return $bagian && in_array($bagian, $bagianBoleh, true)
                ? '<a href="'.e(route('panduan', $bagian)).'">'.$m[2].'</a>'
                : $m[2];
        }, $html);
    }

    /**
     * Diagram Mermaid (untuk GitHub) diganti diagram HTML yang setara.
     */
    private function diagram(string $html): string
    {
        return preg_replace(
            '#<pre><code class="language-mermaid">.*?</code></pre>#s',
            view('panduan.partials.diagram-alur')->render(),
            $html
        );
    }
}
