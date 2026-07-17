@extends('layouts.app')

@section('content')
<header class="page-head">
    <div>
        <p class="eyebrow">Customer Detail Dashboard</p>
        <h1>Profil: {{ $customerName }}</h1>
    </div>
    <a class="button secondary" href="{{ route('crm.index') }}">
        ⬅️ Database Pelanggan
    </a>
</header>

<div class="grid two">
    <!-- Profil Pelanggan & LTV Info -->
    <section class="panel" style="margin-bottom: 0;">
        <div class="panel-head">
            <h2>Informasi Kontak & Segmentasi</h2>
        </div>
        <table style="background: transparent;">
            <tbody>
                <tr>
                    <td style="font-weight: 600; width: 150px; color: var(--muted); border-bottom: 1px solid var(--line);">Nama Lengkap</td>
                    <td style="border-bottom: 1px solid var(--line);">{{ $customerName }}</td>
                </tr>
                <tr>
                    <td style="font-weight: 600; color: var(--muted); border-bottom: 1px solid var(--line);">No. Telepon / WA</td>
                    <td style="border-bottom: 1px solid var(--line);">
                        <code style="background: rgba(255,255,255,0.06); padding: 4px 8px; border-radius: 4px; color: var(--accent); font-size: 0.95rem;">
                            {{ $phone }}
                        </code>
                    </td>
                </tr>
                <tr>
                    <td style="font-weight: 600; color: var(--muted); border-bottom: 1px solid var(--line);">Alamat Terakhir</td>
                    <td style="border-bottom: 1px solid var(--line); font-size: 0.92rem; line-height: 1.5;">{{ $customerAddress }}</td>
                </tr>
                <tr>
                    <td style="font-weight: 600; color: var(--muted); border-bottom: none;">Segmentasi</td>
                    <td style="border-bottom: none;">
                        @if ($segment === 'VIP')
                            <span class="badge" style="background: rgba(251, 191, 36, 0.15); color: #fbbf24; border: 1px solid rgba(251, 191, 36, 0.3);">🥇 VIP Customer</span>
                        @elseif ($segment === 'Loyal')
                            <span class="badge" style="background: rgba(34, 211, 238, 0.15); color: #22d3ee; border: 1px solid rgba(34, 211, 238, 0.3);">🔁 Loyal Customer</span>
                        @elseif ($segment === 'At Risk')
                            <span class="badge badge-cancelled">⚠️ At Risk (Inaktif)</span>
                        @else
                            <span class="badge badge-pending">🆕 New Customer</span>
                        @endif
                    </td>
                </tr>
            </tbody>
        </table>
    </section>

    <!-- Metrics LTV -->
    <section class="panel" style="margin-bottom: 0; display: flex; flex-direction: column; justify-content: space-between;">
        <div>
            <div class="panel-head">
                <h2>Ringkasan Lifetime Value (LTV)</h2>
            </div>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-top: 10px;">
                <div style="background: rgba(255,255,255,0.02); border: 1px solid var(--line); padding: 16px; border-radius: 8px;">
                    <span style="color: var(--muted); font-size: 0.8rem; display: block; text-transform: uppercase; margin-bottom: 6px;">Total Belanja (LTV)</span>
                    <strong style="color: var(--accent); font-size: 1.5rem;">Rp {{ number_format($totalSpent, 0, ',', '.') }}</strong>
                </div>
                <div style="background: rgba(255,255,255,0.02); border: 1px solid var(--line); padding: 16px; border-radius: 8px;">
                    <span style="color: var(--muted); font-size: 0.8rem; display: block; text-transform: uppercase; margin-bottom: 6px;">Jumlah Pesanan</span>
                    <strong style="font-size: 1.5rem;">{{ $totalOrders }} Kali</strong>
                </div>
            </div>
        </div>
        <div style="margin-top: 20px;">
            <a href="{{ route('crm.templates', ['order_id' => $orders->first()->id]) }}" class="button primary" style="width: 100%;">
                💬 Kirim Pesan Follow Up Terakhir
            </a>
        </div>
    </section>
</div>

