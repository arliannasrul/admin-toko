@extends('layouts.app')

@section('content')
<header class="page-head">
    <div>
        <p class="eyebrow">Pengaturan Sistem</p>
        <h1>Manajemen Pengguna</h1>
    </div>
</header>

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
                    <th style="width: 320px;">Ubah Role</th>
                </tr>
            </thead>
            <tbody>
            @forelse ($users as $user)
                <tr>
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
                                <strong style="color: white; display: block; font-size: 14px;">{{ $user->name }}</strong>
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
                        @else
                            <span class="badge" style="background: rgba(148, 163, 184, 0.1); color: #94a3b8; border: 1px solid rgba(148, 163, 184, 0.2);">{{ $user->role }}</span>
                        @endif
                    </td>
                    <td>
                        <form method="POST" action="{{ route('users.updateRole', $user->id) }}" style="display: flex; gap: 8px; margin: 0; align-items: center;">
                            @csrf
                            <select name="role" required style="padding: 8px 12px; font-size: 13px; height: 38px; min-width: 160px;">
                                <option value="super_admin" @selected($user->role === 'super_admin')>Super Admin</option>
                                <option value="warehouse_staff" @selected($user->role === 'warehouse_staff')>Staff Gudang</option>
                                <option value="sales_staff" @selected($user->role === 'sales_staff')>Staff Penjualan</option>
                            </select>
                            <button type="submit" class="button primary" style="padding: 8px 16px; font-size: 13px; height: 38px; min-height: auto;">
                                Simpan
                            </button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="empty">Tidak ada pengguna terdaftar.</td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
</section>
@endsection
