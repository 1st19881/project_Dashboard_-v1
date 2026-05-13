@extends('layouts.admin')

@section('title', 'แดชบอร์ด')

@push('head')
<style>
    .welcome-banner {
        background: linear-gradient(135deg, rgba(99,102,241,0.12), rgba(139,92,246,0.08));
        border: 1px solid rgba(99,102,241,0.2);
        border-radius: 16px;
        padding: 32px;
        margin-bottom: 28px;
    }
    .welcome-banner h2 { font-size: 24px; font-weight: 700; margin-bottom: 8px; }
    .welcome-banner p { color: var(--text-secondary); font-size: 14px; }
    .welcome-banner .welcome-date {
        margin-top: 12px; font-size: 13px; color: var(--text-muted);
        display: flex; align-items: center; gap: 8px;
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
        gap: 20px;
        margin-bottom: 28px;
    }
    .stat-card {
        background: var(--bg-card);
        border: 1px solid var(--border-color);
        border-radius: 16px;
        padding: 24px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        transition: all 0.2s;
    }
    .stat-card:hover {
        background: var(--bg-card-hover);
        transform: translateY(-2px);
        box-shadow: var(--shadow-md);
    }
    .stat-info h3 { font-size: 13px; font-weight: 500; color: var(--text-secondary); margin-bottom: 8px; }
    .stat-value { font-size: 32px; font-weight: 800; letter-spacing: -0.02em; }
    .stat-sub { font-size: 12px; color: var(--text-muted); margin-top: 4px; }
    .stat-icon {
        width: 52px; height: 52px;
        border-radius: 14px;
        display: flex; align-items: center; justify-content: center;
    }
    .stat-icon svg { width: 26px; height: 26px; }
    .stat-icon.indigo { background: rgba(99,102,241,0.15); color: #818cf8; }
    .stat-icon.emerald { background: rgba(16,185,129,0.15); color: #34d399; }
    .stat-icon.blue { background: rgba(59,130,246,0.15); color: #60a5fa; }

    .content-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
        gap: 20px;
    }
    .panel {
        background: var(--bg-card);
        border: 1px solid var(--border-color);
        border-radius: 16px;
        overflow: hidden;
    }
    .panel-header {
        padding: 20px 24px;
        border-bottom: 1px solid var(--border-light);
        display: flex; align-items: center; justify-content: space-between;
    }
    .panel-header h2 {
        font-size: 15px; font-weight: 600;
        display: flex; align-items: center; gap: 10px;
    }
    .panel-header h2 svg { width: 20px; height: 20px; color: var(--primary-light); }
    .panel-body { padding: 24px; }

    .quick-links { display: grid; gap: 12px; }
    .quick-link {
        display: flex; align-items: center; gap: 14px;
        padding: 14px 16px;
        background: rgba(255,255,255,0.02);
        border: 1px solid var(--border-light);
        border-radius: 12px;
        text-decoration: none;
        color: var(--text-secondary);
        transition: all 0.2s;
    }
    .quick-link:hover {
        background: var(--primary-subtle);
        color: var(--text-primary);
        border-color: rgba(99,102,241,0.2);
    }
    .quick-link-icon {
        width: 40px; height: 40px;
        border-radius: 10px;
        display: flex; align-items: center; justify-content: center;
        flex-shrink: 0;
    }
    .quick-link-icon svg { width: 20px; height: 20px; }
    .quick-link-icon.purple { background: rgba(139,92,246,0.15); color: #a78bfa; }
    .quick-link-icon.blue { background: rgba(59,130,246,0.15); color: #60a5fa; }
    .quick-link-icon.emerald { background: rgba(16,185,129,0.15); color: #34d399; }
    .quick-link-icon.amber { background: rgba(245,158,11,0.15); color: #fbbf24; }
    .quick-link-text h4 { font-size: 14px; font-weight: 600; color: var(--text-primary); }
    .quick-link-text p { font-size: 12px; color: var(--text-muted); margin-top: 2px; }

    .info-list { list-style: none; }
    .info-list li {
        display: flex; align-items: center; gap: 12px;
        padding: 12px 0;
        border-bottom: 1px solid var(--border-light);
        font-size: 13px; color: var(--text-secondary);
    }
    .info-list li:last-child { border-bottom: none; }
    .info-list li svg { width: 18px; height: 18px; color: var(--primary-light); flex-shrink: 0; }
    .info-label { font-weight: 600; color: var(--text-primary); min-width: 120px; }
</style>
@endpush

@section('content')
    <!-- Welcome Banner -->
    <div class="welcome-banner">
        <h2>👋 สวัสดี, {{ session('admin.NAMETH', 'Admin') }}</h2>
        <p>ยินดีต้อนรับเข้าสู่ระบบจัดการ</p>
        <div class="welcome-date">
            📅 {{ \Carbon\Carbon::now()->locale('th')->translatedFormat('l ที่ j F Y') }}
            &nbsp;|&nbsp; 🏢 {{ session('admin.DEPT', '-') }}
        </div>
    </div>

    <!-- Stats Grid — ตัวอย่าง Placeholder -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-info">
                <h3>ข้อมูลตัวอย่าง 1</h3>
                <div class="stat-value">0</div>
                <div class="stat-sub">รายการในระบบ</div>
            </div>
            <div class="stat-icon indigo">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                </svg>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-info">
                <h3>ข้อมูลตัวอย่าง 2</h3>
                <div class="stat-value">0</div>
                <div class="stat-sub">ผู้ใช้ในระบบ</div>
            </div>
            <div class="stat-icon emerald">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                </svg>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-info">
                <h3>ข้อมูลตัวอย่าง 3</h3>
                <div class="stat-value">0</div>
                <div class="stat-sub">รายการรอดำเนินการ</div>
            </div>
            <div class="stat-icon blue">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
        </div>
    </div>

    <!-- Content Grid -->
    <div class="content-grid">
        <!-- Quick Links -->
        <div class="panel">
            <div class="panel-header">
                <h2>
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 003 8.25v10.5A2.25 2.25 0 005.25 21h10.5A2.25 2.25 0 0018 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25" />
                    </svg>
                    ลิงก์ด่วน
                </h2>
            </div>
            <div class="panel-body">
                <div class="quick-links">
                    <a href="{{ route('admin.dashboard') }}" class="quick-link">
                        <div class="quick-link-icon purple">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z" />
                            </svg>
                        </div>
                        <div class="quick-link-text">
                            <h4>แดชบอร์ด</h4>
                            <p>ภาพรวมระบบ</p>
                        </div>
                    </a>
                    {{-- เพิ่ม Quick Links เพิ่มเติมที่นี่ --}}
                </div>
            </div>
        </div>

        <!-- System Info -->
        <div class="panel">
            <div class="panel-header">
                <h2>
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z" />
                    </svg>
                    ข้อมูลระบบ
                </h2>
            </div>
            <div class="panel-body">
                <ul class="info-list">
                    <li>
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                        </svg>
                        <span class="info-label">ผู้ใช้งาน</span>
                        <span>{{ session('admin.NAMETH', '-') }}</span>
                    </li>
                    <li>
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12m-.75 4.5H21m-3.75 3h.008v.008h-.008v-.008zm0 3h.008v.008h-.008v-.008zm0 3h.008v.008h-.008v-.008z" />
                        </svg>
                        <span class="info-label">แผนก</span>
                        <span>{{ session('admin.DEPT', '-') }}</span>
                    </li>
                    <li>
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5.25 14.25h13.5m-13.5 0a3 3 0 01-3-3m3 3a3 3 0 100 6h13.5a3 3 0 100-6m-16.5-3a3 3 0 013-3h13.5a3 3 0 013 3m-19.5 0a4.5 4.5 0 01.9-2.7L5.737 5.1a3.375 3.375 0 012.7-1.35h7.126c1.062 0 2.062.5 2.7 1.35l2.587 3.45a4.5 4.5 0 01.9 2.7m0 0a3 3 0 01-3 3m0 3h.008v.008h-.008v-.008zm0-6h.008v.008h-.008v-.008zm-3 6h.008v.008h-.008v-.008zm0-6h.008v.008h-.008v-.008z" />
                        </svg>
                        <span class="info-label">Laravel</span>
                        <span>{{ app()->version() }}</span>
                    </li>
                    <li>
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17.25 6.75L22.5 12l-5.25 5.25m-10.5 0L1.5 12l5.25-5.25m7.5-3l-4.5 16.5" />
                        </svg>
                        <span class="info-label">PHP</span>
                        <span>{{ PHP_VERSION }}</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
@endsection