<!-- Riwayat Order Pelanggan -->
<section class="panel" style="margin-top: 30px;">
    <div class="panel-head">
        <h2>Riwayat Pesanan</h2>
    </div>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>No. Order</th>
                    <th>Tanggal</th>
                    <th>Daftar Barang</th>
                    <th>Ekspedisi & Status Kirim</th>
                    <th>Status Bayar</th>
                    <th>Total Biaya</th>
                    <th style="text-align: right;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($orders as $order)
                    @php
                        $itemsTotal = $order->items->sum(fn($i) => $i->pivot->quantity * $i->pivot->price);
                        $orderGrandTotal = $order->shipping_cost + $itemsTotal;
                    @endphp
                    <tr>
                        <td>
                            <strong>{{ $order->order_number }}</strong>
                        </td>
                        <td>
                            {{ $order->created_at->format('d/m/Y H:i') }}
                        </td>
                        <td>
                            <ul style="margin: 0; padding-left: 16px; font-size: 0.85rem; color: #cbd5e1;">
                                @foreach ($order->items as $item)
                                    <li>{{ $item->name }} (x{{ $item->pivot->quantity }})</li>
                                @endforeach
                            </ul>
                        </td>
                        <td>
                            <div style="display: flex; flex-direction: column; gap: 4px;">
                                <span style="font-size: 0.8rem; text-transform: uppercase;">{{ $order->courier }} - {{ $order->shipping_service }}</span>
                                <span class="badge badge-{{ $order->status }}" style="font-size: 0.7rem; padding: 2px 8px; width: fit-content;">
                                    {{ strtoupper($order->status) }}
                                </span>
                            </div>
                        </td>
                        <td>
                            <span class="badge badge-{{ $order->payment_status === 'paid' ? 'delivered' : ($order->payment_status === 'refunded' ? 'cancelled' : 'pending') }}">
                                {{ strtoupper($order->payment_status ?? 'unpaid') }}
                            </span>
                        </td>
                        <td style="font-weight: 700;">
                            Rp {{ number_format($orderGrandTotal, 0, ',', '.') }}
                        </td>
                        <td style="text-align: right;">
                            <a href="{{ route('orders.show', $order->id) }}" class="button" style="padding: 6px 12px; min-height: auto; font-size: 0.85rem;">
                                👁️ Lihat Detail
                            </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Pagination Control --}}
    @if ($orders->count() > 0 || $orders->total() > 0)
        <div class="pagination-container" style="border-bottom-left-radius: 10px; border-bottom-right-radius: 10px; border-top: 1px solid var(--line);">
            <div class="pagination-info">
                Menampilkan <strong>{{ $orders->firstItem() ?? 0 }}</strong> - <strong>{{ $orders->lastItem() ?? 0 }}</strong> dari total <strong>{{ $orders->total() }}</strong> pesanan.
            </div>
            <div class="pagination-nav">
                <span class="page-counter">Halaman {{ $orders->currentPage() }} / {{ $orders->lastPage() }}</span>
                <div class="pagination-buttons">
                    @if ($orders->onFirstPage())
                        <span class="button disabled" title="Halaman Sebelumnya">
                            <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2.5" fill="none" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"></polyline></svg>
                        </span>
                    @else
                        <a href="{{ $orders->previousPageUrl() }}" class="button" title="Halaman Sebelumnya">
                            <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2.5" fill="none" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"></polyline></svg>
                        </a>
                    @endif
                    @if ($orders->hasMorePages())
                        <a href="{{ $orders->nextPageUrl() }}" class="button" title="Halaman Berikutnya">
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

