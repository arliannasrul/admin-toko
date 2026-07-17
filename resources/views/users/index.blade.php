@extends('layouts.app')

@section('content')
<header class="page-head">
    <div>
        <p class="eyebrow">Pengaturan Sistem</p>
        <h1>Manajemen Pengguna</h1>
    </div>
</header>

{{-- Pending admin requests alert --}}
@php 
    // Count pending requests across ALL pages (not just current page)
    $pendingCount = \App\Models\User::where('admin_request_status', 'like', 'pending:%')->count(); 
@endphp
@if ($pendingCount > 0)
    <div style="margin-bottom: 24px; padding: 16px 20px; background: rgba(245, 158, 11, 0.08); border: 1px solid rgba(245, 158, 11, 0.25); border-radius: 12px; display: flex; align-items: center; gap: 14px;">
        <span style="font-size: 22px; flex-shrink: 0;">🔔</span>
        <div>
            <strong style="display: block; color: #fbbf24; font-size: 0.95rem; margin-bottom: 2px;">
                {{ $pendingCount }} Pengajuan Perubahan Role Baru Menunggu Persetujuan
            </strong>
            <span style="font-size: 0.83rem; color: #94a3b8;">
                Tinjau tabel di bawah. Sistem otomatis memilih role yang diajukan pada dropdown. Klik Approve untuk menyetujui.
            </span>
        </div>
    </div>
@endif

