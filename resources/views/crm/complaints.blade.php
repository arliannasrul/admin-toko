@extends('layouts.app')

@section('content')
<header class="page-head">
    <div>
        <p class="eyebrow">Customer Support</p>
        <h1>Daftar Keluhan Pelanggan</h1>
    </div>
    <a class="button secondary" href="{{ route('crm.index') }}" style="display: inline-flex; align-items: center; gap: 6px;">
        <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none"><polyline points="15 18 9 12 15 6"></polyline></svg> Database Pelanggan
    </a>
</header>

<!-- Filter Tabs -->
<div class="tabs">
    <a href="{{ route('crm.complaints', ['status' => 'all']) }}" class="tab-link {{ $status === 'all' ? 'active' : '' }}">
        Semua ({{ $counts['all'] }})
    </a>
    <a href="{{ route('crm.complaints', ['status' => 'open']) }}" class="tab-link {{ $status === 'open' ? 'active' : '' }}">
        Open ({{ $counts['open'] }})
    </a>
    <a href="{{ route('crm.complaints', ['status' => 'in_progress']) }}" class="tab-link {{ $status === 'in_progress' ? 'active' : '' }}">
        In Progress ({{ $counts['in_progress'] }})
    </a>
    <a href="{{ route('crm.complaints', ['status' => 'resolved']) }}" class="tab-link {{ $status === 'resolved' ? 'active' : '' }}">
        Resolved ({{ $counts['resolved'] }})
    </a>
</div>

