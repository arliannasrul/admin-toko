@extends('layouts.app')

@section('content')
<header class="page-head">
    <div>
        <p class="eyebrow">Pengiriman & Logistik</p>
        <h1>Daftar Order & Tracking</h1>
    </div>
</header>

<section class="stats">
    <article>
        <span>Semua Order</span>
        <strong>{{ $counts['all'] }}</strong>
    </article>
    <article style="border-color: var(--warn-border);">
        <span>Menunggu Proses</span>
        <strong style="color: var(--warn);">{{ $counts['pending'] }}</strong>
    </article>
    <article style="border-color: rgba(6, 182, 212, 0.2);">
        <span>Sedang Dikirim</span>
        <strong style="color: #22d3ee;">{{ $counts['shipping'] }}</strong>
    </article>
    <article style="border-color: var(--success-border);">
        <span>Selesai</span>
        <strong style="color: var(--success);">{{ $counts['delivered'] }}</strong>
    </article>
</section>

<div class="tabs">
    <a href="{{ route('orders.index', ['status' => 'all']) }}" class="tab-link {{ $status === 'all' ? 'active' : '' }}">Semua</a>
    <a href="{{ route('orders.index', ['status' => 'pending']) }}" class="tab-link {{ $status === 'pending' ? 'active' : '' }}">Menunggu Proses</a>
    <a href="{{ route('orders.index', ['status' => 'shipping']) }}" class="tab-link {{ $status === 'shipping' ? 'active' : '' }}">Dalam Pengiriman</a>
    <a href="{{ route('orders.index', ['status' => 'delivered']) }}" class="tab-link {{ $status === 'delivered' ? 'active' : '' }}">Selesai</a>
</div>

<section class="panel">
    <div class="panel-head">
        <h2>Daftar Transaksi Pelanggan</h2>
    </div>

    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Order ID / Tanggal</th>
                    <th>Pelanggan</th>
                    <th>Detail Barang</th>
                    <th>Pengiriman (Kiriminaja)</th>
                    <th>Status</th>
                    <th>Resi (AWB)</th>
                    <th style="text-align: right;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($orders as $order)
                    <tr>
                        <td>
                            <strong style="display: block; color: var(--accent);">{{ $order->order_number }}</strong>
                            <small style="color: var(--muted);">{{ $order->created_at->format('d M Y H:i') }}</small>
                        </td>
                        <td>
                            <strong>{{ $order->customer_name }}</strong>
                            <small style="display: block; color: var(--muted);">{{ $order->customer_phone }}</small>
                            <span style="display: block; font-size: 0.8rem; max-width: 200px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="{{ $order->customer_address }}">
                                {{ $order->customer_address }}
                            </span>
                        </td>
                        <td>
                            @foreach ($order->items as $item)
                                <div style="font-size: 0.9rem;">
                                    {{ $item->name }} <span style="color: var(--muted);">x{{ $item->pivot->quantity }}</span>
                                </div>
                            @endforeach
                        </td>
                        <td>
                            <div style="font-weight: bold; text-transform: uppercase;">{{ $order->courier }} - {{ $order->shipping_service }}</div>
                            <small style="color: var(--muted);">Ongkir: Rp {{ number_format($order->shipping_cost, 0, ',', '.') }}</small>
                        </td>
                        <td>
                            <span class="badge badge-{{ $order->status }}">
                                {{ $order->status === 'pending' ? 'Baru' : ($order->status === 'shipping' ? 'Dikirim' : ($order->status === 'delivered' ? 'Selesai' : $order->status)) }}
                            </span>
                        </td>
                        <td>
                            @if ($order->waybill)
                                <code style="background: rgba(255,255,255,0.06); padding: 4px 8px; border-radius: 4px; font-size: 0.85rem; color: #38bdf8;">{{ $order->waybill }}</code>
                            @else
                                <span style="color: var(--muted); font-size: 0.85rem; font-style: italic;">Belum diproses</span>
                            @endif
                        </td>
                        <td style="text-align: right;">
                            <div style="display: flex; gap: 6px; justify-content: flex-end;">
                                <a href="{{ route('orders.show', $order->id) }}" class="button" title="Detail & Label" style="padding: 6px 12px; min-height: auto;">
                                    👁️ Detail
                                </a>

                                @if ($order->status === 'pending')
                                    <form method="POST" action="{{ route('orders.process', $order->id) }}" style="margin: 0;">
                                        @csrf
                                        <button type="submit" class="button primary" style="padding: 6px 12px; min-height: auto;">
                                            🚀 Kirim (Resi)
                                        </button>
                                    </form>
                                @endif

                                @if ($order->status === 'shipping')
                                    <a href="{{ route('orders.tracking', $order->id) }}" class="button secondary" style="padding: 6px 12px; min-height: auto;">
                                        📍 Lacak
                                    </a>
                                    <form method="POST" action="{{ route('orders.complete', $order->id) }}" style="margin: 0;">
                                        @csrf
                                        <button type="submit" class="button success" style="padding: 6px 12px; min-height: auto;">
                                            ✅ Selesai
                                        </button>
                                    </form>
                                @endif

                                @if ($order->status === 'delivered' || $order->status === 'shipping')
                                    <a href="{{ route('crm.templates', ['order_id' => $order->id]) }}" class="button" style="padding: 6px 12px; min-height: auto; background: rgba(16, 185, 129, 0.15); color: #34d399; border-color: rgba(16, 185, 129, 0.3);">
                                        💬 Kirim WA
                                    </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="empty">Belum ada pesanan masuk dari e-commerce.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination Control --}}
    @if ($orders->hasPages())
        <div class="pagination-container" style="margin-top: 24px; display: flex; justify-content: space-between; align-items: center; padding: 12px 20px; background: rgba(255,255,255,0.02); border-top: 1px solid rgba(255,255,255,0.05); border-bottom-left-radius: 16px; border-bottom-right-radius: 16px;">
            <div style="font-size: 0.85rem; color: var(--muted);">
                Menampilkan <strong>{{ $orders->firstItem() }}</strong> - <strong>{{ $orders->lastItem() }}</strong> dari total <strong>{{ $orders->total() }}</strong> pesanan.
            </div>
            <div style="display: flex; gap: 8px;">
                @if ($orders->onFirstPage())
                    <span class="button disabled" style="opacity: 0.4; cursor: not-allowed; padding: 8px 12px; min-height: auto; display: inline-flex; align-items: center; justify-content: center;" title="Halaman Sebelumnya">
                        <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2.5" fill="none" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"></polyline></svg>
                    </span>
                @else
                    <a href="{{ $orders->previousPageUrl() }}" class="button" style="padding: 8px 12px; min-height: auto; display: inline-flex; align-items: center; justify-content: center;" title="Halaman Sebelumnya">
                        <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2.5" fill="none" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"></polyline></svg>
                    </a>
                @endif

                @if ($orders->hasMorePages())
                    <a href="{{ $orders->nextPageUrl() }}" class="button" style="padding: 8px 12px; min-height: auto; display: inline-flex; align-items: center; justify-content: center;" title="Halaman Berikutnya">
                        <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2.5" fill="none" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"></polyline></svg>
                    </a>
                @else
                    <span class="button disabled" style="opacity: 0.4; cursor: not-allowed; padding: 8px 12px; min-height: auto; display: inline-flex; align-items: center; justify-content: center;" title="Halaman Berikutnya">
                        <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2.5" fill="none" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"></polyline></svg>
                    </span>
                @endif
            </div>
        </div>
    @endif
</section>
@endsection
