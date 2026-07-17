@extends('layouts.app')

@section('content')
<header class="page-head">
    <div>
        <p class="eyebrow">Customer Relationship</p>
        <h1>CRM & Database Pelanggan</h1>
    </div>
    <div style="display: flex; gap: 10px;">
        <a class="button secondary" href="{{ route('crm.complaints') }}" style="display: inline-flex; align-items: center; gap: 6px;">
            <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path><line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>
            Keluhan Pelanggan <span style="background: var(--danger); color: white; padding: 2px 6px; border-radius: 9999px; font-size: 0.75rem; margin-left: 4px;">{{ $activeComplaintsCount }}</span>
        </a>
        <a class="button secondary" href="{{ route('orders.index') }}" style="display: inline-flex; align-items: center; gap: 6px;">
            <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none"><rect x="1" y="3" width="15" height="13"></rect><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"></polygon><circle cx="5.5" cy="18.5" r="2.5"></circle><circle cx="18.5" cy="18.5" r="2.5"></circle></svg>
            Kelola Pengiriman
        </a>
    </div>
</header>

<!-- Stats Cards -->
<section class="stats">
    <article>
        <span>Total Pelanggan</span>
        <strong>{{ $totalCustomers }}</strong>
    </article>
    <article style="border-left: 4px solid var(--accent);">
        <span>Pelanggan VIP</span>
        <strong style="color: #fbbf24; display: flex; align-items: center; gap: 6px;"><svg viewBox="0 0 24 24" width="20" height="20" stroke="currentColor" stroke-width="2" fill="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg> {{ $vipCount }}</strong>
    </article>
    <article style="border-left: 4px solid var(--danger);">
        <span>Pelanggan At Risk</span>
        <strong style="color: #f87171; display: flex; align-items: center; gap: 6px;"><svg viewBox="0 0 24 24" width="20" height="20" stroke="currentColor" stroke-width="2" fill="none"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path><line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line></svg> {{ $atRiskCount }}</strong>
    </article>
    <article style="border-left: 4px solid var(--warn);">
        <span>Keluhan Aktif</span>
        <strong style="color: #fb923c; display: flex; align-items: center; gap: 6px;"><svg viewBox="0 0 24 24" width="20" height="20" stroke="currentColor" stroke-width="2" fill="none"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg> {{ $activeComplaintsCount }}</strong>
    </article>
</section>

