<!doctype html>
<html lang="id" data-theme="dark">
<head>
    <script>
        (function() {
            const savedTheme = localStorage.getItem('theme');
            const systemTheme = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
            const activeTheme = savedTheme || systemTheme;
            document.documentElement.setAttribute('data-theme', activeTheme);
        })();
    </script>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name') }}</title>
    <link rel="stylesheet" href="/css/app.css">
    <style>
        /* ===========================
           GUEST TOAST NOTIFICATION
           =========================== */
        .guest-toast {
            position: fixed;
            bottom: 28px;
            right: 28px;
            z-index: 9999;
            width: 340px;
            background: linear-gradient(135deg, #0d1222 0%, #111827 100%);
            border: 1px solid rgba(245, 158, 11, 0.35);
            border-radius: 16px;
            box-shadow: 0 24px 60px rgba(0,0,0,0.55), 0 0 0 1px rgba(245,158,11,0.08);
            padding: 20px 20px 18px;
            animation: toastIn 0.5s cubic-bezier(0.16, 1, 0.3, 1) forwards;
            backdrop-filter: blur(20px);
        }
        @keyframes toastIn {
            from { opacity: 0; transform: translateY(20px) scale(0.97); }
            to   { opacity: 1; transform: translateY(0) scale(1); }
        }
        .guest-toast-dismiss {
            position: absolute;
            top: 12px;
            right: 14px;
            background: none;
            border: none;
            color: #64748b;
            font-size: 18px;
            cursor: pointer;
            line-height: 1;
            padding: 2px 6px;
            border-radius: 4px;
            transition: color 0.2s, background 0.2s;
        }
        .guest-toast-dismiss:hover { color: #f8fafc; background: rgba(255,255,255,0.06); }

        .guest-toast-icon {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            background: linear-gradient(135deg, rgba(245,158,11,0.2) 0%, rgba(251,191,36,0.1) 100%);
            border: 1px solid rgba(245,158,11,0.3);
            display: grid;
            place-items: center;
            font-size: 20px;
            margin-bottom: 12px;
            flex-shrink: 0;
        }
        .guest-toast-title {
            font-family: 'Outfit', sans-serif;
            font-weight: 700;
            font-size: 0.95rem;
            color: #f8fafc;
            margin-bottom: 6px;
            letter-spacing: -0.2px;
        }
        .guest-toast-desc {
            font-size: 0.82rem;
            color: #94a3b8;
            line-height: 1.55;
            margin-bottom: 16px;
        }
        .guest-toast-desc strong {
            color: #fbbf24;
        }
        .guest-toast-actions {
            display: flex;
            gap: 8px;
        }
        .btn-apply-admin {
            flex: 1;
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            color: #0d1117;
            font-weight: 700;
            font-size: 0.83rem;
            border: none;
            border-radius: 8px;
            padding: 10px 14px;
            cursor: pointer;
            font-family: inherit;
            transition: opacity 0.2s, transform 0.15s;
            text-align: center;
        }
        .btn-apply-admin:hover { opacity: 0.92; transform: translateY(-1px); }
        .btn-apply-admin:active { transform: translateY(0); }
        .btn-dismiss-soft {
            background: rgba(255,255,255,0.04);
            color: #64748b;
            font-weight: 600;
            font-size: 0.82rem;
            border: 1px solid rgba(255,255,255,0.08);
            border-radius: 8px;
            padding: 10px 14px;
            cursor: pointer;
            font-family: inherit;
            transition: all 0.2s;
        }
        .btn-dismiss-soft:hover { background: rgba(255,255,255,0.08); color: #94a3b8; }

        /* Pending status variant */
        .guest-toast.pending {
            border-color: rgba(16,185,129,0.3);
            box-shadow: 0 24px 60px rgba(0,0,0,0.55), 0 0 0 1px rgba(16,185,129,0.06);
        }
        .guest-toast.pending .guest-toast-icon {
            background: linear-gradient(135deg, rgba(16,185,129,0.2) 0%, rgba(52,211,153,0.1) 100%);
            border-color: rgba(16,185,129,0.3);
        }
        .guest-toast.pending .guest-toast-desc strong { color: #34d399; }
        .pending-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 0.78rem;
            font-weight: 600;
            color: #34d399;
            background: rgba(16,185,129,0.1);
            border: 1px solid rgba(16,185,129,0.2);
            border-radius: 20px;
            padding: 5px 12px;
            width: 100%;
            justify-content: center;
        }
        .pending-dot {
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background: #10b981;
            animation: pulse 1.8s infinite;
        }
        /* ===========================
           GUEST BLOCKED MODAL
           =========================== */
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(15, 23, 42, 0.75);
            backdrop-filter: blur(8px);
            z-index: 10000;
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.3s ease;
        }
        .modal-overlay.active {
            opacity: 1;
            pointer-events: auto;
        }
        .modal-box {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            border: 1px solid rgba(239, 68, 68, 0.3);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5), 0 0 0 1px rgba(239, 68, 68, 0.1);
            border-radius: 20px;
            width: 90%;
            max-width: 460px;
            padding: 32px;
            text-align: center;
            transform: scale(0.9);
            transition: transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
        }
        .modal-overlay.active .modal-box {
            transform: scale(1);
        }
        .modal-icon {
            font-size: 48px;
            margin-bottom: 20px;
            display: inline-block;
            animation: shake 0.5s ease-in-out;
        }
        .modal-title {
            font-family: 'Outfit', sans-serif;
            font-size: 1.3rem;
            font-weight: 700;
            color: #f8fafc;
            margin-bottom: 12px;
        }
        .modal-text {
            font-size: 0.9rem;
            color: #94a3b8;
            line-height: 1.6;
            margin-bottom: 24px;
        }
        .modal-text strong {
            color: #ef4444;
        }
        .modal-close-btn {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
            color: white;
            font-weight: 700;
            font-size: 0.9rem;
            border: none;
            border-radius: 10px;
            padding: 12px 24px;
            cursor: pointer;
            width: 100%;
            transition: opacity 0.2s, transform 0.1s;
            box-shadow: 0 4px 12px rgba(239, 68, 68, 0.25);
        }
        .modal-close-btn:hover {
            opacity: 0.95;
            transform: translateY(-1px);
        }
        .modal-close-btn:active {
            transform: translateY(0);
        }
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-6px); }
            75% { transform: translateX(6px); }
        }
    </style>
