@extends('layouts.app')

@section('content')
<header class="page-head">
    <div>
        <p class="eyebrow">Laporan</p>
        <h1>Inventaris dan Pergerakan Stok</h1>
    </div>
    <a class="button primary" href="{{ route('reports.print', request()->query()) }}">Cetak</a>
</header>

<section class="panel">
    <form class="filters" method="get">
        <input name="from" type="date" value="{{ $filters['from'] ?? '' }}">
        <input name="to" type="date" value="{{ $filters['to'] ?? '' }}">
        <input name="category" value="{{ $filters['category'] ?? '' }}" placeholder="Kategori">
        <input name="location" value="{{ $filters['location'] ?? '' }}" placeholder="Lokasi">
        <button class="button" type="submit">Terapkan</button>
    </form>
</section>

<section class="stats">
    <article><span>Total barang</span><strong>{{ $data['summary']['items'] ?? 0 }}</strong></article>
    <article><span>Total stok</span><strong>{{ $data['summary']['stock'] ?? 0 }}</strong></article>
    <article><span>Barang masuk</span><strong>{{ $data['summary']['in'] ?? 0 }}</strong></article>
    <article><span>Barang keluar</span><strong>{{ $data['summary']['out'] ?? 0 }}</strong></article>
</section>

<section class="panel">
    <h2>Pergerakan</h2>
    <div class="table-wrap">
        <table>
            <thead><tr><th>Tanggal</th><th>Barang</th><th>Tipe</th><th>Jumlah</th><th>Petugas</th><th>Referensi</th></tr></thead>
            <tbody>
            @forelse (($data['movements'] ?? []) as $movement)
                <tr>
                    <td>{{ \Illuminate\Support\Str::of($movement['created_at'])->substr(0, 10) }}</td>
                    <td>{{ $movement['item']['name'] ?? '-' }}</td>
                    <td>{{ $movement['type'] }}</td>
                    <td>{{ $movement['quantity'] }}</td>
                    <td>{{ $movement['actor'] }}</td>
                    <td>{{ $movement['reference'] ?? '-' }}</td>
                </tr>
            @empty
                <tr><td colspan="6" class="empty">Tidak ada data pada filter ini.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination Control --}}
    @if ($data['movements']->count() > 0 || $data['movements']->total() > 0)
        <div class="pagination-container">
            <div class="pagination-info">
                Menampilkan <strong>{{ $data['movements']->firstItem() ?? 0 }}</strong> - <strong>{{ $data['movements']->lastItem() ?? 0 }}</strong> dari total <strong>{{ $data['movements']->total() }}</strong> pergerakan stok.
            </div>
            <div class="pagination-nav">
                <span class="page-counter">Halaman {{ $data['movements']->currentPage() }} / {{ $data['movements']->lastPage() }}</span>
                <div class="pagination-buttons">
                    @if ($data['movements']->onFirstPage())
                        <span class="button disabled" title="Halaman Sebelumnya">
                            <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2.5" fill="none" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"></polyline></svg>
                        </span>
                    @else
                        <a href="{{ $data['movements']->appends(request()->query())->previousPageUrl() }}" class="button" title="Halaman Sebelumnya">
                            <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2.5" fill="none" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"></polyline></svg>
                        </a>
                    @endif
                    @if ($data['movements']->hasMorePages())
                        <a href="{{ $data['movements']->appends(request()->query())->nextPageUrl() }}" class="button" title="Halaman Berikutnya">
                            <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2.5" fill="none" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"></polyline></svg>
                        </a>
                    @else
                        <span class="button disabled" title="Halaman Berikutnya">
                            <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2.5" fill="none" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"></polyline></svg>
                        </span>
                    @endif
                </div>
            </div>
        </div>
    @endif
</section>
@endsection
