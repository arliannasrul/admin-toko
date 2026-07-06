@extends('layouts.app')

@section('content')
<header class="page-head">
    <div>
        <p class="eyebrow">Customer Relationship</p>
        <h1>CRM & Database Pelanggan</h1>
    </div>
    <div style="display: flex; gap: 10px;">
        <a class="button secondary" href="{{ route('crm.complaints') }}">
            ⚠️ Keluhan Pelanggan <span style="background: var(--danger); color: white; padding: 2px 6px; border-radius: 9999px; font-size: 0.75rem; margin-left: 4px;">{{ $activeComplaintsCount }}</span>
        </a>
        <a class="button secondary" href="{{ route('orders.index') }}">
            🚚 Kelola Pengiriman
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
        <strong style="color: #fbbf24;">🥇 {{ $vipCount }}</strong>
    </article>
    <article style="border-left: 4px solid var(--danger);">
        <span>Pelanggan At Risk</span>
        <strong style="color: #f87171;">⚠️ {{ $atRiskCount }}</strong>
    </article>
    <article style="border-left: 4px solid var(--warn);">
        <span>Keluhan Aktif</span>
        <strong style="color: #fb923c;">💬 {{ $activeComplaintsCount }}</strong>
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
                                <span class="badge" style="background: rgba(251, 191, 36, 0.15); color: #fbbf24; border: 1px solid rgba(251, 191, 36, 0.3);">🥇 VIP</span>
                            @elseif ($customer['segment'] === 'Loyal')
                                <span class="badge" style="background: rgba(34, 211, 238, 0.15); color: #22d3ee; border: 1px solid rgba(34, 211, 238, 0.3);">🔁 Loyal</span>
                            @elseif ($customer['segment'] === 'At Risk')
                                <span class="badge badge-cancelled">⚠️ At Risk</span>
                            @else
                                <span class="badge badge-pending">🆕 New</span>
                            @endif
                        </td>
                        <td>
                            <div style="display: flex; flex-direction: column; gap: 4px;">
                                <span class="badge badge-{{ $customer['last_shipping_status'] }}" style="font-size: 0.7rem; padding: 2px 8px; width: fit-content;">
                                    📦 {{ strtoupper($customer['last_shipping_status']) }}
                                </span>
                                <span class="badge badge-{{ $customer['last_payment_status'] === 'paid' ? 'delivered' : ($customer['last_payment_status'] === 'refunded' ? 'cancelled' : 'pending') }}" style="font-size: 0.7rem; padding: 2px 8px; width: fit-content;">
                                    💳 {{ strtoupper($customer['last_payment_status'] ?? 'unpaid') }}
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
                                <a href="{{ route('crm.detail', ['phone' => $customer['customer_phone']]) }}" class="button secondary" style="padding: 6px 12px; min-height: auto; font-size: 0.85rem;">
                                    👁️ Detail & Riwayat
                                </a>
                                <a href="{{ route('crm.templates', ['order_id' => \App\Models\Order::where('customer_phone', $customer['customer_phone'])->latest()->first()->id]) }}" class="button primary" style="padding: 6px 12px; min-height: auto; font-size: 0.85rem;">
                                    💬 Follow Up
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
</section>
@endsection
