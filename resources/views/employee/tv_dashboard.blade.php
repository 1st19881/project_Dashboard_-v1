<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Premium TV Dashboard | Car Reservation</title>
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 64 64'><rect rx='14' width='64' height='64' fill='%23050811'/><text x='32' y='44' text-anchor='middle' font-size='36'>🚗</text></svg>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;800&family=Sarabun:wght@300;400;600&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.highcharts.com/highcharts.js"></script>
    
    <style>
        :root {
            --bg-deep: #050811;
            --bg-card: rgba(16, 23, 42, 0.6);
            --border-glass: rgba(255, 255, 255, 0.08);
            --accent-cyan: #22d3ee;
            --accent-blue: #3b82f6;
            --accent-purple: #8b5cf6;
            --text-main: #f8fafc;
            --text-muted: #94a3b8;
        }

        body {
            background-color: var(--bg-deep);
            background-image: 
                radial-gradient(at 0% 0%, rgba(59, 130, 246, 0.05) 0px, transparent 50%),
                radial-gradient(at 100% 0%, rgba(139, 92, 246, 0.05) 0px, transparent 50%);
            color: var(--text-main);
            font-family: 'Outfit', 'Sarabun', sans-serif;
            overflow-x: hidden;
            min-height: 100vh;
            padding-bottom: 30px;
        }

        /* TV Mode: Specific for 1080p TVs (Single Screen, No Scroll) */
        @media (min-width: 1400px) and (min-height: 800px) {
            body {
                overflow: hidden;
                height: 100vh;
                display: flex;
                flex-direction: column;
            }
            .container-fluid {
                flex: 1;
                display: flex;
                flex-direction: column;
                justify-content: space-evenly;
                padding-bottom: 20px;
                padding-top: 10px;
            }
            .premium-header { padding: 0.75rem 2rem !important; margin-bottom: 0 !important; height: 70px; }
            .mb-5, .mb-4 { margin-bottom: 1rem !important; }
            .g-4, .g-3 { --bs-gutter-x: 1rem; --bs-gutter-y: 1rem; }
            .stat-card { padding: 1.25rem !important; }
            .stat-value { font-size: 2.4rem !important; margin-bottom: 0.25rem !important; }
            .stat-label { font-size: 0.75rem !important; }
            .glass-card-header { padding: 0.75rem 1.25rem !important; }
            .glass-card-header h3 { font-size: 0.85rem !important; }
            
            #fleetOverviewChart, #carGroupDonutChart, #carBodyDonutChart { height: 20vh !important; }
            #dailyDeptComboChart, #dailyLocComboChart, #monthlyDeptComboChart, #monthlyLocComboChart { height: 26vh !important; }
            
            .row { margin-left: -10px; margin-right: -10px; }
            .col-xl-2, .col-xl-4, .col-xl-6 { padding-left: 10px; padding-right: 10px; }
        }

        /* Standard Responsive for PC/Tablet/Mobile */
        @media (max-width: 1399px) {
            .premium-header { flex-wrap: wrap; gap: 1rem; height: auto; padding: 1rem; }
            .stat-value { font-size: 2rem; }
            .chart-container, [id*="Chart"] { height: 300px !important; }
        }

        @media (max-width: 768px) {
            .premium-header { padding: 0.75rem; justify-content: center !important; text-align: center; }
            .premium-header > div { justify-content: center !important; width: 100%; }
            .logo-text { font-size: 1rem !important; }
            .badge { font-size: 0.65rem !important; }
            .hide-mobile { display: none; }
        }

        /* =========================================
           Modal Responsive — Tablet & Mobile
           ========================================= */

        /* Slide-up animation for mobile/tablet */
        @keyframes slideUp {
            from { transform: translateY(40px); opacity: 0; }
            to   { transform: translateY(0);    opacity: 1; }
        }

        /* -- Tablet (576px – 991px) -- */
        @media (max-width: 991.98px) {
            #cardModal .modal-dialog {
                max-width: 95vw;
                margin: 1.5rem auto;
            }
            #cardModal .modal-content {
                animation: slideUp 0.35s ease-out;
            }
            #cardModal .modal-header {
                padding: 0.85rem 1.25rem !important;
                position: sticky;
                top: 0;
                z-index: 10;
                background: rgba(30, 41, 59, 0.98) !important;
                backdrop-filter: blur(10px);
            }
            #cardModal .modal-title { font-size: 1rem; }
            #cardModal .modal-body {
                max-height: 70vh;
                overflow-y: auto;
                -webkit-overflow-scrolling: touch;
            }
            /* Smooth scrollbar */
            #cardModal .modal-body::-webkit-scrollbar { width: 4px; }
            #cardModal .modal-body::-webkit-scrollbar-track { background: transparent; }
            #cardModal .modal-body::-webkit-scrollbar-thumb { background: rgba(34, 211, 238, 0.3); border-radius: 4px; }

            .table-premium thead th { font-size: 0.7rem; padding: 0.75rem 0.6rem; white-space: nowrap; }
            .table-premium td { font-size: 0.8rem; padding: 0.75rem 0.6rem; }
        }

        /* -- Mobile (≤ 575px) — Fullscreen modal -- */
        @media (max-width: 575.98px) {
            #cardModal .modal-dialog {
                max-width: 100%;
                width: 100%;
                margin: 0;
                min-height: 100vh;
                display: flex;
                align-items: flex-end;
            }
            #cardModal .modal-content {
                border-radius: 1.25rem 1.25rem 0 0 !important;
                min-height: 92vh;
                max-height: 100vh;
                animation: slideUp 0.3s ease-out;
            }
            #cardModal .modal-header {
                padding: 1rem 1rem 0.75rem !important;
                position: sticky;
                top: 0;
                z-index: 10;
                background: rgba(30, 41, 59, 0.98) !important;
                backdrop-filter: blur(12px);
                border-radius: 1.25rem 1.25rem 0 0;
            }
            #cardModal .modal-title {
                font-size: 0.95rem;
                max-width: 75%;
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
            }
            /* Larger close button for touch */
            #cardModal .btn-close {
                width: 1.2em;
                height: 1.2em;
                padding: 0.5rem;
            }
            #cardModal .modal-body {
                max-height: calc(92vh - 56px);
                overflow-y: auto;
                -webkit-overflow-scrolling: touch;
                padding: 0 !important;
            }
            #cardModal .modal-body::-webkit-scrollbar { width: 3px; }
            #cardModal .modal-body::-webkit-scrollbar-thumb { background: rgba(34, 211, 238, 0.25); border-radius: 3px; }

            /* Table adjustments for mobile */
            .table-premium thead th {
                font-size: 0.6rem;
                padding: 0.6rem 0.4rem;
                white-space: nowrap;
                position: sticky;
                top: 0;
                background: rgba(15, 23, 42, 0.95) !important;
                z-index: 5;
            }
            .table-premium td {
                font-size: 0.72rem;
                padding: 0.6rem 0.4rem;
                vertical-align: middle;
            }
            /* Tighter row numbering */
            .table-premium td.ps-4 { padding-left: 0.6rem !important; }
            .table-premium th.ps-4 { padding-left: 0.6rem !important; }

            /* Badge sizing inside table */
            .table-premium .badge {
                font-size: 0.6rem !important;
                padding: 0.25rem 0.4rem !important;
            }
        }

        /* -- Very small screens (≤ 374px, e.g. iPhone SE) -- */
        @media (max-width: 374px) {
            #cardModal .modal-content { min-height: 95vh; }
            .table-premium thead th { font-size: 0.55rem; padding: 0.5rem 0.3rem; }
            .table-premium td { font-size: 0.65rem; padding: 0.5rem 0.3rem; }
        }

        .glass-card {
            background: var(--bg-card);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid var(--border-glass);
            border-radius: 1.25rem;
            box-shadow: 0 10px 30px -10px rgba(0, 0, 0, 0.5);
            transition: all 0.3s ease;
        }
        
        .glass-card:hover { border-color: rgba(34, 211, 238, 0.3); }

        .glass-card-header {
            padding: 1rem 1.5rem;
            border-bottom: 1px solid var(--border-glass);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .glass-card-header h3 {
            font-size: 0.9rem;
            font-weight: 600;
            margin: 0;
            color: var(--accent-cyan);
            text-transform: uppercase;
            letter-spacing: 0.1em;
        }

        .stat-card {
            padding: 1.5rem;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .stat-card::after {
            content: '';
            position: absolute;
            bottom: 0; left: 10%; width: 80%; height: 2px;
            background: linear-gradient(90deg, transparent, var(--accent-blue), transparent);
            opacity: 0.5;
        }

        .stat-value {
            font-size: 2.8rem;
            font-weight: 800;
            line-height: 1;
            margin-bottom: 0.5rem;
            background: linear-gradient(180deg, #fff 0%, #94a3b8 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .stat-label {
            color: var(--text-muted);
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.1em;
        }

        .premium-header {
            background: rgba(15, 23, 42, 0.8);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid var(--border-glass);
            padding: 1rem 2rem;
            margin-bottom: 2rem;
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .logo-text {
            font-weight: 800;
            font-size: 1.4rem;
            letter-spacing: -0.02em;
            background: linear-gradient(90deg, var(--accent-cyan), var(--accent-blue));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .live-pulse {
            width: 8px; height: 8px;
            background: #ef4444;
            border-radius: 50%;
            display: inline-block;
            box-shadow: 0 0 8px #ef4444;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% { transform: scale(1); opacity: 1; }
            50% { transform: scale(1.5); opacity: 0.5; }
            100% { transform: scale(1); opacity: 1; }
        }

        .chart-container { height: 300px; width: 100%; }

        .cursor-pointer { cursor: pointer; }

        .bi-spin { animation: spin 1s linear infinite; }
        @keyframes spin { 100% { transform: rotate(360deg); } }

        .stat-card.cursor-pointer:hover {
            transform: translateY(-2px);
            border-color: rgba(34, 211, 238, 0.4);
            box-shadow: 0 10px 40px -10px rgba(34, 211, 238, 0.15);
        }


        .loading-overlay {
            position: fixed; inset: 0;
            background: var(--bg-deep);
            z-index: 9999;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            transition: all 0.5s ease;
        }

        .loading-overlay.hidden { opacity: 0; visibility: hidden; }

        .btn-premium {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid var(--border-glass);
            color: var(--text-main);
            border-radius: 50px;
            padding: 0.4rem 1.2rem;
            font-size: 0.85rem;
            transition: all 0.3s;
        }

        .btn-premium:hover, .btn-premium.active {
            background: var(--accent-blue);
            border-color: var(--accent-blue);
            color: white;
            box-shadow: 0 0 15px rgba(59, 130, 246, 0.4);
        }

        /* Table Customization */
        .table-premium { --bs-table-bg: transparent; --bs-table-color: #e2e8f0; }
        .table-premium thead th { 
            background: rgba(255,255,255,0.03); 
            border-bottom: 2px solid var(--border-glass); 
            color: var(--accent-cyan); 
            font-size: 0.75rem; 
            text-transform: uppercase;
            letter-spacing: 0.05em;
            padding: 1rem 0.75rem;
        }
        .table-premium td { 
            border-bottom: 1px solid rgba(255,255,255,0.05); 
            font-size: 0.85rem; 
            padding: 1rem 0.75rem;
            color: #cbd5e1;
        }
        .text-muted-light { color: #94a3b8 !important; }
        .modal-content { border: 1px solid rgba(255,255,255,0.1) !important; box-shadow: 0 0 50px rgba(0,0,0,0.8) !important; }

        /* Modal backdrop enhancement */
        .modal-backdrop.show { backdrop-filter: blur(4px); }
    </style>
</head>
<body>

    <div class="loading-overlay" id="loadingOverlay">
        <div class="spinner-grow text-info mb-4" style="width: 4rem; height: 4rem;"></div>
        <div class="h5 fw-bold text-info" style="letter-spacing: 4px;">กำลังเริ่มระบบ...</div>
    </div>

    <nav class="premium-header d-flex align-items-center justify-content-between px-4 py-2">
        <div class="d-flex align-items-center gap-4">
            <div class="d-flex align-items-center gap-3">
                <div class="p-2 bg-info bg-opacity-10 rounded-3 border border-info border-opacity-20">
                    <i class="bi bi-car-front-fill h4 mb-0 text-info"></i>
                </div>
                <div class="d-none d-xl-block">
                    <div class="logo-text" style="font-size: 1.2rem;">Car Reservation Dashboard</div>
                    <div class="text-muted small fw-bold" style="font-size: 9px; letter-spacing: 1px;">ระบบติดตามการจองรถส่วนกลาง</div>
                </div>
            </div>
            
            <div class="h-100 border-start border-white border-opacity-10 mx-2" style="height: 25px !important;"></div>

            <div class="d-flex align-items-center gap-3">
                <h2 class="h6 mb-0 fw-bold text-white d-none" id="dateText">---</h2>
                <div class="badge bg-info bg-opacity-10 text-info border border-info border-opacity-20 px-3 py-1 rounded-pill d-flex align-items-center gap-2" style="font-size: 0.75rem;">
                    <i class="bi bi-geo-alt-fill text-info"></i>
                    <span id="plantName" class="fw-bold">---</span>
                    <span class="opacity-25">|</span>
                    <span id="displayDate" class="small">---</span>
                </div>
                <div class="d-flex gap-1" id="plantSelector"></div>
            </div>
        </div>

        <div class="d-flex align-items-center gap-3">
            <div class="d-flex align-items-center gap-2 bg-dark bg-opacity-50 px-3 py-1 rounded-pill border border-white border-opacity-10 shadow-inner">
                <span class="live-pulse" style="width:6px; height:6px;"></span>
                <span class="fw-bold text-white font-monospace small" id="clockText">00:00:00</span>
            </div>
            
            <div class="d-flex gap-2 align-items-center bg-dark bg-opacity-50 p-1 rounded-pill border border-white border-opacity-10 shadow-sm" style="background: rgba(15, 23, 42, 0.9) !important;">
                <input type="date" id="datePicker" class="form-control form-control-sm bg-transparent border-0 text-white fw-bold" style="width: 140px; font-size: 0.8rem; color-scheme: dark;" onchange="fetchDashboard()">
                <button class="btn btn-sm btn-icon border-start border-white border-opacity-10 rounded-0 ps-2" id="refreshBtn" onclick="fetchDashboard()" title="Refresh">
                    <i class="bi bi-arrow-clockwise text-info" id="refreshIcon"></i>
                </button>
            </div>
        </div>
    </nav>

    <div class="container-fluid px-4">

        <!-- KPI Cards -->
        <div class="row g-4 mb-5">
            <div class="col-xl-2 col-md-4 col-6">
                <div class="glass-card stat-card cursor-pointer" onclick="showCardModal('all', 'รายการจองทั้งหมด')">
                    <div class="stat-value" id="totalBookings">0</div>
                    <div class="stat-label">รายการจอง</div>
                </div>
            </div>
            <div class="col-xl-2 col-md-4 col-6">
                <div class="glass-card stat-card cursor-pointer" onclick="showCardModal('pending', 'รอจ่ายกุญแจ')">
                    <div class="stat-value text-warning" id="pendingCount" style="background:none;-webkit-text-fill-color:inherit;">0</div>
                    <div class="stat-label">รอจ่ายกุญแจ</div>
                </div>
            </div>
            <div class="col-xl-2 col-md-4 col-6">
                <div class="glass-card stat-card cursor-pointer" onclick="showCardModal('issued', 'รอคืนกุญแจ')">
                    <div class="stat-value text-primary" id="issuedCount" style="background:none;-webkit-text-fill-color:inherit;">0</div>
                    <div class="stat-label">รอคืนกุญแจ</div>
                </div>
            </div>
            <div class="col-xl-2 col-md-4 col-6">
                <div class="glass-card stat-card cursor-pointer" onclick="showCardModal('closed', 'สถานะใบจอง')">
                    <div class="stat-value text-success" id="returnedCount" style="background:none;-webkit-text-fill-color:inherit;">0</div>
                    <div class="stat-label">สถานะใบจอง</div>
                </div>
            </div>
            <div class="col-xl-2 col-md-4 col-6">
                <div class="glass-card stat-card cursor-pointer" onclick="showCardModal('private', 'รถส่วนตัว')">
                    <div class="stat-value text-secondary" id="privateCount" style="background:none;-webkit-text-fill-color:inherit;">0</div>
                    <div class="stat-label">รถส่วนตัว</div>
                </div>
            </div>
            <div class="col-xl-2 col-md-4 col-6">
                <div class="glass-card stat-card cursor-pointer" onclick="showCardModal('distance', 'ระยะทางรวม (KM)')">
                    <div class="stat-value text-info" id="totalDistance" style="background:none;-webkit-text-fill-color:inherit;">0</div>
                    <div class="stat-label">ระยะทางรวม</div>
                </div>
            </div>
        </div>

        <!-- Top Row: 3 Donut Charts -->
        <div class="row g-3 mb-4">
            <div class="col-xl-4">
                <div class="glass-card">
                    <div class="glass-card-header">
                        <h3 class="small"><i class="bi bi-pie-chart-fill me-2 text-info"></i>ภาพรวมรถส่วนกลาง</h3>
                        <div class="badge bg-info bg-opacity-10 text-info border border-info border-opacity-20 px-2 py-1 rounded-pill small" style="font-size: 0.65rem;">
                            <i class="bi bi-car-front-fill me-1"></i>รวม <span id="totalCarsBadge1">0</span> คัน
                        </div>
                    </div>
                    <div class="glass-card-body p-2">
                        <div id="fleetOverviewChart" style="height: 220px;"></div>
                    </div>
                </div>
            </div>
            <div class="col-xl-4">
                <div class="glass-card">
                    <div class="glass-card-header">
                        <h3 class="small"><i class="bi bi-pie-chart-fill me-2 text-primary"></i>จำนวนรถแยกตามหมวด</h3>
                        <div class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-20 px-2 py-1 rounded-pill small" style="font-size: 0.65rem;">
                            <i class="bi bi-car-front-fill me-1"></i>รวม <span id="totalCarsBadge2">0</span> คัน
                        </div>
                    </div>
                    <div class="glass-card-body p-2">
                        <div id="carGroupDonutChart" style="height: 220px;"></div>
                    </div>
                </div>
            </div>
            <div class="col-xl-4">
                <div class="glass-card">
                    <div class="glass-card-header">
                        <h3 class="small"><i class="bi bi-pie-chart-fill me-2 text-success"></i>จำนวนรถแต่ละประเภท</h3>
                        <div class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-20 px-2 py-1 rounded-pill small" style="font-size: 0.65rem;">
                            <i class="bi bi-car-front-fill me-1"></i>รวม <span id="totalCarsBadge3">0</span> คัน
                        </div>
                    </div>
                    <div class="glass-card-body p-2">
                        <div id="carBodyDonutChart" style="height: 220px;"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Middle Row: Daily Analytics -->
        <div class="row g-3 mb-4">
            <div class="col-xl-6">
                <div class="glass-card">
                    <div class="glass-card-header">
                        <h3><i class="bi bi-trophy-fill me-2 text-warning"></i>Top 10 แผนกจองมากที่สุด (รายวัน)</h3>
                    </div>
                    <div class="glass-card-body">
                        <div id="dailyDeptComboChart" style="height: 280px;"></div>
                    </div>
                </div>
            </div>
            <div class="col-xl-6">
                <div class="glass-card">
                    <div class="glass-card-header">
                        <h3><i class="bi bi-geo-alt-fill me-2 text-danger"></i>Top 10 สถานที่ไปมากที่สุด (รายวัน)</h3>
                    </div>
                    <div class="glass-card-body">
                        <div id="dailyLocComboChart" style="height: 280px;"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bottom Row: Monthly Analytics -->
        <div class="row g-3 mb-4">
            <div class="col-xl-6">
                <div class="glass-card border-info border-opacity-10">
                    <div class="glass-card-header">
                        <h3><i class="bi bi-calendar-check-fill me-2 text-info"></i>Top 10 แผนกจองมากที่สุด (รายเดือน)</h3>
                    </div>
                    <div class="glass-card-body">
                        <div id="monthlyDeptComboChart" style="height: 280px;"></div>
                    </div>
                </div>
            </div>
            <div class="col-xl-6">
                <div class="glass-card border-primary border-opacity-10">
                    <div class="glass-card-header">
                        <h3><i class="bi bi-pin-map-fill me-2 text-primary"></i>Top 10 สถานที่ไปมากที่สุด (รายเดือน)</h3>
                    </div>
                    <div class="glass-card-body">
                        <div id="monthlyLocComboChart" style="height: 280px;"></div>
                    </div>
                </div>
            </div>
        </div>
        </div>
    </div>


    <!-- Modal -->
    <div class="modal fade" id="cardModal" tabindex="-1">
        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content bg-dark border-secondary shadow-lg rounded-4 overflow-hidden">
                <div class="modal-header border-bottom border-white border-opacity-10 px-4 py-3 bg-white bg-opacity-5">
                    <h5 class="modal-title fw-bold text-info d-flex align-items-center gap-2" id="modalTitle">
                        <i class="bi bi-list-ul d-sm-none" style="font-size: 0.9rem;"></i>
                        <span>รายละเอียด</span>
                    </h5>
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge bg-info bg-opacity-10 text-info border border-info border-opacity-20 rounded-pill d-none" id="modalCount" style="font-size: 0.7rem;"></span>
                        <button type="button" class="btn btn-link text-white p-0 d-flex align-items-center justify-content-center" data-bs-dismiss="modal" aria-label="ปิด" style="width: 32px; height: 32px; border: 1px solid rgba(255,255,255,0.15); border-radius: 50%; text-decoration: none; opacity: 0.8; transition: all 0.2s;" onmouseover="this.style.opacity='1';this.style.borderColor='rgba(34,211,238,0.5)';this.style.color='#22d3ee'" onmouseout="this.style.opacity='0.8';this.style.borderColor='rgba(255,255,255,0.15)';this.style.color='white'">
                            <i class="bi bi-x-lg" style="font-size: 0.85rem;"></i>
                        </button>
                    </div>
                </div>
                <div class="modal-body p-0">
                    <div class="table-responsive">
                        <table class="table table-premium table-hover mb-0 align-middle">
                            <thead>
                                <tr>
                                    <th class="ps-4">#</th>
                                    <th>เวลาใช้</th>
                                    <th>ทะเบียน</th>
                                    <th>ผู้จอง</th>
                                    <th class="hide-mobile">ปลายทาง</th>
                                    <th class="hide-mobile">จังหวัด</th>
                                    <th>ระยะทาง</th>
                                    <th>สถานะ</th>
                                </tr>
                            </thead>
                            <tbody id="modalBody"></tbody>
                        </table>
                    </div>
                    <div class="text-center py-5" id="modalEmpty" style="display:none">
                        <i class="bi bi-database-exclamation display-4 opacity-10"></i>
                        <p class="text-muted mt-2">ไม่พบข้อมูลในหมวดนี้</p>
                    </div>
                </div>
                <div class="modal-footer border-top border-white border-opacity-10 py-2 px-3 d-sm-none bg-white bg-opacity-5">
                    <button type="button" class="btn btn-sm btn-outline-info w-100 rounded-pill" data-bs-dismiss="modal">
                        <i class="bi bi-x-lg me-1"></i>ปิด
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        const DATA_URL = "{{ route('tv.dashboard.data') }}";
        const REFRESH_RATE = 3600000; // 1 hour
        
        let currentPlant = '1100';
        let dashData = null;
        let modal = null;

        document.addEventListener('DOMContentLoaded', () => {
            modal = new bootstrap.Modal(document.getElementById('cardModal'));
            document.getElementById('datePicker').value = new Date().toISOString().split('T')[0];
            
            document.getElementById('datePicker').addEventListener('change', fetchDashboard);
            
            updateClock();
            setInterval(updateClock, 1000);
            
            fetchDashboard();
            setInterval(fetchDashboard, REFRESH_RATE);

            // Fail-safe
            setTimeout(() => document.getElementById('loadingOverlay').classList.add('hidden'), 4000);
        });

        function updateClock() {
            const now = new Date();
            document.getElementById('clockText').textContent = now.toLocaleTimeString('en-GB');
        }

        async function fetchDashboard() {
            const refreshIcon = document.getElementById('refreshIcon');
            try {
                if (refreshIcon) refreshIcon.classList.add('bi-spin');
                
                const date = document.getElementById('datePicker').value;
                const res = await fetch(`${DATA_URL}?plant=${currentPlant}&date=${date}&_t=${Date.now()}`);
                const data = await res.json();
                if (!data.success) throw new Error(data.error);

                dashData = data;
                renderUI(data);
                document.getElementById('loadingOverlay').classList.add('hidden');
            } catch (err) { 
                console.error('Dashboard error:', err); 
            } finally {
                if (refreshIcon) refreshIcon.classList.remove('bi-spin');
            }
        }

        function renderUI(data) {
            document.getElementById('dateText').textContent = data.thaiDate;
            const cleanDate = data.thaiDate.replace(/^วัน[ก-ฮ]+ที่\s/, '');
            document.getElementById('displayDate').textContent = cleanDate;
            document.getElementById('plantName').textContent = data.plantName || data.plant;
            
            updateKPIs(data.stats);
            renderFleetOverviewDonut(data.stats);
            renderCarGroupDonut(data.carGroupChart);
            renderCarBodyDonut(data.carBodyChart);
            
            renderComboChart('dailyDeptComboChart', data.topDepartments, '#22d3ee', 'คัน');
            renderComboChart('dailyLocComboChart', data.topLocations, '#22d3ee', 'คัน');
            renderComboChart('monthlyDeptComboChart', data.topDeptMonthly, '#3b82f6', 'คัน');
            renderComboChart('monthlyLocComboChart', data.topLocMonthly, '#3b82f6', 'คัน');
            
            updatePlants(data.availablePlants);
        }

        function updateKPIs(stats) {
            animateValue("totalBookings", stats.totalBookings);
            animateValue("pendingCount", stats.pendingCount);
            animateValue("issuedCount", stats.issuedCount);
            animateValue("returnedCount", stats.returnedCount);
            animateValue("privateCount", stats.privateCount);
            animateValue("totalDistance", stats.totalDistance);
        }

        function animateValue(id, value) {
            const el = document.getElementById(id);
            if (!el) return;
            const target = parseInt(value) || 0;
            const current = parseInt(el.textContent.replace(/,/g, '')) || 0;
            if (current === target) { el.textContent = target.toLocaleString(); return; }

            const duration = 800;
            let start = null;
            function step(ts) {
                if (!start) start = ts;
                const progress = Math.min((ts - start) / duration, 1);
                const eased = 1 - Math.pow(1 - progress, 3); // easeOutCubic
                el.textContent = Math.floor(current + (target - current) * eased).toLocaleString();
                if (progress < 1) requestAnimationFrame(step);
            }
            requestAnimationFrame(step);
        }

        function renderFleetOverviewDonut(stats) {
            document.getElementById('totalCarsBadge1').textContent = stats.totalCars;
            Highcharts.chart('fleetOverviewChart', {
                chart: { backgroundColor: 'transparent', type: 'pie' },
                title: { text: '' },
                credits: { enabled: false },
                plotOptions: { 
                    pie: { 
                        innerSize: '65%', borderWidth: 0,
                        dataLabels: { 
                            enabled: true, 
                            format: '{point.name}<br>{point.y} ({point.percentage:.1f}%)',
                            style: { color: '#94a3b8', textOutline: 'none', fontSize: '9px' },
                            distance: 15
                        }
                    } 
                },
                series: [{
                    name: 'สถานะ',
                    data: [
                        { name: 'พร้อมใช้งาน', y: stats.carsAvailable, color: '#10b981' },
                        { name: 'ไม่พร้อมใช้งาน', y: stats.carsNotReady, color: '#ef4444' }
                    ]
                }]
            });
        }

        function renderCarGroupDonut(chartData) {
            const total = chartData.data.reduce((a, b) => a + b, 0);
            document.getElementById('totalCarsBadge2').textContent = total;
            const colors = ['#22d3ee', '#f59e0b', '#3b82f6', '#8b5cf6', '#ec4899'];
            Highcharts.chart('carGroupDonutChart', {
                chart: { backgroundColor: 'transparent', type: 'pie' },
                title: { text: '' },
                credits: { enabled: false },
                plotOptions: { 
                    pie: { 
                        innerSize: '65%', borderWidth: 0,
                        dataLabels: { 
                            enabled: true, 
                            format: '{point.name}<br>{point.y} ({point.percentage:.1f}%)',
                            style: { color: '#94a3b8', textOutline: 'none', fontSize: '9px' },
                            distance: 15
                        }
                    } 
                },
                series: [{
                    name: 'หมวดหมู่',
                    data: chartData.labels.map((l, i) => ({
                        name: l,
                        y: chartData.data[i],
                        color: colors[i % colors.length]
                    }))
                }]
            });
        }

        function renderCarBodyDonut(chartData) {
            const total = chartData.data.reduce((a, b) => a + b, 0);
            document.getElementById('totalCarsBadge3').textContent = total;
            const colors = ['#3b82f6', '#8b5cf6', '#22d3ee', '#10b981', '#f59e0b', '#ef4444', '#ec4899', '#f97316'];
            Highcharts.chart('carBodyDonutChart', {
                chart: { backgroundColor: 'transparent', type: 'pie' },
                title: { text: '' },
                credits: { enabled: false },
                plotOptions: { 
                    pie: { 
                        innerSize: '65%', borderWidth: 0,
                        dataLabels: { 
                            enabled: true, 
                            format: '{point.name}<br>{point.y} ({point.percentage:.1f}%)',
                            style: { color: '#94a3b8', textOutline: 'none', fontSize: '8px' },
                            distance: 12
                        }
                    } 
                },
                series: [{
                    name: 'ประเภทรถ',
                    data: chartData.labels.map((l, i) => ({
                        name: l,
                        y: chartData.data[i],
                        color: colors[i % colors.length]
                    }))
                }]
            });
        }

        function renderComboChart(id, data, color, unit) {
            if (!data || data.length === 0) return;
            const categories = data.map(i => i.name);
            const values = data.map(i => i.count);
            
            Highcharts.chart(id, {
                chart: { backgroundColor: 'transparent', type: 'column' },
                title: { text: '' },
                xAxis: { 
                    categories: categories,
                    labels: { style: { color: '#64748b', fontSize: '9px' } },
                    gridLineWidth: 0, lineWidth: 0
                },
                yAxis: { 
                    title: { text: '' },
                    gridLineColor: 'rgba(255,255,255,0.03)',
                    labels: { style: { color: '#475569', fontSize: '9px' } }
                },
                legend: { enabled: false },
                credits: { enabled: false },
                plotOptions: { 
                    column: { 
                        borderRadius: 4, 
                        borderWidth: 0,
                        color: {
                            linearGradient: { x1: 0, y1: 0, x2: 0, y2: 1 },
                            stops: [[0, color], [1, Highcharts.color(color).setOpacity(0.1).get()]]
                        },
                        dataLabels: { enabled: true, format: '{y} ' + unit, style: { color: '#94a3b8', textOutline: 'none', fontSize: '9px' } }
                    } 
                },
                series: [
                    { name: 'จำนวน', type: 'column', data: values },
                    { name: 'แนวโน้ม', type: 'spline', data: values, color: '#f8fafc', opacity: 0.8, marker: { radius: 3, lineWidth: 1, lineColor: color } }
                ]
            });
        }


        function updatePlants(plants) {
            const el = document.getElementById('plantSelector');
            el.innerHTML = plants.map(p => 
                `<button class="btn-premium ${p.id === currentPlant ? 'active' : ''}" onclick="switchPlant('${p.id}')">${p.name}</button>`
            ).join('');
        }

        function switchPlant(pid) {
            currentPlant = pid;
            fetchDashboard();
        }

        const statusColors = {
            pending: { text: 'รอจ่ายกุญแจ', cls: 'bg-warning text-dark', color: '#f59e0b' },
            'in-use': { text: 'กำลังใช้งาน', cls: 'bg-primary', color: '#3b82f6' },
            issued: { text: 'รอคืนกุญแจ', cls: 'bg-info text-dark', color: '#06b6d4' },
            returned: { text: 'รอปิดใบจอง', cls: 'bg-success', color: '#10b981' },
            closed: { text: 'ปิดใบจองแล้ว', cls: 'bg-success', color: '#10b981' },
            private: { text: 'รถส่วนตัว', cls: 'bg-secondary', color: '#64748b' }
        };

        function resolveDisplayStatus(b) {
            if (b.status === 'issued') {
                return b.totalMile > 0 ? 'issued' : 'in-use';
            }
            return b.status;
        }

        function showCardModal(type, title) {
            if (!dashData) return;
            const titleEl = document.getElementById('modalTitle');
            const titleSpan = titleEl.querySelector('span');
            if (titleSpan) titleSpan.textContent = title;
            else titleEl.textContent = title;
            
            let list = [];
            if (type === 'all') list = dashData.recentBookings;
            else if (type === 'pending') list = dashData.recentBookings.filter(b => b.status === 'pending');
            else if (type === 'issued') list = dashData.recentBookings.filter(b => b.status === 'issued');
            else if (type === 'closed') list = dashData.recentBookings.filter(b => b.status === 'returned' || b.status === 'closed');
            else if (type === 'private') list = dashData.recentBookings.filter(b => b.status === 'private');
            else if (type === 'distance') list = dashData.recentBookings.filter(b => b.totalMile > 0);

            // Show record count badge
            const countBadge = document.getElementById('modalCount');
            if (countBadge) {
                countBadge.textContent = list.length + ' รายการ';
                countBadge.classList.toggle('d-none', list.length === 0);
            }

            const tbody = document.getElementById('modalBody');
            const empty = document.getElementById('modalEmpty');
            
            if (list.length === 0) {
                tbody.innerHTML = '';
                empty.style.display = 'block';
            } else {
                empty.style.display = 'none';
                tbody.innerHTML = list.map((b, i) => {
                    const ds = resolveDisplayStatus(b);
                    const st = statusColors[ds] || statusColors['pending'];
                    return `
                        <tr>
                            <td class="ps-4 text-muted-light small">${i + 1}</td>
                            <td class="fw-bold text-white">${b.usedTime || '—'}</td>
                            <td><span class="badge bg-info bg-opacity-10 border border-info border-opacity-20 text-info px-2 py-1">${b.carLicense}</span></td>
                            <td class="text-main">${b.userName}</td>
                            <td class="text-main hide-mobile">${b.destination}</td>
                            <td class="small text-muted-light hide-mobile">${b.province || '—'}</td>
                            <td class="font-monospace text-info">${b.totalMile > 0 ? b.totalMile.toLocaleString() + ' km' : '—'}</td>
                            <td><span class="badge ${st.cls}" style="font-size:0.7rem; letter-spacing: 0.5px;">${st.text}</span></td>
                        </tr>
                    `;
                }).join('');
            }
            modal.show();
        }
    </script>
</body>
</html>
