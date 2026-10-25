{{--
    Navigasi halaman untuk hasil paginate().

    Contoh:
    <x-pagination :paginator="$pengajuans" />
--}}
@props([
    'paginator',
])

<div {{ $attributes->class(['x-pagination']) }}>

    <div class="x-pagination-info">
        @if ($paginator->total())
            Menampilkan {{ $paginator->firstItem() }}–{{ $paginator->lastItem() }}
            dari {{ $paginator->total() }} data
        @else
            Tidak ada data
        @endif
    </div>

    @if ($paginator->hasPages())
        <nav class="x-pagination-links" aria-label="Navigasi halaman">

            @if ($paginator->onFirstPage())
                <span aria-disabled="true"><x-icon name="chevron-left" :size="16" /></span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="Sebelumnya"><x-icon name="chevron-left" :size="16" /></a>
            @endif

            @foreach ($paginator->getUrlRange(max(1, $paginator->currentPage() - 2), min($paginator->lastPage(), $paginator->currentPage() + 2)) as $page => $url)
                @if ($page === $paginator->currentPage())
                    <span aria-current="page">{{ $page }}</span>
                @else
                    <a href="{{ $url }}">{{ $page }}</a>
                @endif
            @endforeach

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="Berikutnya"><x-icon name="chevron-right" :size="16" /></a>
            @else
                <span aria-disabled="true"><x-icon name="chevron-right" :size="16" /></span>
            @endif

        </nav>
    @endif

</div>
