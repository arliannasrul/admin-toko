@extends('layouts.app')

@section('content')
<header class="page-head">
    <div>
        <p class="eyebrow">Pusat notifikasi</p>
        <h1>Notifikasi dan Tindak Lanjut</h1>
    </div>
</header>

<section class="panel">
    <ul class="activity notifications">
        @forelse (($data['notifications'] ?? []) as $notification)
            <li @class(['unread' => empty($notification['read_at'])])>
                <strong>{{ $notification['title'] }}</strong>
                <span>{{ $notification['body'] }}</span>
                @if (empty($notification['read_at']))
                    <form method="post" action="{{ route('notifications.read', $notification['id']) }}">
                        @csrf
                        <button class="button" type="submit">Tandai dibaca</button>
                    </form>
                @endif
            </li>
        @empty
            <li class="empty">Tidak ada notifikasi.</li>
        @endforelse
    </ul>

    {{-- Pagination Control --}}
    @if ($data['notifications']->count() > 0 || $data['notifications']->total() > 0)
        <div class="pagination-container" style="border-bottom-left-radius: 10px; border-bottom-right-radius: 10px;">
            <div class="pagination-info">
                Menampilkan <strong>{{ $data['notifications']->firstItem() ?? 0 }}</strong> - <strong>{{ $data['notifications']->lastItem() ?? 0 }}</strong> dari total <strong>{{ $data['notifications']->total() }}</strong> notifikasi.
            </div>
            <div class="pagination-nav">
                <span class="page-counter">Halaman {{ $data['notifications']->currentPage() }} / {{ $data['notifications']->lastPage() }}</span>
                <div class="pagination-buttons">
                    @if ($data['notifications']->onFirstPage())
                        <span class="button disabled" title="Halaman Sebelumnya">
                            <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2.5" fill="none" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"></polyline></svg>
                        </span>
                    @else
                        <a href="{{ $data['notifications']->previousPageUrl() }}" class="button" title="Halaman Sebelumnya">
                            <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2.5" fill="none" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"></polyline></svg>
                        </a>
                    @endif
                    @if ($data['notifications']->hasMorePages())
                        <a href="{{ $data['notifications']->nextPageUrl() }}" class="button" title="Halaman Berikutnya">
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
