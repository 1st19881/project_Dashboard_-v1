<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'หน้าหลัก') - Employee Portal</title>
    <link rel="icon" type="image/png" href="{{ asset('storage/images/logo.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Noto+Sans+Thai:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --sidebar-width: 272px;
            --topbar-height: 64px;
            --primary: #10b981;
            --primary-dark: #059669;
            --primary-light: #34d399;
            --primary-subtle: rgba(16, 185, 129, 0.08);
            --bg-body: #0f172a;
            --bg-sidebar: #111827;
            --bg-topbar: rgba(17, 24, 39, 0.8);
            --bg-card: rgba(30, 41, 59, 0.6);
            --bg-card-hover: rgba(30, 41, 59, 0.8);
            --text-primary: #f1f5f9;
            --text-secondary: #94a3b8;
            --text-muted: #64748b;
            --border-color: rgba(148, 163, 184, 0.1);
            --border-light: rgba(148, 163, 184, 0.06);
            --success: #22c55e;
            --warning: #f59e0b;
            --danger: #ef4444;
            --info: #3b82f6;
            --shadow-sm: 0 1px 2px rgba(0,0,0,0.2);
            --shadow-md: 0 4px 12px rgba(0,0,0,0.2);
            --shadow-lg: 0 8px 32px rgba(0,0,0,0.3);
            --radius-sm: 8px;
            --radius-md: 12px;
            --radius-lg: 16px;
            --transition: 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        }

        body {
            font-family: 'Inter', 'Noto Sans Thai', sans-serif;
            background: var(--bg-body);
            color: var(--text-primary);
            min-height: 100vh;
            overflow-x: hidden;
        }

        /* ===== SIDEBAR ===== */
        .sidebar {
            position: fixed; top: 0; left: 0;
            width: var(--sidebar-width); height: 100vh;
            background: var(--bg-sidebar);
            border-right: 1px solid var(--border-color);
            z-index: 100;
            display: flex; flex-direction: column;
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            overflow: hidden;
        }
        .sidebar-brand {
            height: var(--topbar-height);
            display: flex; align-items: center;
            padding: 0 20px; gap: 14px;
            border-bottom: 1px solid var(--border-color);
            flex-shrink: 0;
        }
        .sidebar-brand-icon {
            width: 40px; height: 40px;
            background: linear-gradient(135deg, var(--primary), #06b6d4);
            border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
        }
        .sidebar-brand-icon svg { width: 22px; height: 22px; color: white; }
        .sidebar-brand-text h2 { font-size: 16px; font-weight: 700; color: var(--text-primary); white-space: nowrap; }
        .sidebar-brand-text span { font-size: 11px; color: var(--text-muted); white-space: nowrap; }

        .sidebar-nav { flex: 1; overflow-y: auto; padding: 16px 12px; }
        .sidebar-nav::-webkit-scrollbar { width: 4px; }
        .sidebar-nav::-webkit-scrollbar-thumb { background: rgba(148,163,184,0.2); border-radius: 4px; }

        .nav-section { margin-bottom: 24px; }
        .nav-section-title {
            font-size: 11px; font-weight: 600; color: var(--text-muted);
            text-transform: uppercase; letter-spacing: 1px;
            padding: 0 12px; margin-bottom: 8px;
        }
        .nav-item {
            display: flex; align-items: center; gap: 12px;
            padding: 10px 12px; border-radius: var(--radius-sm);
            color: var(--text-secondary); text-decoration: none;
            font-size: 14px; font-weight: 500;
            transition: all var(--transition);
            margin-bottom: 2px; cursor: pointer; position: relative;
        }
        .nav-item:hover { background: var(--primary-subtle); color: var(--text-primary); }
        .nav-item.active {
            background: linear-gradient(135deg, rgba(16,185,129,0.15), rgba(6,182,212,0.1));
            color: var(--primary-light);
        }
        .nav-item.active::before {
            content: ''; position: absolute; left: 0; top: 6px; bottom: 6px;
            width: 3px; background: var(--primary); border-radius: 0 3px 3px 0;
        }
        .nav-item svg { width: 20px; height: 20px; flex-shrink: 0; opacity: 0.7; }
        .nav-item.active svg, .nav-item:hover svg { opacity: 1; }

        .sidebar-footer { padding: 16px 12px; border-top: 1px solid var(--border-color); flex-shrink: 0; }

        /* ===== TOPBAR ===== */
        .topbar {
            position: fixed; top: 0; left: var(--sidebar-width); right: 0;
            height: var(--topbar-height);
            background: var(--bg-topbar);
            backdrop-filter: blur(20px);
            border-bottom: 1px solid var(--border-color);
            display: flex; align-items: center; justify-content: space-between;
            padding: 0 28px; z-index: 90;
            transition: left 0.3s;
        }
        .topbar-left { display: flex; align-items: center; gap: 16px; }
        .topbar-toggle {
            display: none; background: none; border: none;
            color: var(--text-secondary); cursor: pointer; padding: 8px;
            border-radius: var(--radius-sm); transition: all var(--transition);
        }
        .topbar-toggle:hover { background: var(--primary-subtle); color: var(--text-primary); }
        .topbar-toggle svg { width: 22px; height: 22px; }
        .topbar-title { font-size: 18px; font-weight: 600; }

        .topbar-right { display: flex; align-items: center; gap: 16px; }
        .user-menu { position: relative; }
        .user-trigger {
            display: flex; align-items: center; gap: 12px; cursor: pointer;
            padding: 6px 12px; border-radius: var(--radius-sm);
            transition: all var(--transition);
        }
        .user-trigger:hover { background: var(--primary-subtle); }
        .user-avatar {
            width: 36px; height: 36px;
            background: linear-gradient(135deg, var(--primary), #06b6d4);
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            font-size: 14px; font-weight: 700; color: white;
        }
        .user-info { display: flex; flex-direction: column; }
        .user-name { font-size: 13px; font-weight: 600; color: var(--text-primary); }
        .user-role { font-size: 11px; color: var(--text-muted); }
        .user-chevron svg { width: 16px; height: 16px; color: var(--text-muted); }

        .user-dropdown {
            position: absolute; right: 0; top: calc(100% + 8px);
            background: var(--bg-sidebar); border: 1px solid var(--border-color);
            border-radius: var(--radius-md); min-width: 200px;
            box-shadow: var(--shadow-lg); opacity: 0; visibility: hidden;
            transform: translateY(-8px); transition: all 0.2s;
        }
        .user-dropdown.show { opacity: 1; visibility: visible; transform: translateY(0); }
        .dropdown-item {
            display: flex; align-items: center; gap: 10px;
            padding: 10px 16px; font-size: 13px; color: var(--text-secondary);
            cursor: pointer; transition: all var(--transition);
            border: none; background: none; width: 100%; font-family: inherit;
        }
        .dropdown-item:hover { background: var(--primary-subtle); color: var(--text-primary); }
        .dropdown-item.danger { color: #fca5a5; }
        .dropdown-item.danger:hover { background: rgba(239,68,68,0.1); }
        .dropdown-item svg { width: 18px; height: 18px; }
        .dropdown-divider { height: 1px; background: var(--border-color); margin: 4px 0; }

        /* ===== OVERLAY ===== */
        .sidebar-overlay {
            display: none; position: fixed; inset: 0;
            background: rgba(0,0,0,0.5); z-index: 99;
        }
        .sidebar-overlay.show { display: block; }

        /* ===== MAIN CONTENT ===== */
        .main-content {
            margin-left: var(--sidebar-width);
            padding-top: var(--topbar-height);
            min-height: 100vh;
            transition: margin-left 0.3s;
        }
        .content-wrapper { padding: 28px; }

        /* ===== TOAST ===== */
        .toast {
            position: fixed; top: 20px; right: 20px; z-index: 9999;
            padding: 14px 20px; border-radius: var(--radius-md);
            font-size: 13px; font-weight: 500;
            display: flex; align-items: center; gap: 10px;
            box-shadow: var(--shadow-lg);
            animation: slideIn 0.3s ease;
        }
        .toast svg { width: 20px; height: 20px; flex-shrink: 0; }
        .toast-success { background: rgba(34,197,94,0.15); border: 1px solid rgba(34,197,94,0.3); color: #86efac; }
        .toast-error { background: rgba(239,68,68,0.15); border: 1px solid rgba(239,68,68,0.3); color: #fca5a5; }
        @keyframes slideIn { from { opacity:0; transform:translateX(30px); } to { opacity:1; transform:translateX(0); } }

        /* ===== RESPONSIVE ===== */
        @media (max-width: 1024px) {
            .sidebar { transform: translateX(-100%); }
            .sidebar.open { transform: translateX(0); }
            .topbar { left: 0; }
            .topbar-toggle { display: block; }
            .main-content { margin-left: 0; }
        }
    </style>
    @stack('head')
</head>
<body>
    <!-- Sidebar -->
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-brand">
            <div class="sidebar-brand-icon">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                </svg>
            </div>
            <div class="sidebar-brand-text">
                <h2>Employee Portal</h2>
                <span>ระบบสำหรับพนักงาน</span>
            </div>
        </div>

                <!-- User Info Card -->
        <div style="padding:16px 12px; border-bottom:1px solid rgba(148,163,184,0.1); margin-bottom:8px;">
            <div style="display:flex; align-items:center; gap:10px;">
                <div style="width:40px; height:40px; background:linear-gradient(135deg,#10b981,#06b6d4); border-radius:10px; display:flex; align-items:center; justify-content:center; font-size:16px; font-weight:700; color:#fff;">
                    {{ mb_substr(session('employee.USERS_EMPCODE', 'E'), 0, 1) }}
                </div>
                <div>
                    <div style="font-size:13px; font-weight:600; color:#f1f5f9;">{{ session('employee.USERS_EMPCODE', '-') }} | {{ session('employee.USERS_FNAMETH', session('employee.USERS_FNAME', '')) }}</div>
                    <a href="{{ route('employee.dashboard') }}" style="font-size:11px; color:#34d399; text-decoration:none;">&#x1f464; ข้อมูลส่วนตัว</a>
                </div>
            </div>
        </div>

        <nav class="sidebar-nav">
            <div class="nav-section">
                <div class="nav-section-title">เมนู</div>

                <a href="{{ route('employee.dashboard') }}" class="nav-item {{ request()->routeIs('employee.dashboard') ? 'active' : '' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z" />
                    </svg>
                    หน้าหลัก
                </a>

                {{-- เพิ่มเมนูของโปรเจคใหม่ที่นี่ --}}
            </div>
        </nav>

        <div class="sidebar-footer">
            <form method="POST" action="{{ route('employee.logout') }}" id="sidebarLogoutForm">
                @csrf
                <button type="submit" class="nav-item" style="width:100%;border:none;background:none;">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" style="color:#fca5a5;">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9" />
                    </svg>
                    <span style="color:#fca5a5;">ออกจากระบบ</span>
                </button>
            </form>
        </div>
    </aside>

    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <!-- Topbar -->
    <header class="topbar" id="topbar">
        <div class="topbar-left">
            <button class="topbar-toggle" id="sidebarToggle" aria-label="Toggle sidebar">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                </svg>
            </button>
            <h1 class="topbar-title">@yield('title', 'หน้าหลัก')</h1>
        </div>

        <div class="topbar-right">
            <div class="user-menu" id="userMenu">
                <div class="user-trigger" id="userTrigger">
                    <div class="user-avatar">
                        {{ mb_substr(session('employee.USERS_FNAMETH', session('employee.USERS_FNAME', 'E')), 0, 1) }}
                    </div>
                    <div class="user-info">
                        <div class="user-name">{{ session('employee.USERS_FNAMETH', session('employee.USERS_FNAME', 'Employee')) }} {{ session('employee.USERS_LNAMETH', session('employee.USERS_LNAME', '')) }}</div>
                        <div class="user-role">{{ session('employee.USERS_EMPCODE', '-') }}</div>
                    </div>
                    <span class="user-chevron">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                        </svg>
                    </span>
                </div>
                <div class="user-dropdown" id="userDropdown">
                    <div class="dropdown-item" style="pointer-events:none;opacity:0.6;">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                        </svg>
                        {{ session('employee.USERS_USERNAME', '-') }}
                    </div>
                    <div class="dropdown-divider"></div>
                    <form method="POST" action="{{ route('employee.logout') }}" id="dropdownLogoutForm">
                        @csrf
                        <button type="submit" class="dropdown-item danger">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9" />
                            </svg>
                            ออกจากระบบ
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="main-content">
        @if(session('success'))
            <div class="toast toast-success" id="toast">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="toast toast-error" id="toast">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
                </svg>
                {{ session('error') }}
            </div>
        @endif

        <div class="content-wrapper">
            @yield('content')
        </div>
    </main>

    <script>
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('sidebarOverlay');
        const toggleBtn = document.getElementById('sidebarToggle');

        toggleBtn.addEventListener('click', () => {
            if (window.innerWidth <= 1024) {
                sidebar.classList.toggle('open');
                overlay.classList.toggle('show');
            }
        });
        overlay.addEventListener('click', () => {
            sidebar.classList.remove('open');
            overlay.classList.remove('show');
        });

        const userTrigger = document.getElementById('userTrigger');
        const userDropdown = document.getElementById('userDropdown');
        userTrigger.addEventListener('click', (e) => { e.stopPropagation(); userDropdown.classList.toggle('show'); });
        document.addEventListener('click', (e) => {
            if (!document.getElementById('userMenu').contains(e.target)) userDropdown.classList.remove('show');
        });

        const toast = document.getElementById('toast');
        if (toast) {
            setTimeout(() => {
                toast.style.transition = 'opacity 0.3s, transform 0.3s';
                toast.style.opacity = '0';
                toast.style.transform = 'translateX(30px)';
                setTimeout(() => toast.remove(), 300);
            }, 4000);
        }

        window.addEventListener('resize', () => {
            if (window.innerWidth > 1024) { sidebar.classList.remove('open'); overlay.classList.remove('show'); }
        });
    </script>
    @stack('scripts')
</body>
</html>