<div class="grid two" style="grid-template-columns: 2fr 1fr; align-items: start;">
    <!-- Daftar Tiket Keluhan -->
    <section class="panel">
        <div class="panel-head">
            <h2>Daftar Tiket Dukungan (Status: {{ strtoupper($status) }})</h2>
        </div>

        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Tiket</th>
                        <th>Pelanggan</th>
                        <th>Subjek / Keluhan</th>
                        <th>Terkait Order</th>
                        <th>Status</th>
                        <th style="text-align: right;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($complaints as $c)
                        <tr>
                            <td>
                                <strong>#TK-{{ str_pad($c->id, 5, '0', STR_PAD_LEFT) }}</strong>
                                <small style="display: block; color: var(--muted);">{{ $c->created_at->format('d/m/Y H:i') }}</small>
                            </td>
                            <td>
                                <strong>{{ $c->customer_name }}</strong>
                                <small style="display: block; color: var(--muted); font-family: monospace;">{{ $c->customer_phone }}</small>
                            </td>
                            <td>
                                <strong style="color: var(--accent); display: block;">{{ $c->subject }}</strong>
                                <p style="margin: 4px 0 0 0; color: #cbd5e1; font-size: 0.85rem; max-width: 250px; overflow: hidden; text-overflow: ellipsis; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; line-height: 1.4;">
                                    {{ $c->description }}
                                </p>
                            </td>
                            <td>
                                @if ($c->order)
                                    <a href="{{ route('orders.show', $c->order->id) }}" style="text-decoration: underline; font-weight: 600;">
                                        {{ $c->order->order_number }}
                                    </a>
                                @else
                                    <span style="color: var(--muted); font-style: italic;">General</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge badge-{{ $c->status === 'resolved' ? 'delivered' : ($c->status === 'in_progress' ? 'processing' : 'pending') }}">
                                    {{ strtoupper($c->status) }}
                                </span>
                            </td>
                            <td style="text-align: right;">
                                <div style="display: flex; flex-direction: column; gap: 6px; align-items: flex-end;">
                                    <a href="{{ route('crm.detail', ['phone' => $c->customer_phone]) }}" class="button secondary" style="padding: 4px 10px; min-height: auto; font-size: 0.8rem; width: fit-content; display: inline-flex; align-items: center; gap: 4px;">
                                        <svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2" fill="none"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg> Profil
                                    </a>
                                    
                                    <!-- Quick Update Status -->
                                    <form action="{{ route('crm.complaints.updateStatus', $c->id) }}" method="POST" style="margin: 0; display: flex; gap: 4px;">
                                        @csrf
                                        <select name="status" style="padding: 2px 6px; font-size: 0.75rem; width: 105px; min-height: auto;">
                                            <option value="open" @selected($c->status === 'open')>Open</option>
                                            <option value="in_progress" @selected($c->status === 'in_progress')>In-Prog</option>
                                            <option value="resolved" @selected($c->status === 'resolved')>Resolved</option>
                                        </select>
                                        <button type="submit" class="button" style="padding: 2px 6px; min-height: auto; font-size: 0.75rem; background: var(--accent); border: none;">Go</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="empty">Tidak ada tiket keluhan dengan status ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    <!-- Buat Tiket General -->
    <section class="panel">
        <div class="panel-head">
            <h2>Buat Tiket Baru</h2>
        </div>
        <p style="font-size: 0.85rem; color: var(--muted); margin-bottom: 15px;">
            Gunakan form ini untuk membuat tiket aduan secara manual jika dihubungi pelanggan di luar web (misalnya via chat WA manual).
        </p>
        <form action="{{ route('crm.complaints.store') }}" method="POST" style="display: grid; gap: 14px;">
            @csrf
            
            <div>
                <label style="display: block; font-size: 0.85rem; color: var(--muted); margin-bottom: 6px;">Pilih Acuan Order Terakhir (Opsional)</label>
                <select id="order-selector" style="font-size: 0.88rem;">
                    <option value="">-- Pilih dari order terbaru --</option>
                    @foreach ($recentOrders as $ro)
                        <option value="{{ $ro->id }}" 
                                data-name="{{ $ro->customer_name }}" 
                                data-phone="{{ $ro->customer_phone }}">
                            {{ $ro->order_number }} - {{ $ro->customer_name }} ({{ $ro->customer_phone }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div style="border-top: 1px solid var(--line); padding-top: 10px;">
                <input type="hidden" name="order_id" id="form-order-id">
                
                <div style="margin-bottom: 12px;">
                    <label style="display: block; font-size: 0.85rem; color: var(--muted); margin-bottom: 6px;">Nama Customer</label>
                    <input type="text" name="customer_name" id="form-customer-name" placeholder="Nama Pelanggan" required>
                </div>

                <div style="margin-bottom: 12px;">
                    <label style="display: block; font-size: 0.85rem; color: var(--muted); margin-bottom: 6px;">No. HP Customer</label>
                    <input type="text" name="customer_phone" id="form-customer-phone" placeholder="Contoh: 0812345678" required>
                </div>
            </div>

            <div>
                <label style="display: block; font-size: 0.85rem; color: var(--muted); margin-bottom: 6px;">Subjek Keluhan</label>
                <input type="text" name="subject" placeholder="Contoh: Salah kirim barang, paket basah" required>
            </div>

            <div>
                <label style="display: block; font-size: 0.85rem; color: var(--muted); margin-bottom: 6px;">Detail Penjelasan Keluhan</label>
                <textarea name="description" rows="4" placeholder="Jelaskan kendala secara lengkap..." required></textarea>
            </div>

            <button type="submit" class="button primary" style="width: 100%; display: inline-flex; align-items: center; justify-content: center; gap: 6px;">
                <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path><polyline points="17 21 17 13 7 13 7 21"></polyline><polyline points="7 3 7 8 15 8"></polyline></svg> Buat Tiket Dukungan
            </button>
        </form>
    </section>
</div>

<script>
    document.getElementById('order-selector').addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        if (selectedOption.value) {
            document.getElementById('form-order-id').value = selectedOption.value;
            document.getElementById('form-customer-name').value = selectedOption.getAttribute('data-name');
            document.getElementById('form-customer-phone').value = selectedOption.getAttribute('data-phone');
        } else {
            document.getElementById('form-order-id').value = '';
            document.getElementById('form-customer-name').value = '';
            document.getElementById('form-customer-phone').value = '';
        }
    });
</script>
@endsection