<!-- Keluhan & CS Tiket Pelanggan -->
<div class="grid two" style="margin-top: 30px; align-items: start;">
    <!-- Daftar Keluhan -->
    <section class="panel">
        <div class="panel-head">
            <h2>Daftar Keluhan Customer</h2>
        </div>
        @forelse ($complaints as $complaint)
            <div style="background: rgba(255,255,255,0.02); border: 1px solid var(--line); padding: 18px; border-radius: 8px; margin-bottom: 12px; position: relative;">
                <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 8px;">
                    <div>
                        <h4 style="margin: 0; color: var(--accent); font-size: 1rem;">{{ $complaint->subject }}</h4>
                        <small style="color: var(--muted);">Dilaporkan: {{ $complaint->created_at->format('d/m/Y H:i') }}</small>
                        @if ($complaint->order)
                            <div style="font-size: 0.8rem; margin-top: 4px; color: var(--muted);">
                                Terkait Order: <strong>{{ $complaint->order->order_number }}</strong>
                            </div>
                        @endif
                    </div>
                    <span class="badge badge-{{ $complaint->status === 'resolved' ? 'delivered' : ($complaint->status === 'in_progress' ? 'processing' : 'pending') }}" style="font-size: 0.75rem;">
                        {{ strtoupper($complaint->status) }}
                    </span>
                </div>
                <p style="margin: 0; color: #cbd5e1; font-size: 0.9rem; line-height: 1.5; white-space: pre-wrap; margin-bottom: 14px;">
                    {{ $complaint->description }}
                </p>

                <!-- Update Status Form -->
                <form action="{{ route('crm.complaints.updateStatus', $complaint->id) }}" method="POST" style="margin: 0; border-top: 1px solid var(--line); padding-top: 10px; display: flex; align-items: center; justify-content: space-between; gap: 8px;">
                    @csrf
                    <div style="display: flex; align-items: center; gap: 6px;">
                        <span style="font-size: 0.8rem; color: var(--muted);">Ubah Status:</span>
                        <select name="status" style="padding: 4px 8px; font-size: 0.8rem; width: 140px; min-height: auto;">
                            <option value="open" @selected($complaint->status === 'open')>Open</option>
                            <option value="in_progress" @selected($complaint->status === 'in_progress')>In Progress</option>
                            <option value="resolved" @selected($complaint->status === 'resolved')>Resolved</option>
                        </select>
                    </div>
                    <button type="submit" class="button" style="padding: 4px 10px; min-height: auto; font-size: 0.8rem; background: var(--accent-gradient); border: none;">Update</button>
                </form>

                @if ($complaint->resolved_at)
                    <div style="font-size: 0.78rem; color: var(--success); margin-top: 8px; text-align: right;">
                        Selesai pada: {{ $complaint->resolved_at->format('d/m/Y H:i') }}
                    </div>
                @endif
            </div>
        @empty
            <p class="empty" style="padding: 20px 0;">Belum ada riwayat keluhan untuk pelanggan ini.</p>
        @endforelse
    </section>

    <!-- Tambah Keluhan Form -->
    <section class="panel">
        <div class="panel-head">
            <h2>Laporkan Keluhan Baru</h2>
        </div>
        <form action="{{ route('crm.complaints.store') }}" method="POST" style="display: grid; gap: 14px;">
            @csrf
            <input type="hidden" name="customer_phone" value="{{ $phone }}">
            <input type="hidden" name="customer_name" value="{{ $customerName }}">

            <div>
                <label style="display: block; font-size: 0.85rem; color: var(--muted); margin-bottom: 6px;">Terkait Pesanan (Opsional)</label>
                <select name="order_id">
                    <option value="">-- General / Tidak Terkait Order Spesifik --</option>
                    @foreach ($allOrdersForDropdown as $o)
                        <option value="{{ $o->id }}">Order: {{ $o->order_number }} (Rp {{ number_format($o->shipping_cost + $o->items->sum(fn($i) => $i->pivot->quantity * $i->pivot->price), 0, ',', '.') }})</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label style="display: block; font-size: 0.85rem; color: var(--muted); margin-bottom: 6px;">Subjek Keluhan</label>
                <input type="text" name="subject" placeholder="Contoh: Barang cacat/pecah, Resi lambat, dll" required>
            </div>

            <div>
                <label style="display: block; font-size: 0.85rem; color: var(--muted); margin-bottom: 6px;">Detail Penjelasan Keluhan</label>
                <textarea name="description" rows="4" placeholder="Tuliskan secara lengkap detail keluhan dari customer dan solusi sementara yang direncanakan..." required></textarea>
            </div>

            <button type="submit" class="button primary" style="width: 100%;">
                💾 Simpan Keluhan Customer
            </button>
        </form>
    </section>
</div>
@endsection