<section class="panel">
    <div class="panel-head">
        <h2>Daftar Pengguna & Role</h2>
    </div>

    @if ($errors->any())
        <div class="error" style="margin-bottom: 20px;">
            @foreach ($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif

    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Pengguna</th>
                    <th>Email</th>
                    <th>Role Saat Ini</th>
                    <th>Status Pengajuan</th>
                    <th style="width: 320px;">Ubah Role</th>
                </tr>
            </thead>
            <tbody>
            @forelse ($users as $user)
                @php 
                    $isPendingApplicant = !empty($user->admin_request_status) && str_starts_with($user->admin_request_status, 'pending:'); 
                    $requestedRole = $isPendingApplicant ? str_replace('pending:', '', $user->admin_request_status) : null;
                    
                    $roleLabel = match($requestedRole) {
                        'super_admin' => 'Super Admin',
                        'warehouse_staff' => 'Staff Gudang',
                        'sales_staff' => 'Staff Penjualan',
                        default => $requestedRole
                    };
                @endphp
                <tr style="{{ $isPendingApplicant ? 'background: rgba(245, 158, 11, 0.04); outline: 1px solid rgba(245,158,11,0.15);' : '' }}">
                    <td>
                        <div style="display: flex; align-items: center; gap: 12px;">
                            @if ($user->avatar)
                                <img src="{{ $user->avatar }}" alt="" style="width: 36px; height: 36px; border-radius: 50%; object-fit: cover; border: 1px solid rgba(255,255,255,0.1);">
                            @else
                                <div style="width: 36px; height: 36px; border-radius: 50%; background: #2bb5a6; display: grid; place-items: center; font-weight: 700; color: white;">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </div>
                            @endif
                            <div>
                                <strong style="color: white; display: block; font-size: 14px;">
                                    @if($isPendingApplicant) 🔔 @endif
                                    {{ $user->name }}
                                </strong>
                                @if($user->id === Auth::id())
                                    <small style="color: var(--accent); font-size: 10px; font-weight: 600; text-transform: uppercase;">Anda</small>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td>{{ $user->email }}</td>
                    <td>
                        @if ($user->isSuperAdmin())
                            <span class="badge" style="background: rgba(6, 182, 212, 0.1); color: #22d3ee; border: 1px solid rgba(6, 182, 212, 0.2);">Super Admin</span>
                        @elseif ($user->isWarehouseStaff())
                            <span class="badge" style="background: rgba(34, 197, 94, 0.1); color: #4ade80; border: 1px solid rgba(34, 197, 94, 0.2);">Staff Gudang</span>
                        @elseif ($user->isSalesStaff())
                            <span class="badge" style="background: rgba(249, 115, 22, 0.1); color: #fb923c; border: 1px solid rgba(249, 115, 22, 0.2);">Staff Penjualan</span>
                        @elseif ($user->role === 'guest')
                            <span class="badge" style="background: rgba(245, 158, 11, 0.1); color: #fbbf24; border: 1px solid rgba(245, 158, 11, 0.2);">👁️ Guest</span>
                        @else
                            <span class="badge" style="background: rgba(148, 163, 184, 0.1); color: #94a3b8; border: 1px solid rgba(148, 163, 184, 0.2);">{{ $user->role }}</span>
                        @endif
                    </td>
                    <td>
                        @if ($isPendingApplicant)
                            <span style="display: inline-flex; flex-direction: column; gap: 4px;">
                                <span style="display: inline-flex; align-items: center; gap: 6px; font-size: 0.78rem; font-weight: 600; color: #fbbf24; background: rgba(245,158,11,0.1); border: 1px solid rgba(245,158,11,0.25); border-radius: 20px; padding: 4px 10px;">
                                    <span style="width: 6px; height: 6px; border-radius: 50%; background: #f59e0b; animation: pulse 1.8s infinite; display: inline-block;"></span>
                                    Minta Akses
                                </span>
                                <small style="color: #cbd5e1; font-size: 0.75rem; text-align: center;">Ke: <strong>{{ $roleLabel }}</strong></small>
                            </span>
                        @else
                            <span style="color: #475569; font-size: 0.82rem;">—</span>
                        @endif
                    </td>
                    <td>
                        <form method="POST" action="{{ route('users.updateRole', $user->id) }}" style="display: flex; gap: 8px; margin: 0; align-items: center;">
                            @csrf
                            <select name="role" required style="padding: 8px 12px; font-size: 13px; height: 38px; min-width: 160px; {{ $isPendingApplicant ? 'border-color: rgba(245,158,11,0.5);' : '' }}">
                                <option value="super_admin" @selected(($requestedRole ?? $user->role) === 'super_admin')>Super Admin</option>
                                <option value="warehouse_staff" @selected(($requestedRole ?? $user->role) === 'warehouse_staff')>Staff Gudang</option>
                                <option value="sales_staff" @selected(($requestedRole ?? $user->role) === 'sales_staff')>Staff Penjualan</option>
                                <option value="guest" @selected(($requestedRole ?? $user->role) === 'guest')>Guest (Demo)</option>
                            </select>
                            <button type="submit" class="button primary" style="padding: 8px 16px; font-size: 13px; height: 38px; min-height: auto; {{ $isPendingApplicant ? 'background: linear-gradient(135deg, #f59e0b, #d97706); box-shadow: 0 4px 14px rgba(245,158,11,0.3);' : '' }}">
                                @if($isPendingApplicant) ✅ Approve @else Simpan @endif
                            </button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="empty">Tidak ada pengguna terdaftar.</td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination Control --}}
    @if ($users->count() > 0 || $users->total() > 0)
        <div class="pagination-container">
            <div class="pagination-info">
                Menampilkan <strong>{{ $users->firstItem() ?? 0 }}</strong> - <strong>{{ $users->lastItem() ?? 0 }}</strong> dari total <strong>{{ $users->total() }}</strong> pengguna.
            </div>
            <div class="pagination-nav">
                <span class="page-counter">Halaman {{ $users->currentPage() }} / {{ $users->lastPage() }}</span>
                <div class="pagination-buttons">
                    @if ($users->onFirstPage())
                        <span class="button disabled" title="Halaman Sebelumnya">
                            <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2.5" fill="none" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"></polyline></svg>
                        </span>
                    @else
                        <a href="{{ $users->previousPageUrl() }}" class="button" title="Halaman Sebelumnya">
                            <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2.5" fill="none" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"></polyline></svg>
                        </a>
                    @endif
                    @if ($users->hasMorePages())
                        <a href="{{ $users->nextPageUrl() }}" class="button" title="Halaman Berikutnya">
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

<style>
    @keyframes pulse {
        0%, 100% { opacity: 1; transform: scale(1); }
        50% { opacity: 0.5; transform: scale(0.8); }
    }
</style>
@endsection
