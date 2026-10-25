<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\BukuPanduan;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Menu Panduan: buku panduan sesuai role yang login dan buku alur magang.
 */
class PanduanController extends Controller
{
    public function show(Request $request, BukuPanduan $bukuPanduan, ?string $bagian = null): View
    {
        $bagianBoleh = $bukuPanduan->bagianUntuk($request->user());
        $bagian ??= $bagianBoleh[0];

        // Bagian untuk role lain tidak ditampilkan.
        abort_unless(in_array($bagian, $bagianBoleh, true), 404);

        return view('panduan.index', [
            'bagian' => $bagian,
            'tab' => collect($bagianBoleh)->mapWithKeys(fn (string $slug) => [$slug => BukuPanduan::BAGIAN[$slug][0]]),
            ...$bukuPanduan->render($bagian, $bagianBoleh),
        ]);
    }
}
