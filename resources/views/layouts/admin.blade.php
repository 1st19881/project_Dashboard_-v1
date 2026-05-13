<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin') - Admin Panel</title>
    <link rel="icon" type="image/png" href="{{ asset('storage/images/logo.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Noto+Sans+Thai:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --sidebar-width: 272px;
            --sidebar-collapsed: 80px;
            --topbar-height: 64px;
            --primary: #6366f1;
            --primary-dark: #4f46e5;
            --primary-light: #818cf8;
            --primary-subtle: rgba(99, 102, 241, 0.08);
            --bg-body: #0f172a;
            --bg-sidebar: #111827;
            --bg-topbar: rgba(17, 24, 39, 0.8);
            --bg-input: rgba(15, 23, 42, 0.6);
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
            --radius-xl: 20px;
            --transition: 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        }

        body {
            font-family: 'Inter', 'Noto Sans Thai', sans-serif;
            background: var(--bg-body);
            color: var(--text-primary);
            min-height: 100vh;
            overflow-x: hidden;
        }

        /* Global Select overrides for Dark/Light mode */
        select option {
            background-color: var(--bg-card);
            color: var(--text-primary);
        }

        /* ==================== SIDEBAR ==================== */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: var(--sidebar-width);
            height: 100vh;
            background: var(--bg-sidebar);
            border-right: 1px solid var(--border-color);
            z-index: 100;
            display: flex;
            flex-direction: column;
            transition: width 0.3s cubic-bezier(0.4, 0, 0.2, 1),
                        transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            overflow: hidden;
        }

        .sidebar-brand {
            height: var(--topbar-height);
            display: flex;
            align-items: center;
            padding: 0 20px;
            gap: 14px;
            border-bottom: 1px solid var(--border-color);
            flex-shrink: 0;
        }
        .sidebar-brand-icon {
            width: 40px; height: 40px;
            background: linear-gradient(135deg, var(--primary), #8b5cf6);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        .sidebar-brand-icon svg { width: 22px; height: 22px; color: white; }
        .sidebar-brand-text h2 {
            font-size: 16px;
            font-weight: 700;
            color: var(--text-primary);
            white-space: nowrap;
        }
        .sidebar-brand-text span {
            font-size: 11px;
            color: var(--text-muted);
            white-space: nowrap;
        }

        .sidebar-nav {
            flex: 1;
            overflow-y: auto;
            overflow-x: hidden;
            padding: 16px 12px;
        }
        .sidebar-nav::-webkit-scrollbar { width: 4px; }
        .sidebar-nav::-webkit-scrollbar-track { background: transparent; }
        .sidebar-nav::-webkit-scrollbar-thumb { background: rgba(148,163,184,0.2); border-radius: 4px; }

        .nav-section {
            margin-bottom: 24px;
        }
        .nav-section-title {
            font-size: 11px;
            font-weight: 600;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 1px;
            padding: 0 12px;
            margin-bottom: 8px;
            white-space: nowrap;
        }

        .nav-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 12px;
            border-radius: var(--radius-sm);
            color: var(--text-secondary);
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            transition: all var(--transition);
            margin-bottom: 2px;
            white-space: nowrap;
            cursor: pointer;
            position: relative;
        }
        .nav-item:hover {
            background: var(--primary-subtle);
            color: var(--text-primary);
        }
        .nav-item.active {
            background: linear-gradient(135deg, rgba(99,102,241,0.15), rgba(139,92,246,0.1));
            color: var(--primary-light);
        }
        .nav-item.active::before {
            content: '';
            position: absolute;
            left: 0;
            top: 6px;
            bottom: 6px;
            width: 3px;
            background: var(--primary);
            border-radius: 0 3px 3px 0;
        }
        .nav-item svg {
            width: 20px; height: 20px;
            flex-shrink: 0;
            opacity: 0.7;
        }
        .nav-item.active svg,
        .nav-item:hover svg { opacity: 1; }
        .nav-badge {
            margin-left: auto;
            background: var(--danger);
            color: white;
            font-size: 11px;
            font-weight: 600;
            padding: 2px 8px;
            border-radius: 20px;
            min-width: 20px;
            text-align: center;
        }

        /* Sidebar Group / Dropdown */
        .nav-group { margin-bottom: 2px; }
        .nav-group-toggle {
            display: flex; align-items: center; gap: 12px;
            padding: 10px 12px; border-radius: var(--radius-sm);
            color: var(--text-secondary); text-decoration: none;
            font-size: 14px; font-weight: 500; transition: all var(--transition);
            cursor: pointer; white-space: nowrap; user-select: none;
        }
        .nav-group-toggle:hover { background: var(--primary-subtle); color: var(--text-primary); }
        .nav-group-toggle svg.icon { width: 20px; height: 20px; flex-shrink: 0; opacity: 0.7; }
        .nav-group.open .nav-group-toggle, .nav-group-toggle:hover svg.icon { opacity: 1; }
        .nav-group.open .nav-group-toggle { color: var(--primary-light); }
        .nav-group.open .nav-group-toggle svg.icon { opacity: 1; color: var(--primary-light); }
        .nav-group-toggle .chevron { margin-left: auto; width: 14px; height: 14px; transition: transform 0.2s; opacity: 0.5; }
        .nav-group.open .nav-group-toggle .chevron { transform: rotate(90deg); opacity: 1; }
        
        .nav-group-items {
            display: none; padding-left: 38px; margin-top: 4px;
            flex-direction: column; gap: 2px; margin-bottom: 8px;
        }
        .nav-group.open .nav-group-items { display: flex; }
        .nav-group-item {
            color: var(--text-secondary); text-decoration: none;
            font-size: 13px; font-weight: 500; padding: 6px 12px;
            border-radius: var(--radius-sm); transition: all var(--transition);
        }
        .nav-group-item:hover { color: var(--text-primary); background: rgba(255,255,255,0.03); }
        .nav-group-item.active { color: var(--primary-light); font-weight: 600; background: rgba(255,255,255,0.06); }

        .sidebar-footer {
            padding: 16px 12px;
            border-top: 1px solid var(--border-color);
            flex-shrink: 0;
        }

        /* ==================== TOPBAR ==================== */
        .topbar {
            position: fixed;
            top: 0;
            left: var(--sidebar-width);
            right: 0;
            height: var(--topbar-height);
            background: var(--bg-topbar);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-bottom: 1px solid var(--border-color);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 28px;
            z-index: 90;
            transition: left 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .topbar-left {
            display: flex;
            align-items: center;
            gap: 16px;
        }
        .topbar-toggle {
            width: 36px; height: 36px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: none;
            border: 1px solid var(--border-color);
            border-radius: var(--radius-sm);
            color: var(--text-secondary);
            cursor: pointer;
            transition: all var(--transition);
        }
        .topbar-toggle:hover {
            background: var(--primary-subtle);
            color: var(--text-primary);
            border-color: rgba(99,102,241,0.3);
        }
        .topbar-toggle svg { width: 20px; height: 20px; }
        .topbar-title {
            font-size: 18px;
            font-weight: 600;
            color: var(--text-primary);
        }

        .topbar-right {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        /* User dropdown */
        .user-menu {
            position: relative;
        }
        .user-trigger {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 6px 12px 6px 6px;
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 28px;
            cursor: pointer;
            transition: all var(--transition);
        }
        .user-trigger:hover {
            background: var(--bg-card-hover);
            border-color: rgba(99,102,241,0.3);
        }
        .user-avatar {
            width: 32px; height: 32px;
            background: linear-gradient(135deg, var(--primary), #8b5cf6);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 13px;
            font-weight: 700;
            color: white;
        }
        .user-info { text-align: left; }
        .user-name {
            font-size: 13px;
            font-weight: 600;
            color: var(--text-primary);
            line-height: 1.2;
        }
        .user-role {
            font-size: 11px;
            color: var(--text-muted);
        }
        .user-chevron {
            color: var(--text-muted);
            transition: transform var(--transition);
        }
        .user-chevron svg { width: 16px; height: 16px; }

        .user-dropdown {
            position: absolute;
            top: calc(100% + 8px);
            right: 0;
            width: 200px;
            background: var(--bg-sidebar);
            border: 1px solid var(--border-color);
            border-radius: var(--radius-md);
            padding: 8px;
            box-shadow: var(--shadow-lg);
            opacity: 0;
            visibility: hidden;
            transform: translateY(-8px);
            transition: all 0.2s;
            z-index: 200;
        }
        .user-dropdown.show {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }
        .dropdown-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 12px;
            border-radius: var(--radius-sm);
            color: var(--text-secondary);
            text-decoration: none;
            font-size: 13px;
            font-weight: 500;
            transition: all var(--transition);
            border: none;
            background: none;
            width: 100%;
            cursor: pointer;
            font-family: inherit;
        }
        .dropdown-item:hover { background: var(--primary-subtle); color: var(--text-primary); }
        .dropdown-item svg { width: 18px; height: 18px; opacity: 0.7; }
        .dropdown-item.danger { color: #fca5a5; }
        .dropdown-item.danger:hover { background: rgba(239,68,68,0.1); }
        .dropdown-divider { height: 1px; background: var(--border-color); margin: 4px 0; }

        /* ==================== MAIN CONTENT ==================== */
        .main-content {
            margin-left: var(--sidebar-width);
            padding-top: var(--topbar-height);
            min-height: 100vh;
            transition: margin-left 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .content-wrapper {
            padding: 28px;
        }

        /* ==================== MOBILE OVERLAY ==================== */
        .sidebar-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.5);
            backdrop-filter: blur(4px);
            z-index: 95;
        }

        /* ==================== RESPONSIVE ==================== */
        @media (max-width: 1024px) {
            .sidebar {
                transform: translateX(-100%);
            }
            .sidebar.open {
                transform: translateX(0);
            }
            .topbar { left: 0; }
            .main-content { margin-left: 0; }
            .sidebar-overlay.show { display: block; }
        }

        /* ==================== ALERT TOAST ==================== */
        .toast {
            position: fixed;
            top: 80px;
            right: 28px;
            z-index: 999;
            padding: 14px 20px;
            border-radius: var(--radius-md);
            font-size: 14px;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 10px;
            animation: toastIn 0.4s cubic-bezier(0.16, 1, 0.3, 1);
            box-shadow: var(--shadow-lg);
        }
        .toast-success {
            background: rgba(34, 197, 94, 0.15);
            border: 1px solid rgba(34, 197, 94, 0.3);
            color: #86efac;
        }
        .toast-error {
            background: rgba(239, 68, 68, 0.15);
            border: 1px solid rgba(239, 68, 68, 0.3);
            color: #fca5a5;
        }
        .toast svg { width: 20px; height: 20px; flex-shrink: 0; }
        @keyframes toastIn {
            from { opacity: 0; transform: translateX(30px); }
            to { opacity: 1; transform: translateX(0); }
        }

        /* ==================== UTILITY ==================== */
        .text-muted { color: var(--text-muted); }
        .text-secondary { color: var(--text-secondary); }
        .text-success { color: var(--success); }
        .text-warning { color: var(--warning); }
        .text-danger { color: var(--danger); }

        /* ==================== LIGHT MODE ==================== */
        body.light-mode {
            --bg-body: #f8fafc;
            --bg-sidebar: #ffffff;
            --bg-topbar: rgba(255, 255, 255, 0.95);
            --bg-input: #ffffff;
            --bg-card: #ffffff;
            --bg-card-hover: #f1f5f9;
            --text-primary: #0f172a;
            --text-secondary: #334155;
            --text-muted: #64748b;
            --border-color: #e2e8f0;
            --border-light: rgba(0, 0, 0, 0.05);
            --primary-subtle: rgba(99, 102, 241, 0.1);
        }
        body.light-mode .topbar { border-bottom: 1px solid var(--border-color); box-shadow: 0 1px 3px rgba(0,0,0,0.03); }
        body.light-mode .sidebar-brand { border-bottom: 1px solid var(--border-color); }
        
        .theme-toggle {
            background: rgba(148, 163, 184, 0.1); border: 1px solid var(--border-color); color: var(--text-secondary);
            border-radius: 50%; width: 36px; height: 36px; display: flex; align-items: center; justify-content: center;
            cursor: pointer; transition: all var(--transition);
        }
        .theme-toggle:hover { background: rgba(148, 163, 184, 0.2); color: var(--primary); }
        .theme-toggle svg { width: 18px; height: 18px; }
        
        body:not(.light-mode) .icon-sun { display: none; }
        body.light-mode .icon-moon { display: none; }

        @yield('styles')
    </style>
    @stack('head')
</head>
<body>
    <!-- Sidebar -->
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-brand">
            <div class="sidebar-brand-icon">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 3v11.25A2.25 2.25 0 006 16.5h2.25M3.75 3h-1.5m1.5 0h16.5m0 0h1.5m-1.5 0v11.25A2.25 2.25 0 0118 16.5h-2.25m-7.5 0h7.5m-7.5 0l-1 3m8.5-3l1 3m0 0l.5 1.5m-.5-1.5h-9.5m0 0l-.5 1.5M9 11.25v1.5M12 9v3.75m3-6v6" />
                </svg>
            </div>
            <div class="sidebar-brand-text">
                <h2>Admin Panel</h2>
                <span>Management System</span>
            </div>
        </div>

        <nav class="sidebar-nav">
            <div class="nav-section">
                <div class="nav-section-title">เมนูหลัก</div>

                <a href="{{ route('admin.dashboard') }}" class="nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z" />
                    </svg>
                    แดชบอร์ด
                </a>

                {{-- เพิ่มเมนูของโปรเจคใหม่ที่นี่ --}}
            </div>
        </nav>

        <div class="sidebar-footer">
            <form method="POST" action="{{ route('logout') }}" id="sidebarLogoutForm">
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

    <!-- Mobile Overlay -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <!-- Topbar -->
    <header class="topbar" id="topbar">
        <div class="topbar-left">
            <button class="topbar-toggle" id="sidebarToggle" aria-label="Toggle sidebar">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                </svg>
            </button>
            <h1 class="topbar-title">@yield('title', 'แดชบอร์ด')</h1>
        </div>

        <div class="topbar-right" style="display:flex; align-items:center; gap:16px;">
            <!-- Theme Toggle -->
            <button class="theme-toggle" id="themeToggleBtn" title="สลับโหมดหน้าจอ">
                <svg class="icon-sun" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v2.25m6.364.386l-1.591 1.591M21 12h-2.25m-.386 6.364l-1.591-1.591M12 18.75V21m-4.773-2.227l1.591-1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0z" />
                </svg>
                <svg class="icon-moon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21.752 15.002A9.718 9.718 0 0118 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753 9.753 0 003 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753 0 009.002-5.998z" />
                </svg>
            </button>

            <!-- User Menu -->
            <div class="user-menu" id="userMenu">
                <div class="user-trigger" id="userTrigger">
                    <div class="user-avatar">
                        {{ mb_substr(session('admin.NAMETH', 'A'), 0, 1) }}
                    </div>
                    <div class="user-info">
                        <div class="user-name">{{ session('admin.NAMETH', 'Admin') }}</div>
                        <div class="user-role">{{ session('admin.USER_LEVEL', 'admin') }}</div>
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
                        ID: {{ session('admin.USERNAME', '-') }}
                    </div>
                    <div class="dropdown-divider"></div>
                    <form method="POST" action="{{ route('logout') }}" id="dropdownLogoutForm">
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
        <!-- Toast Messages -->
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
        // Init theme from local storage
        if (localStorage.getItem('theme') === 'light') {
            document.body.classList.add('light-mode');
        }

        // Theme toggle logic
        const themeToggleBtn = document.getElementById('themeToggleBtn');
        if (themeToggleBtn) {
            themeToggleBtn.addEventListener('click', () => {
                document.body.classList.toggle('light-mode');
                if (document.body.classList.contains('light-mode')) {
                    localStorage.setItem('theme', 'light');
                } else {
                    localStorage.setItem('theme', 'dark');
                }
            });
        }

        // Sidebar toggle
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

        // User dropdown
        const userTrigger = document.getElementById('userTrigger');
        const userDropdown = document.getElementById('userDropdown');

        userTrigger.addEventListener('click', (e) => {
            e.stopPropagation();
            userDropdown.classList.toggle('show');
        });

        document.addEventListener('click', (e) => {
            if (!document.getElementById('userMenu').contains(e.target)) {
                userDropdown.classList.remove('show');
            }
        });

        // Toast auto-hide
        const toast = document.getElementById('toast');
        if (toast) {
            setTimeout(() => {
                toast.style.transition = 'opacity 0.3s, transform 0.3s';
                toast.style.opacity = '0';
                toast.style.transform = 'translateX(30px)';
                setTimeout(() => toast.remove(), 300);
            }, 4000);
        }

        // Responsive check
        window.addEventListener('resize', () => {
            if (window.innerWidth > 1024) {
                sidebar.classList.remove('open');
                overlay.classList.remove('show');
            }
        });
    </script>
    <!-- jQuery + DataTables CDN -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/jquery.dataTables.min.css">
    <script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
    <style>
        /* DataTables Dark Theme Override */
        .dataTables_wrapper .dataTables_length select,
        .dataTables_wrapper .dataTables_filter input {
            background: var(--bg-input) !important; color: var(--text-primary) !important;
            border: 1px solid var(--border-color) !important; border-radius: 6px !important; padding: 6px 10px !important; outline:none !important;
        }
        .dataTables_wrapper .dataTables_length label,
        .dataTables_wrapper .dataTables_filter label,
        .dataTables_wrapper .dataTables_info,
        .dataTables_wrapper .dataTables_paginate { color: var(--text-secondary) !important; font-size: 13px !important; }
        .dataTables_wrapper .dataTables_paginate .paginate_button { color: var(--text-secondary) !important; border: 1px solid var(--border-color) !important; border-radius: 4px !important; background: transparent !important; margin: 0 2px !important; }
        .dataTables_wrapper .dataTables_paginate .paginate_button.current { background: var(--primary) !important; color: white !important; border-color: var(--primary) !important; }
        .dataTables_wrapper .dataTables_paginate .paginate_button:hover { background: var(--primary-subtle) !important; color: var(--primary-light) !important; }
        table.dataTable thead th { border-bottom-color: var(--border-color) !important; }
        table.dataTable tbody td { border-bottom-color: var(--border-light) !important; }
        table.dataTable.no-footer { border-bottom-color: var(--border-color) !important; }
        .dataTables_wrapper { padding: 8px 0; }
    </style>
    @stack('scripts')
</body>
</html>
