@extends('layouts.employee')

@section('title', 'หน้าหลัก')

@push('head')
<style>
    .welcome-card {
        background: linear-gradient(135deg, rgba(16,185,129,0.1), rgba(6,182,212,0.05));
        border: 1px solid rgba(16,185,129,0.2);
        border-radius: 16px; padding: 28px; margin-bottom: 28px;
    }
    .welcome-card h2 { font-size: 22px; font-weight: 700; margin-bottom: 8px; }
    .welcome-card p { color: #94a3b8; font-size: 14px; }
    .welcome-card .emp-info { display: flex; gap: 24px; margin-top: 16px; flex-wrap: wrap; }
    .welcome-card .info-item { display: flex; align-items: center; gap: 8px; font-size: 13px; color: #cbd5e1; }
    .welcome-card .info-item svg { width: 16px; height: 16px; color: #34d399; }

    .empty-state {
        text-align: center; padding: 60px 20px; color: #64748b;
    }
    .empty-state svg { width: 56px; height: 56px; margin-bottom: 16px; opacity: 0.4; }
    .empty-state h3 { font-size: 18px; font-weight: 600; color: #94a3b8; margin-bottom: 8px; }
</style>
@endpush

@section('content')
{{-- Welcome --}}
<div class="welcome-card">
    <h2>สวัสดี, {{ session('employee.USERS_FNAMETH', session('employee.USERS_FNAME', 'พนักงาน')) }}! 👋</h2>
    <p>ยินดีต้อนรับเข้าสู่ระบบ</p>
    <div class="emp-info">
        <div class="info-item">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 9h3.75M15 12h3.75M15 15h3.75M4.5 19.5h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5zm6-10.125a1.875 1.875 0 11-3.75 0 1.875 1.875 0 013.75 0zm1.294 6.336a6.721 6.721 0 01-3.17.789 6.721 6.721 0 01-3.168-.789 3.376 3.376 0 016.338 0z" />
            </svg>
            รหัสพนักงาน: {{ session('employee.USERS_EMPCODE', '-') }}
        </div>
        <div class="info-item">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12m-.75 4.5H21m-3.75 3H21m-3.75 3H21" />
            </svg>
            แผนก: {{ session('employee.USERS_DEPARTMENT', '-') }}
        </div>
        <div class="info-item">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 14.15v4.25c0 1.094-.787 2.036-1.872 2.18-2.087.277-4.216.42-6.378.42s-4.291-.143-6.378-.42c-1.085-.144-1.872-1.086-1.872-2.18v-4.25m16.5 0a2.18 2.18 0 00.75-1.661V8.706c0-1.081-.768-2.015-1.837-2.175a48.114 48.114 0 00-3.413-.387m4.5 8.006c-.194.165-.42.295-.673.38A23.978 23.978 0 0112 15.75c-2.648 0-5.195-.429-7.577-1.22a2.016 2.016 0 01-.673-.38m0 0A2.18 2.18 0 013 12.489V8.706c0-1.081.768-2.015 1.837-2.175a48.111 48.111 0 013.413-.387m7.5 0V5.25A2.25 2.25 0 0013.5 3h-3a2.25 2.25 0 00-2.25 2.25v.894m7.5 0a48.667 48.667 0 00-7.5 0" />
            </svg>
            ตำแหน่ง: {{ session('employee.USERS_POSITION', '-') }}
        </div>
    </div>
</div>
@endsection