</head>
<body>
    <!-- Mobile Header -->
    <div class="mobile-header">
        <div class="mobile-brand">
            <span class="brand-mark" style="width: 32px; height: 32px; font-size: 0.95rem; border-radius: 6px; box-shadow: none;">MS</span>
            <strong>MitraSpace</strong>
        </div>
        <button class="hamburger-btn" id="hamburgerBtn" aria-label="Buka Menu">
            <svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2.5" fill="none" stroke-linecap="round" stroke-linejoin="round">
                <line x1="3" y1="12" x2="21" y2="12"></line>
                <line x1="3" y1="6" x2="21" y2="6"></line>
                <line x1="3" y1="18" x2="21" y2="18"></line>
            </svg>
        </button>
    </div>

    <!-- Sidebar Overlay -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <aside class="sidebar" id="sidebar">
        <div style="display: flex; flex-direction: column; gap: 4px; height: 100%;">
            <div class="brand" style="display: flex; justify-content: space-between; align-items: center;">
                <div style="display: flex; gap: 12px; align-items: center;">
                    <span class="brand-mark">MS</span>
                    <div>
                        <strong>MitraSpace</strong>
                        <small>Seller Center</small>
                    </div>
                </div>
                <!-- Close Button on Mobile Sidebar -->
                <button id="sidebarCloseBtn" class="hamburger-btn" style="padding: 4px; margin-left: 10px;" aria-label="Tutup Menu">
                    <svg viewBox="0 0 24 24" width="22" height="22" stroke="currentColor" stroke-width="2.5" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                </button>
            </div>

            <!-- Theme Toggle Selector Button -->
            <div class="theme-toggle-container">
                <button class="theme-switch-btn" id="themeToggleBtn" type="button" aria-label="Toggle Theme">
                    <div class="theme-switch-track">
                        <span class="theme-icon sun"><svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2.5" fill="none" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="5"></circle><line x1="12" y1="1" x2="12" y2="3"></line><line x1="12" y1="21" x2="12" y2="23"></line><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"></line><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"></line><line x1="1" y1="12" x2="3" y2="12"></line><line x1="21" y1="12" x2="23" y2="12"></line><line x1="4.22" y1="19.78" x2="5.64" y2="18.36"></line><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"></line></svg></span>
                        <span class="theme-icon moon"><svg viewBox="0 0 24 24" width="12" height="12" stroke="currentColor" stroke-width="2.5" fill="none" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path></svg></span>
                        <div class="theme-switch-thumb"></div>
                    </div>
                    <span class="theme-text" id="themeToggleText">Mode Gelap</span>
                </button>
            </div>
            <nav>
                <a href="{{ route('dashboard') }}" @class(['active' => request()->routeIs('dashboard')])>
                    <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" style="margin-right: 12px; flex-shrink: 0;"><rect x="3" y="3" width="7" height="9"></rect><rect x="14" y="3" width="7" height="5"></rect><rect x="14" y="12" width="7" height="9"></rect><rect x="3" y="16" width="7" height="5"></rect></svg> Dashboard
                </a>
                <a href="{{ route('items.index') }}" @class(['active' => request()->routeIs('items.*')])>
                    <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" style="margin-right: 12px; flex-shrink: 0;"><line x1="16.5" y1="9.4" x2="7.5" y2="4.21"></line><polygon points="12 22.08 12 12 3 6.92 3 17.08 12 22.08"></polygon><polygon points="12 12 21 6.92 21 17.08 12 22.08"></polygon><polygon points="12 1.92 21 6.92 12 12 3 6.92 12 1.92"></polygon><line x1="12" y1="22.08" x2="12" y2="12"></line></svg> Barang
                </a>
                <a href="{{ route('notifications.index') }}" @class(['active' => request()->routeIs('notifications.*')])>
                    <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" style="margin-right: 12px; flex-shrink: 0;"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path><path d="M13.73 21a2 2 0 0 1-3.46 0"></path></svg> Notifikasi
                </a>
                {{-- Orders & CRM: untuk sales_staff, super_admin, dan guest (read-only) --}}
                @if(Auth::user()->isSalesStaff() || Auth::user()->isSuperAdmin() || Auth::user()->role === 'guest')
                <a href="{{ route('orders.index') }}" @class(['active' => request()->routeIs('orders.*')])>
                    <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" style="margin-right: 12px; flex-shrink: 0;"><rect x="1" y="3" width="15" height="13"></rect><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"></polygon><circle cx="5.5" cy="18.5" r="2.5"></circle><circle cx="18.5" cy="18.5" r="2.5"></circle></svg> Tracking & Orders
                </a>
                <a href="{{ route('crm.index') }}" @class(['active' => request()->routeIs('crm.*')])>
                    <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" style="margin-right: 12px; flex-shrink: 0;"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg> CRM & Pelanggan
                </a>
                @endif
                {{-- Reports: untuk warehouse_staff, super_admin, dan guest (read-only) --}}
                @if(Auth::user()->isWarehouseStaff() || Auth::user()->isSuperAdmin() || Auth::user()->role === 'guest')
                <a href="{{ route('reports.index') }}" @class(['active' => request()->routeIs('reports.*')])>
                    <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" style="margin-right: 12px; flex-shrink: 0;"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"></polyline><polyline points="17 6 23 6 23 12"></polyline></svg> Laporan
                </a>
                @endif
                {{-- User Management: hanya super_admin --}}
                @if(Auth::user()->isSuperAdmin())
                <a href="{{ route('users.index') }}" @class(['active' => request()->routeIs('users.*')])>
                    <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" style="margin-right: 12px; flex-shrink: 0;"><circle cx="12" cy="12" r="3"></circle><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 1 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 1 1-2.83-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 1 1 2.83-2.83l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 1 1 2.83 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"></path></svg> Manajemen Pengguna
                </a>
                @endif
            </nav>
            {{-- Buyer Website Link (Demo Switcher) --}}
            <div style="padding: 12px 14px 0; border-top: 1px solid var(--line); margin-top: 8px;">
                <a href="https://mitraspace-buyer.vercel.app/" target="_blank" style="display: flex; align-items: center; gap: 10px; padding: 10px 12px; background: linear-gradient(135deg, rgba(239,68,68,0.15) 0%, rgba(249,115,22,0.1) 100%); border: 1px solid rgba(249,115,22,0.25); border-radius: 8px; color: #ff9d42; font-size: 0.85rem; font-weight: 600; text-decoration: none; transition: all 0.2s;" onmouseover="this.style.background='linear-gradient(135deg, rgba(239,68,68,0.25) 0%, rgba(249,115,22,0.18) 100%)'; this.style.borderColor='rgba(249,115,22,0.4)';" onmouseout="this.style.background='linear-gradient(135deg, rgba(239,68,68,0.15) 0%, rgba(249,115,22,0.1) 100%)'; this.style.borderColor='rgba(249,115,22,0.25)';">
                    <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink: 0;"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path><line x1="3" y1="6" x2="21" y2="6"></line><path d="M16 10a4 4 0 0 1-8 0"></path></svg> Ke Toko Buyer (Demo)
                </a>
                @auth
                <div class="user-profile" style="margin-top: 16px; border-top: 1px solid var(--line); padding-top: 16px;">
                    <div style="display: flex; align-items: center; gap: 10px;">
                        @if (Auth::user()->avatar)
                            <img src="{{ Auth::user()->avatar }}" alt="" class="profile-avatar">
                        @else
                            <div class="profile-avatar-placeholder">
                                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                            </div>
                        @endif
                        <div style="overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                            <strong class="profile-name">{{ Auth::user()->name }}</strong>
                            <small class="profile-email">{{ Auth::user()->email }}</small>
                            @if (Auth::user()->isSuperAdmin())
                                <span style="display: inline-block; font-size: 9px; padding: 2px 6px; background: rgba(6, 182, 212, 0.2); color: #22d3ee; border: 1px solid rgba(6, 182, 212, 0.4); border-radius: 4px; font-weight: 600;">Super Admin</span>
                            @elseif (Auth::user()->isWarehouseStaff())
                                <span style="display: inline-block; font-size: 9px; padding: 2px 6px; background: rgba(34, 197, 94, 0.2); color: #4ade80; border: 1px solid rgba(34, 197, 94, 0.4); border-radius: 4px; font-weight: 600;">Staff Gudang</span>
                            @elseif (Auth::user()->isSalesStaff())
                                <span style="display: inline-block; font-size: 9px; padding: 2px 6px; background: rgba(249, 115, 22, 0.2); color: #fb923c; border: 1px solid rgba(249, 115, 22, 0.4); border-radius: 4px; font-weight: 600;">Staff Penjualan</span>
                            @elseif (Auth::user()->role === 'guest')
                                <span style="display: inline-flex; align-items: center; gap: 4px; font-size: 9px; padding: 2px 6px; background: rgba(245, 158, 11, 0.2); color: #fbbf24; border: 1px solid rgba(245, 158, 11, 0.4); border-radius: 4px; font-weight: 600;">
                                    <svg viewBox="0 0 24 24" width="10" height="10" stroke="currentColor" stroke-width="2.5" fill="none" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink: 0;"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg> Guest (Demo)
                                </span>
                            @endif
                        </div>
                    </div>
                    <form method="POST" action="{{ route('logout') }}" style="margin: 0; width: 100%;">
                        @csrf
                        <button type="submit" class="logout-btn">
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
            </div>
        </div>
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

    {{-- ===== GUEST TOAST MODAL ===== --}}
    @auth
    @if(Auth::user()->role === 'guest')
        @php
            $requestStatus = Auth::user()->admin_request_status;
            $isPending = !empty($requestStatus) && str_starts_with($requestStatus, 'pending:');
            $requestedRole = $isPending ? str_replace('pending:', '', $requestStatus) : null;
            
            $requestedRoleLabel = match($requestedRole) {
                'super_admin' => 'Super Admin',
                'warehouse_staff' => 'Staff Gudang',
                'sales_staff' => 'Staff Penjualan',
                default => 'Admin'
            };
        @endphp
        @if($isPending)
            {{-- Already applied: show status --}}
            <div class="guest-toast pending" id="guestToast">
                <button class="guest-toast-dismiss" onclick="dismissToast()" title="Tutup">✕</button>
                <div class="guest-toast-icon">
                    <svg viewBox="0 0 24 24" width="20" height="20" stroke="currentColor" stroke-width="2.5" fill="none" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                </div>
                <div class="guest-toast-title">Pengajuan Sedang Ditinjau</div>
                <div class="guest-toast-desc">
                    Permintaan akses sebagai <strong>{{ $requestedRoleLabel }}</strong> sedang menunggu persetujuan dari <strong>Super Admin</strong>. Anda tetap dapat menjelajahi sistem dalam mode read-only.
                </div>
                <div class="pending-badge">
                    <span class="pending-dot"></span>
                    Menunggu Persetujuan Super Admin
                </div>
            </div>
        @else
            {{-- Not yet applied: show apply button --}}
            <div class="guest-toast" id="guestToast">
                <button class="guest-toast-dismiss" onclick="dismissToast()" title="Tutup">✕</button>
                <div class="guest-toast-icon">
                    <svg viewBox="0 0 24 24" width="20" height="20" stroke="currentColor" stroke-width="2.5" fill="none" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                </div>
                <div class="guest-toast-title">Anda Masuk Sebagai Guest</div>
                <div class="guest-toast-desc" style="margin-bottom: 12px;">
                    Mode demo aktif — Anda dapat <strong>melihat semua fitur</strong> namun tidak dapat melakukan perubahan data. Pilih role dan ajukan akses:
                </div>
                <form method="POST" action="{{ route('users.applyAdmin') }}" style="margin: 0; display: flex; flex-direction: column; gap: 10px;">
                    @csrf
                    <select name="role" required style="width: 100%; padding: 8px 10px; border-radius: 8px; background: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.12); color: #f8fafc; font-size: 0.82rem; outline: none; cursor: pointer; height: 36px;">
                        <option value="warehouse_staff" style="background: #111827; color: #f8fafc;">Staff Gudang (Kelola Barang & Laporan)</option>
                        <option value="sales_staff" style="background: #111827; color: #f8fafc;">Staff Penjualan (Kelola Order & CRM)</option>
                        <option value="super_admin" style="background: #111827; color: #f8fafc;">Super Admin (Akses Penuh)</option>
                    </select>
                    <div class="guest-toast-actions">
                        <button type="submit" class="btn-apply-admin" id="applyAdminBtn" style="height: 36px; padding: 0 14px;">
                            Ajukan Akses
                        </button>
                        <button type="button" class="btn-dismiss-soft" onclick="dismissToast()" style="height: 36px; padding: 0 14px;">Nanti</button>
                    </div>
                </form>
            </div>
        @endif

        <script>
            function dismissToast() {
                const toast = document.getElementById('guestToast');
                if (toast) {
                    toast.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
                    toast.style.opacity = '0';
                    toast.style.transform = 'translateY(16px) scale(0.96)';
                    setTimeout(() => toast.remove(), 300);
                }
                // Remember dismissal for this session
                sessionStorage.setItem('guestToastDismissed', '1');
            }

            // Auto-dismiss if already dismissed this session
            if (sessionStorage.getItem('guestToastDismissed') === '1') {
                const t = document.getElementById('guestToast');
                if (t) t.remove();
            }
        </script>
    @endif
    @endauth

    {{-- Modal Popup Peringatan untuk Guest --}}
    @if(session('guest_blocked'))
    <div class="modal-overlay active" id="guestBlockedModal">
        <div class="modal-box">
            <div class="modal-icon">
                <svg viewBox="0 0 24 24" width="48" height="48" stroke="#ef4444" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" style="display: block; margin: 0 auto;"><circle cx="12" cy="12" r="10"></circle><line x1="15" y1="9" x2="9" y2="15"></line></svg>
            </div>
            <h3 class="modal-title">Tindakan Dibatasi</h3>
            <p class="modal-text">
                Sebagai <strong>Guest (Demo)</strong>, Anda tidak diperbolehkan melakukan penambahan, perubahan, atau penghapusan data di dashboard ini.
            </p>
            <button class="modal-close-btn" onclick="closeBlockedModal()">Mengerti</button>
        </div>
    </div>
    <script>
        function closeBlockedModal() {
            const modal = document.getElementById('guestBlockedModal');
            if (modal) {
                modal.classList.remove('active');
                setTimeout(() => modal.remove(), 300);
            }
        }
        // Menutup modal jika area overlay di luar modal diklik
        document.getElementById('guestBlockedModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeBlockedModal();
            }
        });
    </script>
    @endif

    {{-- Mobile Nav, Theme Switcher & SPA Loader Script --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Sidebar Drawer Toggle Function
            const hamburgerBtn = document.getElementById('hamburgerBtn');
            const sidebarCloseBtn = document.getElementById('sidebarCloseBtn');
            const sidebar = document.getElementById('sidebar');
            const sidebarOverlay = document.getElementById('sidebarOverlay');

            if (hamburgerBtn && sidebar && sidebarOverlay) {
                hamburgerBtn.addEventListener('click', function() {
                    sidebar.classList.add('active');
                    sidebarOverlay.classList.add('active');
                });
            }

            function closeSidebar() {
                if (sidebar && sidebarOverlay) {
                    sidebar.classList.remove('active');
                    sidebarOverlay.classList.remove('active');
                }
            }

            if (sidebarCloseBtn) {
                sidebarCloseBtn.addEventListener('click', closeSidebar);
            }
            if (sidebarOverlay) {
                sidebarOverlay.addEventListener('click', closeSidebar);
            }

            // Theme Toggle Function
            const themeToggleBtn = document.getElementById('themeToggleBtn');
            const themeToggleText = document.getElementById('themeToggleText');
            if (themeToggleBtn && themeToggleText) {
                function updateThemeBtn(theme) {
                    if (theme === 'light') {
                        themeToggleText.textContent = 'Mode Terang';
                    } else {
                        themeToggleText.textContent = 'Mode Gelap';
                    }
                }

                // Initialize button state
                const currentTheme = document.documentElement.getAttribute('data-theme') || 'dark';
                updateThemeBtn(currentTheme);

                // Click handler
                themeToggleBtn.addEventListener('click', function() {
                    const activeTheme = document.documentElement.getAttribute('data-theme') === 'dark' ? 'light' : 'dark';
                    document.documentElement.setAttribute('data-theme', activeTheme);
                    localStorage.setItem('theme', activeTheme);
                    updateThemeBtn(activeTheme);
                });
            }

            // SPA-like AJAX Page Loader
            function loadPage(url, pushState = true) {
                // Get or create loading bar
                let progress = document.getElementById('spa-loading-bar');
                if (!progress) {
                    progress = document.createElement('div');
                    progress.id = 'spa-loading-bar';
                    document.body.appendChild(progress);
                }
                
                // Show loading bar
                progress.classList.add('active');
                progress.style.width = '30%';
                
                fetch(url)
                    .then(response => {
                        if (!response.ok) throw new Error('Network response was not ok');
                        return response.text();
                    })
                    .then(html => {
                        progress.style.width = '100%';
                        
                        const parser = new DOMParser();
                        const doc = parser.parseFromString(html, 'text/html');
                        
                        // Update title
                        document.title = doc.title;
                        
                        // Update main content
                        const main = document.querySelector('.main');
                        const newMain = doc.querySelector('.main');
                        if (main && newMain) {
                            main.innerHTML = newMain.innerHTML;
                        }
                        
                        // Update active state in sidebar nav
                        const currentActive = document.querySelector('nav a.active');
                        if (currentActive) currentActive.classList.remove('active');
                        
                        const sidebarLinks = document.querySelectorAll('nav a');
                        const currentPath = new URL(url).pathname;
                        sidebarLinks.forEach(link => {
                            const linkPath = new URL(link.href).pathname;
                            if (currentPath === linkPath || (currentPath.startsWith(linkPath) && linkPath !== '/')) {
                                link.classList.add('active');
                            }
                        });
                        
                        // Execute scripts inside the newly loaded main container
                        if (main) {
                            const scripts = main.querySelectorAll('script');
                            scripts.forEach(script => {
                                const newScript = document.createElement('script');
                                if (script.src) {
                                    newScript.src = script.src;
                                } else {
                                    newScript.textContent = script.textContent;
                                }
                                script.parentNode.replaceChild(newScript, script);
                            });
                        }
                        
                        // Update browser URL
                        if (pushState) {
                            window.history.pushState({ url: url }, doc.title, url);
                        }
                        
                        // Scroll to top
                        window.scrollTo({ top: 0, behavior: 'instant' });
                        
                        // Hide loading bar
                        setTimeout(() => {
                            progress.style.width = '0%';
                            progress.classList.remove('active');
                        }, 300);
                    })
                    .catch(err => {
                        console.error('SPA load failed, falling back to reload:', err);
                        window.location.href = url;
                    });
            }

            // Intercept internal links
            document.addEventListener('click', function(e) {
                const link = e.target.closest('a');
                if (!link) return;
                
                const href = link.getAttribute('href');
                if (!href || href.startsWith('#') || href.startsWith('javascript:') || link.getAttribute('target') === '_blank' || link.hasAttribute('download')) {
                    return;
                }
                
                const isInternal = link.href && link.href.startsWith(window.location.origin);
                if (isInternal) {
                    if (link.closest('form')) return; // let form actions handle themselves
                    
                    e.preventDefault();
                    loadPage(link.href);
                    
                    // Close sidebar if open on mobile
                    const sidebar = document.getElementById('sidebar');
                    const sidebarOverlay = document.getElementById('sidebarOverlay');
                    if (sidebar && sidebar.classList.contains('active')) {
                        sidebar.classList.remove('active');
                        if (sidebarOverlay) sidebarOverlay.classList.remove('active');
                    }
                }
            });

            // Intercept internal GET form submissions
            document.addEventListener('submit', function(e) {
                const form = e.target;
                if (form.method.toLowerCase() === 'get') {
                    const action = form.getAttribute('action') || window.location.pathname;
                    const isInternal = action.startsWith('/') || action.startsWith(window.location.origin);
                    if (isInternal) {
                        e.preventDefault();
                        const formData = new FormData(form);
                        const params = new URLSearchParams(formData);
                        // Filter empty parameters
                        for (const [key, value] of [...params.entries()]) {
                            if (!value) params.delete(key);
                        }
                        const url = new URL(action, window.location.origin);
                        url.search = params.toString();
                        loadPage(url.toString());
                    }
                }
            });

            // Handle back/forward navigation
            window.addEventListener('popstate', function(e) {
                if (e.state && e.state.url) {
                    loadPage(e.state.url, false);
                } else {
                    loadPage(window.location.href, false);
                }
            });
        });
    </script>
</body>
</html>