<section class="panel">
    <div class="panel-head">
        <h2>Database Hubungan Pelanggan</h2>
    </div>

    <p style="font-size: 0.9rem; color: var(--muted); margin-bottom: 20px;">
        Daftar di bawah ini mengelompokkan riwayat pembelian pelanggan berdasarkan nomor telepon untuk memantau loyalitas, LTV, status transaksi terakhir, dan keluhan mereka.
    </p>

    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Pelanggan</th>
                    <th>No. Telepon / WA</th>
                    <th>Segmentasi</th>
                    <th>Transaksi Terakhir</th>
                    <th>Total Belanja (LTV)</th>
                    <th>Terakhir Belanja</th>
                    <th style="text-align: right;">Aksi CRM</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($customers as $customer)
                    <tr>
                        <td>
                            <strong>{{ $customer['customer_name'] }}</strong>
                            <small style="display: block; color: var(--muted); max-width: 250px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                {{ $customer['customer_address'] }}
                            </small>
                        </td>
                        <td>
                            <code style="background: rgba(255,255,255,0.06); padding: 4px 8px; border-radius: 4px; color: var(--accent);">
                                {{ $customer['customer_phone'] }}
                            </code>
                        </td>
                        <td>
                            @if ($customer['segment'] === 'VIP')
                                <span class="badge" style="background: rgba(251, 191, 36, 0.15); color: #fbbf24; border: 1px solid rgba(251, 191, 36, 0.3); display: inline-flex; align-items: center; gap: 4px;"><svg viewBox="0 0 24 24" width="11" height="11" stroke="currentColor" stroke-width="2" fill="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg> VIP</span>
                            @elseif ($customer['segment'] === 'Loyal')
                                <span class="badge" style="background: rgba(34, 211, 238, 0.15); color: #22d3ee; border: 1px solid rgba(34, 211, 238, 0.3); display: inline-flex; align-items: center; gap: 4px;"><svg viewBox="0 0 24 24" width="11" height="11" stroke="currentColor" stroke-width="2" fill="none"><polyline points="17 1 21 5 17 9"></polyline><path d="M3 11V9a4 4 0 0 1 4-4h14"></path><polyline points="7 23 3 19 7 15"></polyline><path d="M21 13v2a4 4 0 0 1-4 4H3"></path></svg> Loyal</span>
                            @elseif ($customer['segment'] === 'At Risk')
                                <span class="badge badge-cancelled" style="display: inline-flex; align-items: center; gap: 4px;"><svg viewBox="0 0 24 24" width="11" height="11" stroke="currentColor" stroke-width="2" fill="none"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path><line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line></svg> At Risk</span>
                            @else
                                <span class="badge badge-pending" style="display: inline-flex; align-items: center; gap: 4px;"><svg viewBox="0 0 24 24" width="11" height="11" stroke="currentColor" stroke-width="2" fill="none"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg> New</span>
                            @endif
                        </td>
                        <td>
                            <div style="display: flex; flex-direction: column; gap: 4px;">
                                <span class="badge badge-{{ $customer['last_shipping_status'] }}" style="font-size: 0.7rem; padding: 2px 8px; width: fit-content; display: inline-flex; align-items: center; gap: 3px;">
                                    <svg viewBox="0 0 24 24" width="10" height="10" stroke="currentColor" stroke-width="2" fill="none"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path></svg>
                                    {{ strtoupper($customer['last_shipping_status']) }}
                                </span>
                                <span class="badge badge-{{ $customer['last_payment_status'] === 'paid' ? 'delivered' : ($customer['last_payment_status'] === 'refunded' ? 'cancelled' : 'pending') }}" style="font-size: 0.7rem; padding: 2px 8px; width: fit-content; display: inline-flex; align-items: center; gap: 3px;">
                                    <svg viewBox="0 0 24 24" width="10" height="10" stroke="currentColor" stroke-width="2" fill="none"><rect x="1" y="4" width="22" height="16" rx="2" ry="2"></rect><line x1="1" y1="10" x2="23" y2="10"></line></svg>
                                    {{ strtoupper($customer['last_payment_status'] ?? 'unpaid') }}
                                </span>
                            </div>
                        </td>
                        <td style="font-weight: 700; color: var(--accent);">
                            Rp {{ number_format($customer['total_spent'], 0, ',', '.') }}
                            <small style="display: block; color: var(--muted); font-weight: normal;">{{ $customer['total_orders'] }} Order</small>
                        </td>
                        <td>
                            {{ \Carbon\Carbon::parse($customer['last_order_date'])->format('d M Y H:i') }}
                        </td>
                        <td style="text-align: right;">
                            <div style="display: inline-flex; gap: 8px;">
                                <a href="{{ route('crm.detail', ['phone' => $customer['customer_phone']]) }}" class="button secondary" style="padding: 6px 12px; min-height: auto; font-size: 0.85rem; display: inline-flex; align-items: center; gap: 5px;">
                                    <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg> Detail & Riwayat
                                </a>
                                <a href="{{ route('crm.templates', ['order_id' => \App\Models\Order::where('customer_phone', $customer['customer_phone'])->latest()->first()->id]) }}" class="button primary" style="padding: 6px 12px; min-height: auto; font-size: 0.85rem; display: inline-flex; align-items: center; gap: 5px;">
                                    <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2" fill="none"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg> Follow Up
                                </a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="empty">Belum ada data pelanggan. Pelanggan otomatis tercatat ketika ada simulasi pesanan dibuat.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination Info & Controls --}}
    @if ($customers->count() > 0 || $customers->total() > 0)
        <div class="pagination-container">
            <div class="pagination-info">
                Menampilkan <strong>{{ $customers->firstItem() ?? 0 }}</strong> - <strong>{{ $customers->lastItem() ?? 0 }}</strong> dari total <strong>{{ $customers->total() }}</strong> pelanggan.
            </div>
            <div class="pagination-nav">
                <span class="page-counter">Halaman {{ $customers->currentPage() }} / {{ $customers->lastPage() }}</span>
                <div class="pagination-buttons">
                    @if ($customers->onFirstPage())
                        <span class="button disabled" title="Halaman Sebelumnya">
                            <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2.5" fill="none" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"></polyline></svg>
                        </span>
                    @else
                        <a href="{{ $customers->appends(request()->query())->previousPageUrl() }}" class="button" title="Halaman Sebelumnya">
                            <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2.5" fill="none" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"></polyline></svg>
                        </a>
                    @endif

                    @if ($customers->hasMorePages())
                        <a href="{{ $customers->appends(request()->query())->nextPageUrl() }}" class="button" title="Halaman Berikutnya">
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
