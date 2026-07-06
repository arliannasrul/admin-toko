<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name') }}</title>
    <link rel="stylesheet" href="/css/app.css">
</head>
<body>
    <aside class="sidebar">
        <div style="display: flex; flex-direction: column; gap: 4px;">
            <div class="brand">
                <span class="brand-mark">MS</span>
                <div>
                    <strong>MitraSpace</strong>
                    <small>Seller Center</small>
                </div>
            </div>
            <nav>
                <a href="{{ route('dashboard') }}" @class(['active' => request()->routeIs('dashboard')])>
                    <span style="font-size: 16px;">📊</span> Dashboard
                </a>
                <a href="{{ route('items.index') }}" @class(['active' => request()->routeIs('items.*')])>
                    <span style="font-size: 16px;">📦</span> Barang
                </a>
                @if(!Auth::user()->isWarehouseStaff())
                <a href="{{ route('orders.index') }}" @class(['active' => request()->routeIs('orders.*')])>
                    <span style="font-size: 16px;">🚚</span> Tracking & Orders
                </a>
                <a href="{{ route('crm.index') }}" @class(['active' => request()->routeIs('crm.*')])>
                    <span style="font-size: 16px;">👥</span> CRM & Pelanggan
                </a>
                @endif
                @if(!Auth::user()->isSalesStaff())
                <a href="{{ route('reports.index') }}" @class(['active' => request()->routeIs('reports.*')])>
                    <span style="font-size: 16px;">📈</span> Laporan
                </a>
                @endif
                <a href="{{ route('notifications.index') }}" @class(['active' => request()->routeIs('notifications.*')])>
                    <span style="font-size: 16px;">🔔</span> Notifikasi
                </a>
                @if(Auth::user()->isSuperAdmin())
                <a href="{{ route('users.index') }}" @class(['active' => request()->routeIs('users.*')])>
                    <span style="font-size: 16px;">⚙️</span> Manajemen Pengguna
                </a>
                @endif
            </nav>
        </div>

        @auth
        <div class="user-profile" style="border-top: 1px solid rgba(255,255,255,0.1); padding-top: 20px; margin-top: auto; display: flex; flex-direction: column; gap: 12px;">
            <div style="display: flex; align-items: center; gap: 10px;">
                @if (Auth::user()->avatar)
                    <img src="{{ Auth::user()->avatar }}" alt="" style="width: 38px; height: 38px; border-radius: 50%; object-fit: cover; border: 1px solid rgba(255,255,255,0.2);">
                @else
                    <div style="width: 38px; height: 38px; border-radius: 50%; background: #2bb5a6; display: grid; place-items: center; font-weight: 700; color: white;">
                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                    </div>
                @endif
                <div style="overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                    <strong style="display: block; font-size: 14px; color: white;">{{ Auth::user()->name }}</strong>
                    <small style="display: block; font-size: 11px; color: #9fb1bd; margin-bottom: 4px;">{{ Auth::user()->email }}</small>
                    @if (Auth::user()->isSuperAdmin())
                        <span style="display: inline-block; font-size: 9px; padding: 2px 6px; background: rgba(6, 182, 212, 0.2); color: #22d3ee; border: 1px solid rgba(6, 182, 212, 0.4); border-radius: 4px; font-weight: 600;">Super Admin</span>
                    @elseif (Auth::user()->isWarehouseStaff())
                        <span style="display: inline-block; font-size: 9px; padding: 2px 6px; background: rgba(34, 197, 94, 0.2); color: #4ade80; border: 1px solid rgba(34, 197, 94, 0.4); border-radius: 4px; font-weight: 600;">Staff Gudang</span>
                    @elseif (Auth::user()->isSalesStaff())
                        <span style="display: inline-block; font-size: 9px; padding: 2px 6px; background: rgba(249, 115, 22, 0.2); color: #fb923c; border: 1px solid rgba(249, 115, 22, 0.4); border-radius: 4px; font-weight: 600;">Staff Penjualan</span>
                    @endif
                </div>
            </div>
            <form method="POST" action="{{ route('logout') }}" style="margin: 0; width: 100%;">
                @csrf
                <button type="submit" style="width: 100%; border: 1px solid rgba(255,255,255,0.15); background: rgba(255,255,255,0.05); color: #f8fafc; border-radius: 6px; padding: 8px 12px; font-size: 13px; font-weight: 600; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 6px; transition: all 0.2s;">
                    <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                        <polyline points="16 17 21 12 16 7"></polyline>
                        <line x1="21" y1="12" x2="9" y2="12"></line>
                    </svg>
                    Keluar
                </button>
            </form>
        </div>
        @endauth
    </aside>

    <main class="main">
        @if (session('status'))
            <div class="flash">{{ session('status') }}</div>
        @endif

        @if ($errors->any())
            <div class="error">
                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        @yield('content')
    </main>
</body>
</html>
