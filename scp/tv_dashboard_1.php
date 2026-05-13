<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Dashboard แสดงสถานะการจองรถส่วนกลาง - Real-time Display for TV">
    <title>🚗 Car Reservation Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="css/tv_dashboard.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.2.0/dist/chartjs-plugin-datalabels.min.js"></script>
    <script src="https://code.highcharts.com/highcharts.js"></script>

    <script src="https://code.highcharts.com/modules/boost.js"></script>

    <!-- TV 65" TCL Override (1920x1080) + DARK THEME -->
    <link href="css/tv_dashboard_1_demo_override.css" rel="stylesheet">

</head>

<body>
    <!-- ═══ SVG Car Templates (hidden, referenced via <use>) ═══ -->
    <svg xmlns="http://www.w3.org/2000/svg" style="display:none">
        <!-- Full detailed car for road animation -->
        <symbol id="carSVG" viewBox="0 0 100 40">
            <!-- Car body -->
            <path class="car-body" d="M15,28 L10,28 Q5,28 5,24 L5,22 Q5,20 7,20 L18,20 L24,12 Q26,10 30,10 L65,10 Q70,10 72,12 L80,20 L93,20 Q95,20 95,22 L95,24 Q95,28 90,28 L85,28" fill="#3b82f6" />
            <!-- Car roof/windows -->
            <path class="car-window" d="M26,18 L32,11 Q33,10 35,10 L55,10 Q57,10 58,11 L65,18 Z" fill="rgba(6,182,212,0.5)" />
            <line x1="48" y1="10" x2="48" y2="18" stroke="rgba(255,255,255,0.15)" stroke-width="0.5" />
            <!-- Headlights -->
            <rect class="headlight" x="93" y="20" width="4" height="4" rx="1" fill="rgba(255,255,180,0.9)" />
            <rect class="taillight" x="3" y="20" width="3" height="3" rx="1" fill="rgba(239,68,68,0.9)" />
            <!-- Wheels -->
            <circle class="car-wheel" cx="22" cy="28" r="6" fill="#1e293b" stroke="rgba(255,255,255,0.2)" stroke-width="1" />
            <circle class="car-wheel-hub" cx="22" cy="28" r="2.5" fill="rgba(255,255,255,0.1)" />
            <line x1="22" y1="22.5" x2="22" y2="24.5" stroke="rgba(255,255,255,0.15)" stroke-width="0.5">
                <animateTransform attributeName="transform" type="rotate" from="0 22 28" to="360 22 28" dur="0.4s" repeatCount="indefinite" />
            </line>
            <circle class="car-wheel" cx="78" cy="28" r="6" fill="#1e293b" stroke="rgba(255,255,255,0.2)" stroke-width="1" />
            <circle class="car-wheel-hub" cx="78" cy="28" r="2.5" fill="rgba(255,255,255,0.1)" />
            <line x1="78" y1="22.5" x2="78" y2="24.5" stroke="rgba(255,255,255,0.15)" stroke-width="0.5">
                <animateTransform attributeName="transform" type="rotate" from="0 78 28" to="360 78 28" dur="0.4s" repeatCount="indefinite" />
            </line>
            <!-- Bumper chrome -->
            <rect x="6" y="24" width="8" height="1" rx="0.5" fill="rgba(255,255,255,0.1)" />
            <rect x="86" y="24" width="8" height="1" rx="0.5" fill="rgba(255,255,255,0.1)" />
        </symbol>

        <!-- Flipped car (drives left) -->
        <symbol id="carSVGFlip" viewBox="0 0 100 40">
            <g transform="scale(-1,1) translate(-100,0)">
                <path class="car-body" d="M15,28 L10,28 Q5,28 5,24 L5,22 Q5,20 7,20 L18,20 L24,12 Q26,10 30,10 L65,10 Q70,10 72,12 L80,20 L93,20 Q95,20 95,22 L95,24 Q95,28 90,28 L85,28" fill="#8b5cf6" />
                <path class="car-window" d="M26,18 L32,11 Q33,10 35,10 L55,10 Q57,10 58,11 L65,18 Z" fill="rgba(139,92,246,0.4)" />
                <line x1="48" y1="10" x2="48" y2="18" stroke="rgba(255,255,255,0.15)" stroke-width="0.5" />
                <rect class="headlight" x="93" y="20" width="4" height="4" rx="1" fill="rgba(255,255,180,0.9)" />
                <rect class="taillight" x="3" y="20" width="3" height="3" rx="1" fill="rgba(239,68,68,0.9)" />
                <circle class="car-wheel" cx="22" cy="28" r="6" fill="#1e293b" stroke="rgba(255,255,255,0.2)" stroke-width="1" />
                <circle cx="22" cy="28" r="2.5" fill="rgba(255,255,255,0.1)" />
                <line x1="22" y1="22.5" x2="22" y2="24.5" stroke="rgba(255,255,255,0.15)" stroke-width="0.5">
                    <animateTransform attributeName="transform" type="rotate" from="0 22 28" to="-360 22 28" dur="0.4s" repeatCount="indefinite" />
                </line>
                <circle class="car-wheel" cx="78" cy="28" r="6" fill="#1e293b" stroke="rgba(255,255,255,0.2)" stroke-width="1" />
                <circle cx="78" cy="28" r="2.5" fill="rgba(255,255,255,0.1)" />
                <line x1="78" y1="22.5" x2="78" y2="24.5" stroke="rgba(255,255,255,0.15)" stroke-width="0.5">
                    <animateTransform attributeName="transform" type="rotate" from="0 78 28" to="-360 78 28" dur="0.4s" repeatCount="indefinite" />
                </line>
            </g>
        </symbol>

        <!-- Green car variant -->
        <symbol id="carSVGGreen" viewBox="0 0 100 40">
            <path class="car-body" d="M15,28 L10,28 Q5,28 5,24 L5,22 Q5,20 7,20 L18,20 L24,12 Q26,10 30,10 L65,10 Q70,10 72,12 L80,20 L93,20 Q95,20 95,22 L95,24 Q95,28 90,28 L85,28" fill="#10b981" />
            <path class="car-window" d="M26,18 L32,11 Q33,10 35,10 L55,10 Q57,10 58,11 L65,18 Z" fill="rgba(16,185,129,0.4)" />
            <line x1="48" y1="10" x2="48" y2="18" stroke="rgba(255,255,255,0.15)" stroke-width="0.5" />
            <rect class="headlight" x="93" y="20" width="4" height="4" rx="1" fill="rgba(255,255,180,0.9)" />
            <rect class="taillight" x="3" y="20" width="3" height="3" rx="1" fill="rgba(239,68,68,0.9)" />
            <circle class="car-wheel" cx="22" cy="28" r="6" fill="#1e293b" stroke="rgba(255,255,255,0.2)" stroke-width="1" />
            <circle cx="22" cy="28" r="2.5" fill="rgba(255,255,255,0.1)" />
            <line x1="22" y1="22.5" x2="22" y2="24.5" stroke="rgba(255,255,255,0.15)" stroke-width="0.5">
                <animateTransform attributeName="transform" type="rotate" from="0 22 28" to="360 22 28" dur="0.5s" repeatCount="indefinite" />
            </line>
            <circle class="car-wheel" cx="78" cy="28" r="6" fill="#1e293b" stroke="rgba(255,255,255,0.2)" stroke-width="1" />
            <circle cx="78" cy="28" r="2.5" fill="rgba(255,255,255,0.1)" />
            <line x1="78" y1="22.5" x2="78" y2="24.5" stroke="rgba(255,255,255,0.15)" stroke-width="0.5">
                <animateTransform attributeName="transform" type="rotate" from="0 78 28" to="360 78 28" dur="0.5s" repeatCount="indefinite" />
            </line>
        </symbol>

        <!-- Mini car for stat cards and small icons -->
        <symbol id="miniCar" viewBox="0 0 60 24">
            <path d="M8,17 L5,17 Q2,17 2,14 L2,13 Q2,12 4,12 L10,12 L14,7 Q15,6 17,6 L38,6 Q40,6 41,7 L47,12 L55,12 Q57,12 57,13 L57,14 Q57,17 54,17 L51,17" fill="currentColor" opacity="0.9" />
            <path d="M15,11 L18,7 Q19,6 20,6 L33,6 Q34,6 35,7 L39,11 Z" fill="rgba(255,255,255,0.2)" />
            <circle cx="12" cy="17" r="3.5" fill="#1e293b" stroke="rgba(255,255,255,0.2)" stroke-width="0.8" />
            <circle cx="12" cy="17" r="1.5" fill="rgba(255,255,255,0.15)" />
            <circle cx="48" cy="17" r="3.5" fill="#1e293b" stroke="rgba(255,255,255,0.2)" stroke-width="0.8" />
            <circle cx="48" cy="17" r="1.5" fill="rgba(255,255,255,0.15)" />
            <rect x="55" y="12" width="2" height="2" rx="0.5" fill="rgba(255,255,180,0.8)" />
        </symbol>

        <!-- Tiny car for grid items -->
        <symbol id="tinyCar" viewBox="0 0 40 16">
            <path d="M5,11 L3,11 Q1,11 1,9 L1,8.5 Q1,7.5 3,7.5 L7,7.5 L10,4 Q11,3 12,3 L26,3 Q27,3 28,4 L32,7.5 L37,7.5 Q39,7.5 39,8.5 L39,9 Q39,11 37,11 L35,11" fill="currentColor" opacity="0.9" />
            <path d="M11,7 L13,4.5 Q14,3.5 15,3.5 L23,3.5 Q24,3.5 25,4.5 L27,7 Z" fill="rgba(255,255,255,0.2)" />
            <circle cx="8" cy="11" r="2.5" fill="#1e293b" stroke="rgba(255,255,255,0.2)" stroke-width="0.5" />
            <circle cx="8" cy="11" r="1" fill="rgba(255,255,255,0.15)" />
            <circle cx="33" cy="11" r="2.5" fill="#1e293b" stroke="rgba(255,255,255,0.2)" stroke-width="0.5" />
            <circle cx="33" cy="11" r="1" fill="rgba(255,255,255,0.15)" />
        </symbol>
    </svg>

    <!-- Loading Overlay with Animated Car -->
    <div class="loading-overlay" id="loadingOverlay">
        <div class="loading-car-scene">
            <svg class="loading-car-svg" viewBox="0 0 100 40">
                <use href="#carSVG" />
            </svg>
            <div class="loading-road"></div>
        </div>
        <div class="loading-text">กำลังโหลดข้อมูล<span class="loading-dots"></span></div>
    </div>

    <!-- Refresh Progress Bar -->
    <div class="refresh-bar" id="refreshBar"></div>

    <!-- Animated Background -->
    <div class="bg-particles">
        <div class="bg-orb"></div>
        <div class="bg-orb"></div>
        <div class="bg-orb"></div>
    </div>
    <div class="grid-overlay"></div>



    <!-- ═══ Dashboard ═══ -->
    <div class="tv-dashboard">

        <!-- ─── Header ─── -->
        <header class="dash-header">
            <div class="dash-header-left">
                <div class="dash-logo">
                    <svg class="logo-car-svg" viewBox="0 0 60 24">
                        <use href="#miniCar" style="color:#fff" />
                    </svg>
                </div>
                <div class="dash-title">
                    <h1>Car Reservation Dashboard</h1>
                    <div class="subtitle">ระบบติดตามการจองรถส่วนกลาง</div>
                </div>
            </div>

            <div class="dash-header-center">
                <div class="plant-selector" id="plantSelector">
                    <!-- Dynamically populated -->
                </div>
                <div class="date-picker-wrap">
                    <button class="date-nav-btn" id="datePrev" title="วันก่อนหน้า">
                        <i class="bi bi-chevron-left"></i>
                    </button>
                    <div class="date-input-wrap">
                        <i class="bi bi-calendar3"></i>
                        <input type="date" id="datePicker" class="date-input">
                    </div>
                    <button class="date-nav-btn" id="dateNext" title="วันถัดไป">
                        <i class="bi bi-chevron-right"></i>
                    </button>
                    <button class="date-today-btn" id="dateTodayBtn" title="กลับไปวันนี้" style="display:none">
                        <i class="bi bi-arrow-counterclockwise"></i> วันนี้
                    </button>
                </div>
            </div>

            <div class="dash-header-right">
                <div class="dash-date">
                    <div class="date-text" id="dateText"></div>
                    <div class="dash-clock" id="clockDisplay"></div>
                </div>
                <div class="live-badge" id="liveBadge">
                    <div class="live-dot"></div>
                    LIVE
                </div>
            </div>
        </header>

        <!-- ─── Main Content ─── -->
        <div class="dash-content">

            <!-- Stat Cards -->
            <div class="stat-cards-row">
                <div class="tv-stat-card blue" id="cardTotal" data-filter="all" data-title="จองวันนี้ทั้งหมด">
                    <div class="card-glow"></div>
                    <div class="card-car-anim">
                        <svg viewBox="0 0 60 24">
                            <use href="#miniCar" style="color:#3b82f6" />
                        </svg>
                    </div>
                    <div class="stat-card-top">
                        <div class="stat-card-icon">
                            <svg class="icon-car-svg" viewBox="0 0 60 24">
                                <use href="#miniCar" style="color:#3b82f6" />
                            </svg>
                        </div>
                        <div class="stat-card-label">รายการจอง</div>
                    </div>
                    <div class="stat-card-value count-up" data-target="0" id="statTotal">0</div>
                    <div class="stat-card-unit">Total Bookings</div>
                </div>

                <div class="tv-stat-card amber" id="cardPending" data-filter="pending" data-title="รอจ่ายกุญแจ">
                    <div class="card-glow"></div>
                    <div class="card-car-anim delay1">
                        <svg viewBox="0 0 60 24">
                            <use href="#miniCar" style="color:#f59e0b" />
                        </svg>
                    </div>
                    <div class="stat-card-top">
                        <div class="stat-card-icon"><i class="bi bi-hourglass-split"></i></div>
                        <div class="stat-card-label">รอจ่ายกุญแจ</div>
                    </div>
                    <div class="stat-card-value count-up" data-target="0" id="statPending">0</div>
                    <div class="stat-card-unit">Pending</div>
                </div>

                <div class="tv-stat-card green" id="cardIssued" data-filter="issued" data-title="รอคืนกุญแจ">
                    <div class="card-glow"></div>
                    <div class="card-car-anim delay2">
                        <svg viewBox="0 0 60 24">
                            <use href="#miniCar" style="color:#10b981" />
                        </svg>
                    </div>
                    <div class="stat-card-top">
                        <div class="stat-card-icon"><i class="bi bi-key-fill"></i></div>
                        <div class="stat-card-label">รอคืนกุญแจ</div>
                    </div>
                    <div class="stat-card-value count-up" data-target="0" id="statIssued">0</div>
                    <div class="stat-card-unit">Waiting Return</div>
                </div>



                <div class="tv-stat-card red" id="cardClosed" data-filter="closed" data-title="สถานะใบจอง">
                    <div class="card-glow"></div>
                    <div class="card-car-anim delay4">
                        <svg viewBox="0 0 60 24">
                            <use href="#miniCar" style="color:#ef4444" />
                        </svg>
                    </div>
                    <div class="stat-card-top">
                        <div class="stat-card-icon"><i class="bi bi-file-check-fill"></i></div>
                        <div class="stat-card-label">สถานะใบจอง</div>
                    </div>
                    <div class="stat-card-value count-up" data-target="0" id="statClosed">0</div>
                    <div class="stat-card-unit">Closed</div>
                </div>

                <div class="tv-stat-card orange" id="cardPrivate" data-filter="private" data-title="รถส่วนตัว">
                    <div class="card-glow"></div>
                    <div class="card-car-anim delay5">
                        <svg viewBox="0 0 60 24">
                            <use href="#miniCar" style="color:#f97316" />
                        </svg>
                    </div>
                    <div class="stat-card-top">
                        <div class="stat-card-icon"><i class="bi bi-car-front-fill"></i></div>
                        <div class="stat-card-label">รถส่วนตัว</div>
                    </div>
                    <div class="stat-card-value count-up" data-target="0" id="statPrivate">0</div>
                    <div class="stat-card-unit">Private Car</div>
                </div>

                <div class="tv-stat-card cyan" id="cardDistance" data-filter="distance" data-title="ระยะทาง">
                    <div class="card-glow"></div>
                    <div class="card-car-anim delay6">
                        <svg viewBox="0 0 60 24">
                            <use href="#miniCar" style="color:#06b6d4" />
                        </svg>
                    </div>
                    <div class="stat-card-top">
                        <div class="stat-card-icon"><i class="bi bi-speedometer2"></i></div>
                        <div class="stat-card-label">ระยะทางรวม</div>
                    </div>
                    <div class="stat-card-value count-up" data-target="0" id="statDistance">0</div>
                    <div class="stat-card-unit">กิโลเมตร (KM)</div>
                </div>
            </div>

            <!-- Row 2: 3 Semi-circle Donuts side by side -->
            <div style="grid-column:1/-1;display:grid;grid-template-columns:repeat(3,1fr);gap:4px;min-height:0">
                <!-- Donut 1: ภาพรวมรถส่วนกลาง -->
                <div class="glass-card" style="animation-delay:0.4s;min-height:0">
                    <div class="glass-card-header">
                        <h3><i class="bi bi-truck-front-fill"></i> ภาพรวมรถส่วนกลาง</h3>
                        <span class="fleet-badge fleet-badge-available" id="fleetTotalBadge" style="font-size:0.6rem"></span>
                    </div>
                    <div class="glass-card-body" style="padding:0">
                        <div id="fleetOverviewChart" style="width:100%;flex:1;min-height:0"></div>
                    </div>
                </div>
                <!-- Donut 2: จำนวนรถแยกตามหมวด -->
                <div class="glass-card" style="animation-delay:0.5s;min-height:0">
                    <div class="glass-card-header">
                        <h3><i class="bi bi-pie-chart-fill"></i> จำนวนรถแยกตามหมวด</h3>
                        <span class="fleet-badge fleet-badge-available" id="carGroupTotal" style="font-size:0.6rem"></span>
                    </div>
                    <div class="glass-card-body" style="padding:0">
                        <div id="carGroupChart" style="width:100%;flex:1;min-height:0"></div>
                    </div>
                </div>
                <!-- Donut 3: จำนวนรถแต่ละประเภท -->
                <div class="glass-card" style="animation-delay:0.6s;min-height:0">
                    <div class="glass-card-header">
                        <h3><i class="bi bi-car-front-fill"></i> จำนวนรถแต่ละประเภท</h3>
                        <span class="fleet-badge fleet-badge-available" id="carBodyTotal" style="font-size:0.6rem"></span>
                    </div>
                    <div class="glass-card-body" style="padding:0">
                        <div id="carBodyChart" style="width:100%;flex:1;min-height:0"></div>
                    </div>
                </div>
            </div>

            <!-- Row 3: Top 10 Daily (Dept + Location) -->
            <div style="grid-column:1/-1;display:grid;grid-template-columns:1fr 1fr;gap:4px;min-height:0">
                <div class="glass-card" style="animation-delay:0.7s;min-height:0">
                    <div class="glass-card-header">
                        <h3><i class="bi bi-trophy-fill"></i> Top 10 แผนกจองมากสุด (รายวัน)</h3>
                    </div>
                    <div class="glass-card-body" style="display:flex;flex-direction:column;overflow:hidden">
                        <div id="deptLegend" style="display:none;flex-wrap:wrap;gap:4px 10px;padding:4px 8px 2px;min-height:0;flex-shrink:0"></div>
                        <div class="chart-container" style="position:relative;width:100%;flex:1;min-height:0">
                            <canvas id="weeklyChart"></canvas>
                        </div>
                    </div>
                </div>
                <div class="glass-card" style="animation-delay:0.8s;min-height:0">
                    <div class="glass-card-header">
                        <h3><i class="bi bi-geo-alt-fill"></i> Top 10 สถานที่ไปมากสุด (รายวัน)</h3>
                    </div>
                    <div class="glass-card-body" style="display:flex;flex-direction:column;overflow:hidden">
                        <div class="chart-container" style="position:relative;width:100%;flex:1;min-height:0">
                            <canvas id="locationChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Row 4: Top 10 Monthly (Dept + Location) -->
            <div style="grid-column:1/-1;display:grid;grid-template-columns:1fr 1fr;gap:4px;min-height:0">
                <div class="glass-card" style="animation-delay:0.9s;min-height:0">
                    <div class="glass-card-header">
                        <h3><i class="bi bi-calendar-month-fill"></i> Top 10 แผนกจองมากสุด (รายเดือน)</h3>
                    </div>
                    <div class="glass-card-body" style="display:flex;flex-direction:column;overflow:hidden">
                        <div class="chart-container" style="position:relative;width:100%;flex:1;min-height:0">
                            <canvas id="monthlyDeptChart"></canvas>
                        </div>
                    </div>
                </div>
                <div class="glass-card" style="animation-delay:1.0s;min-height:0">
                    <div class="glass-card-header">
                        <h3><i class="bi bi-pin-map-fill"></i> Top 10 สถานที่ไปมากสุด (รายเดือน)</h3>
                    </div>
                    <div class="glass-card-body" style="display:flex;flex-direction:column;overflow:hidden">
                        <div class="chart-container" style="position:relative;width:100%;flex:1;min-height:0">
                            <canvas id="monthlyLocChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ─── Bottom Ticker ─── -->
        <div class="dash-ticker">
            <div class="ticker-label">
                <i class="bi bi-broadcast"></i> UPDATE
            </div>
            <svg class="ticker-car-icon" viewBox="0 0 60 24">
                <use href="#miniCar" style="color:#3b82f6" />
            </svg>
            <div class="ticker-content">
                <div class="ticker-scroll" id="tickerScroll"></div>
            </div>
        </div>

    </div>

    <!-- ═══ Booking Detail Modal ═══ -->
    <div class="modal-overlay" id="cardModal">
        <div class="modal-container">
            <div class="modal-header">
                <div class="modal-title-wrap">
                    <svg style="width:24px;height:12px;display:inline-block" viewBox="0 0 40 16">
                        <use href="#tinyCar" style="color:var(--accent-cyan)" />
                    </svg>
                    <h2 id="modalTitle">รายละเอียด</h2>
                    <span class="modal-count" id="modalCount">0</span>
                </div>
                <button class="modal-close-btn" onclick="hideCardModal()">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
            <div class="modal-body">
                <table class="modal-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>เวลาใช้</th>
                            <th>ทะเบียน</th>
                            <th>ผู้จอง</th>
                            <th>ปลายทาง</th>
                            <th>จังหวัด</th>
                            <th>ระยะทาง</th>
                            <th>สถานะ</th>
                        </tr>
                    </thead>
                    <tbody id="modalBody"></tbody>
                </table>
                <div class="modal-empty" id="modalEmpty" style="display:none">
                    <i class="bi bi-inbox"></i>
                    <span>ไม่มีรายการ</span>
                </div>
            </div>
        </div>
    </div>

    <script>
        // ═══ Disable ALL Highcharts animations globally (TV perf) ═══
        Highcharts.setOptions({
            chart: { animation: false },
            plotOptions: {
                series: { animation: false }
            }
        });

        // ═══════════════════════════════════════════════════════════
        // TV Dashboard - JavaScript Controller
        // ═══════════════════════════════════════════════════════════

        const REFRESH_INTERVAL = 30000; // 30 seconds
        let currentPlant = '1100';
        let selectedDate = null; // null = today

        let weeklyChart = null;
        let monthlyDeptChart = null;
        let monthlyLocChart = null;
        let refreshTimer = null;
        let autoRefreshInterval = null;
        let dashData = null;
        let modalTimer = null;
        let isModalOpen = false;

        // ─── Date helper ───
        function getTodayStr() {
            const d = new Date();
            return d.getFullYear() + '-' + String(d.getMonth() + 1).padStart(2, '0') + '-' + String(d.getDate()).padStart(2, '0');
        }

        function getEffectiveDate() {
            return selectedDate || getTodayStr();
        }

        // ─── Card Hover Modal ───
        const statusColors = {
            pending: {
                text: 'รอจ่ายกุญแจ',
                cls: 'dep-pending',
                color: '#f59e0b'
            },
            'in-use': {
                text: 'กำลังใช้งาน',
                cls: 'dep-inuse',
                color: '#3b82f6'
            },
            issued: {
                text: 'รอคืนกุญแจ',
                cls: 'dep-departed',
                color: '#10b981'
            },
            returned: {
                text: 'รอปิดใบจอง',
                cls: 'dep-arrived',
                color: '#8b5cf6'
            },
            closed: {
                text: 'ปิดใบจองแล้ว',
                cls: 'dep-closed',
                color: '#ef4444'
            },
            private: {
                text: 'รถส่วนตัว',
                cls: 'dep-private',
                color: '#f97316'
            }
        };

        // Resolve display status: split 'issued' into 'in-use' vs 'issued' based on totalMile
        function resolveDisplayStatus(booking) {
            if (booking.status === 'issued') {
                // totalMile > 0 = บันทึกขาเข้าแล้ว → รอคืนกุญแจ
                // totalMile = 0 = ยังไม่ได้บันทึกขาเข้า → กำลังใช้งาน
                return booking.totalMile > 0 ? 'issued' : 'in-use';
            }
            return booking.status;
        }

        function showCardModal(filterType, title) {
            if (!dashData || !dashData.recentBookings) return;

            let bookings;
            let totalCount = 0;

            // Get correct total count from stats
            const stats = dashData.stats;
            if (filterType === 'all') {
                bookings = dashData.recentBookings;
                totalCount = stats.totalBookings;
            } else if (filterType === 'distance') {
                bookings = dashData.recentBookings.filter(b => b.totalMile > 0);
                totalCount = bookings.length;
            } else if (filterType === 'pending') {
                bookings = dashData.recentBookings.filter(b => b.status === 'pending');
                totalCount = stats.pendingCount;
            } else if (filterType === 'issued') {
                bookings = dashData.recentBookings.filter(b => b.status === 'issued');
                totalCount = stats.issuedCount;
            } else if (filterType === 'closed') {
                bookings = dashData.recentBookings.filter(b => b.status === 'returned' || b.status === 'closed');
                totalCount = (stats.returnedCount || 0) + (stats.closedCount || 0);
            } else if (filterType === 'private') {
                bookings = dashData.recentBookings.filter(b => b.status === 'private');
                totalCount = stats.privateCount;
            }

            document.getElementById('modalTitle').textContent = title;

            // Show correct count
            let countText;
            if (bookings.length < totalCount) {
                countText = `แสดง ${bookings.length} จาก ${totalCount} รายการ`;
            } else {
                countText = totalCount + ' รายการ';
            }
            document.getElementById('modalCount').textContent = countText;

            const tbody = document.getElementById('modalBody');
            const emptyEl = document.getElementById('modalEmpty');

            if (bookings.length === 0) {
                tbody.innerHTML = '';
                emptyEl.style.display = 'flex';
            } else {
                emptyEl.style.display = 'none';
                tbody.innerHTML = bookings.map((b, i) => {
                    const displayStatus = resolveDisplayStatus(b);
                    const st = statusColors[displayStatus] || statusColors['pending'];
                    return `<tr>
                        <td class="modal-td-num">${i + 1}</td>
                        <td class="modal-td-time">${b.usedTime || '—'}</td>
                        <td class="modal-td-car">
                            <svg class="modal-car-icon" viewBox="0 0 40 16" style="color:${st.color}"><use href="#tinyCar"/></svg>
                            ${b.carLicense}
                        </td>
                        <td>${b.userName || '—'}</td>
                        <td>${b.destination || '—'}</td>
                        <td>${b.province || '—'}</td>
                        <td class="modal-td-mile">${b.totalMile > 0 ? b.totalMile.toLocaleString() + ' km' : '—'}</td>
                        <td><span class="dep-status-badge ${st.cls}">${st.text}</span></td>
                    </tr>`;
                }).join('');
            }

            const modal = document.getElementById('cardModal');
            modal.classList.add('active');
            isModalOpen = true;
        }

        function hideCardModal() {
            const modal = document.getElementById('cardModal');
            modal.classList.remove('active');
            isModalOpen = false;
        }

        // Attach click events to stat cards
        document.addEventListener('DOMContentLoaded', () => {
            const cards = document.querySelectorAll('.tv-stat-card[data-filter]');
            const modal = document.getElementById('cardModal');

            cards.forEach(card => {
                card.addEventListener('click', () => {
                    const filter = card.dataset.filter;
                    const title = card.dataset.title;
                    showCardModal(filter, title);
                });
            });

            // Click overlay to close
            modal.addEventListener('click', (e) => {
                if (e.target === modal) hideCardModal();
            });
        });

        // ─── SVG Templates for JS ───
        function getMiniCarSVG(color) {
            return `<svg class="grid-car-svg" viewBox="0 0 40 16" style="color:${color}"><use href="#tinyCar"/></svg>`;
        }

        function getRecentCarSVG(color) {
            return `<svg class="recent-item-car-icon" viewBox="0 0 40 16" style="color:${color}"><use href="#tinyCar"/></svg>`;
        }

        // ─── Clock ───
        function updateClock() {
            const now = new Date();
            const h = String(now.getHours()).padStart(2, '0');
            const m = String(now.getMinutes()).padStart(2, '0');
            const s = String(now.getSeconds()).padStart(2, '0');
            document.getElementById('clockDisplay').innerHTML = `${h}:${m}:${s}`;
        }
        setInterval(updateClock, 1000);
        updateClock();

        // ─── Thai Date ───
        function setThaiDate(text) {
            document.getElementById('dateText').textContent = text;
        }

        // ─── Animated Counter ───
        function animateCounter(el, target, duration = 1500) {
            const start = parseInt(el.textContent.replace(/,/g, '')) || 0;
            if (start === target) return;

            const increment = target > start ? 1 : -1;
            const steps = Math.abs(target - start);
            const stepTime = Math.max(Math.floor(duration / steps), 20);
            let current = start;

            const timer = setInterval(() => {
                const remaining = Math.abs(target - current);
                const jump = Math.max(1, Math.floor(remaining / 10));
                current += increment * jump;

                if ((increment > 0 && current >= target) || (increment < 0 && current <= target)) {
                    current = target;
                    clearInterval(timer);
                }
                el.textContent = current.toLocaleString();
            }, stepTime);
        }

        // ─── Update Stats ───
        function updateStats(stats) {
            const pairs = [
                ['statTotal', stats.totalBookings],
                ['statPending', stats.pendingCount],
                ['statIssued', stats.issuedCount],
                ['statClosed', (stats.returnedCount || 0) + (stats.closedCount || 0)],
                ['statPrivate', stats.privateCount],
                ['statDistance', stats.totalDistance],
            ];
            pairs.forEach(([id, val]) => {
                animateCounter(document.getElementById(id), val);
            });
        }

        // ─── Top 10 Department Chart (Bar + Line Combo) ───
        function updateTopDeptChart(topDepts) {
            const ctx = document.getElementById('weeklyChart').getContext('2d');

            if (!topDepts || topDepts.length === 0) {
                if (weeklyChart) {
                    weeklyChart.destroy();
                    weeklyChart = null;
                }
                return;
            }

            const items = topDepts.slice(0, 10);
            const labels = items.map(d => d.name);
            const dataValues = items.map(d => d.count);

            const barColors = [
                'rgba(59, 130, 246, 0.95)',
                'rgba(96, 165, 250, 0.90)',
                'rgba(59, 130, 246, 0.78)',
                'rgba(96, 165, 250, 0.72)',
                'rgba(59, 130, 246, 0.62)',
                'rgba(96, 165, 250, 0.55)',
                'rgba(59, 130, 246, 0.48)',
                'rgba(96, 165, 250, 0.42)',
                'rgba(59, 130, 246, 0.36)',
                'rgba(96, 165, 250, 0.32)'
            ];

            if (weeklyChart) {
                weeklyChart.data.labels = labels;
                weeklyChart.data.datasets[0].data = dataValues;
                weeklyChart.data.datasets[0].backgroundColor = barColors.slice(0, items.length);
                weeklyChart.data.datasets[1].data = dataValues;
                weeklyChart.update('active');
                renderDeptLegend(labels, dataValues, barColors);
                return;
            }

            weeklyChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                            label: 'จำนวนครั้ง',
                            data: dataValues,
                            backgroundColor: barColors.slice(0, items.length),
                            borderColor: barColors.map(c => c.replace('0.85', '1')),
                            borderWidth: 1,
                            borderRadius: 6,
                            borderSkipped: false,
                            maxBarThickness: 50,
                            order: 2
                        },
                        {
                            label: 'แนวโน้ม',
                            data: dataValues,
                            type: 'line',
                            borderColor: 'rgba(34, 211, 238, 1)',
                            backgroundColor: 'rgba(34, 211, 238, 0.08)',
                            borderWidth: 2.5,
                            pointBackgroundColor: '#22d3ee',
                            pointBorderColor: '#0b1120',
                            pointBorderWidth: 2,
                            pointRadius: 5,
                            pointHoverRadius: 7,
                            tension: 0.3,
                            fill: true,
                            order: 1
                        }
                    ]
                },
                plugins: [ChartDataLabels],
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    layout: {
                        padding: {
                            top: 12
                        }
                    },
                    plugins: {
                        legend: {
                            display: false
                        },
                        datalabels: {
                            display: function(context) {
                                return context.datasetIndex === 0;
                            },
                            anchor: 'end',
                            align: 'top',
                            font: {
                                family: "'Inter', sans-serif",
                                size: 9,
                                weight: '800'
                            },
                            color: '#e2e8f0',
                            formatter: (value) => value > 0 ? value + ' คัน' : ''
                        },
                        tooltip: {
                            backgroundColor: 'rgba(15,23,42,0.95)',
                            titleColor: '#e2e8f0',
                            bodyColor: '#94a3b8',
                            borderColor: 'rgba(255,255,255,0.1)',
                            borderWidth: 1,
                            cornerRadius: 8,
                            padding: 12,
                            titleFont: {
                                family: "'Sarabun', sans-serif",
                                size: 13
                            },
                            bodyFont: {
                                family: "'Sarabun', sans-serif"
                            },
                            filter: function(tooltipItem) {
                                return tooltipItem.datasetIndex === 0;
                            },
                            callbacks: {
                                label: (ctx) => ` ${ctx.parsed.y} คัน`
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                display: false
                            },
                            border: {
                                color: 'rgba(255,255,255,0.15)'
                            },
                            ticks: {
                                color: '#e2e8f0',
                                font: {
                                    family: "'Sarabun', sans-serif",
                                    size: 14,
                                    weight: '700'
                                },
                                stepSize: 1,
                                padding: 6
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            },
                            ticks: {
                                color: '#e2e8f0',
                                font: {
                                    family: "'Sarabun', sans-serif",
                                    size: 13,
                                    weight: '600'
                                },
                                maxRotation: 45,
                                minRotation: 0
                            }
                        }
                    },
                    animation: {
                        duration: 1500,
                        easing: 'easeOutElastic',
                        delay: function(context) {
                            let delay = 0;
                            if (context.type === 'data' && context.mode === 'default') {
                                delay = context.dataIndex * 120 + context.datasetIndex * 200;
                            }
                            return delay;
                        }
                    }
                }
            });

            renderDeptLegend(labels, dataValues, barColors);
        }

        function renderDeptLegend(labels, values, colors) {
            const container = document.getElementById('deptLegend');
            if (!container) return;
            container.innerHTML = labels.map((name, i) => {
                const color = colors[i] || '#888';
                const solidColor = color.replace('0.85', '1');
                return `<div style="display:flex;align-items:center;gap:4px;white-space:nowrap">
                    <span style="width:10px;height:10px;border-radius:3px;background:${solidColor};display:inline-block;flex-shrink:0;box-shadow:0 0 6px ${solidColor}"></span>
                    <span style="font-size:0.78rem;font-weight:600;color:#e2e8f0;font-family:'Sarabun',sans-serif">${name}</span>
                    <span style="font-size:0.72rem;font-weight:800;color:${solidColor};font-family:'Inter',sans-serif">${values[i]}</span>
                </div>`;
            }).join('');
        }

        // ─── Top 10 Location Chart (Bar + Line Combo) ───
        let locationChart = null;

        function updateTopLocationChart(topLocs) {
            const ctx = document.getElementById('locationChart').getContext('2d');

            if (!topLocs || topLocs.length === 0) {
                if (locationChart) {
                    locationChart.destroy();
                    locationChart = null;
                }
                return;
            }

            const items = topLocs.slice(0, 10);
            const labels = items.map(d => d.name);
            const dataValues = items.map(d => d.count);

            const barColors = [
                'rgba(20, 184, 166, 0.95)',
                'rgba(45, 212, 191, 0.90)',
                'rgba(20, 184, 166, 0.78)',
                'rgba(45, 212, 191, 0.72)',
                'rgba(20, 184, 166, 0.62)',
                'rgba(45, 212, 191, 0.55)',
                'rgba(20, 184, 166, 0.48)',
                'rgba(45, 212, 191, 0.42)',
                'rgba(20, 184, 166, 0.36)',
                'rgba(45, 212, 191, 0.32)'
            ];

            if (locationChart) {
                locationChart.data.labels = labels;
                locationChart.data.datasets[0].data = dataValues;
                locationChart.data.datasets[0].backgroundColor = barColors.slice(0, items.length);
                locationChart.data.datasets[1].data = dataValues;
                locationChart.update('active');
                return;
            }

            locationChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                            label: 'จำนวนครั้ง',
                            data: dataValues,
                            backgroundColor: barColors.slice(0, items.length),
                            borderColor: barColors.map(c => c.replace('0.85', '1')),
                            borderWidth: 1,
                            borderRadius: 6,
                            borderSkipped: false,
                            maxBarThickness: 50,
                            order: 2
                        },
                        {
                            label: 'แนวโน้ม',
                            data: dataValues,
                            type: 'line',
                            borderColor: 'rgba(52, 211, 153, 1)',
                            backgroundColor: 'rgba(52, 211, 153, 0.08)',
                            borderWidth: 2.5,
                            pointBackgroundColor: '#34d399',
                            pointBorderColor: '#0b1120',
                            pointBorderWidth: 2,
                            pointRadius: 5,
                            pointHoverRadius: 7,
                            tension: 0.3,
                            fill: true,
                            order: 1
                        }
                    ]
                },
                plugins: [ChartDataLabels],
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    layout: {
                        padding: {
                            top: 12
                        }
                    },
                    plugins: {
                        legend: {
                            display: false
                        },
                        datalabels: {
                            display: function(context) {
                                return context.datasetIndex === 0;
                            },
                            anchor: 'end',
                            align: 'top',
                            font: {
                                family: "'Inter', sans-serif",
                                size: 9,
                                weight: '800'
                            },
                            color: '#e2e8f0',
                            formatter: (value) => value > 0 ? value + ' คัน' : ''
                        },
                        tooltip: {
                            backgroundColor: 'rgba(15,23,42,0.95)',
                            titleColor: '#e2e8f0',
                            bodyColor: '#94a3b8',
                            borderColor: 'rgba(255,255,255,0.1)',
                            borderWidth: 1,
                            cornerRadius: 8,
                            padding: 12,
                            titleFont: {
                                family: "'Sarabun', sans-serif",
                                size: 13
                            },
                            bodyFont: {
                                family: "'Sarabun', sans-serif"
                            },
                            filter: function(tooltipItem) {
                                return tooltipItem.datasetIndex === 0;
                            },
                            callbacks: {
                                label: (ctx) => ` ${ctx.parsed.y} คัน`
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                display: false
                            },
                            border: {
                                color: 'rgba(255,255,255,0.15)'
                            },
                            ticks: {
                                color: '#e2e8f0',
                                font: {
                                    family: "'Sarabun', sans-serif",
                                    size: 14,
                                    weight: '700'
                                },
                                stepSize: 1,
                                padding: 6
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            },
                            ticks: {
                                color: '#e2e8f0',
                                font: {
                                    family: "'Sarabun', sans-serif",
                                    size: 11,
                                    weight: '600'
                                },
                                maxRotation: 45,
                                minRotation: 0,
                                callback: function(value, index) {
                                    const label = this.getLabelForValue(index);
                                    return label.length > 15 ? label.substring(0, 13) + '...' : label;
                                }
                            }
                        }
                    },
                    animation: {
                        duration: 1500,
                        easing: 'easeOutElastic',
                        delay: function(context) {
                            let delay = 0;
                            if (context.type === 'data' && context.mode === 'default') {
                                delay = context.dataIndex * 120 + context.datasetIndex * 200;
                            }
                            return delay;
                        }
                    }
                }
            });
        }

        // ─── Monthly Top 10 Department Chart ───
        function updateMonthlyDeptChart(topDepts) {
            try {
                const ctx = document.getElementById('monthlyDeptChart');
                if (!ctx) return;

                if (!topDepts || topDepts.length === 0) {
                    if (monthlyDeptChart) {
                        monthlyDeptChart.destroy();
                        monthlyDeptChart = null;
                    }
                    return;
                }

                const items = topDepts.slice(0, 10);
                const labels = items.map(d => d.name);
                const dataValues = items.map(d => d.count);
                const colors = [
                    'rgba(129, 140, 248, 0.95)', 'rgba(167, 139, 250, 0.90)',
                    'rgba(129, 140, 248, 0.78)', 'rgba(167, 139, 250, 0.72)',
                    'rgba(129, 140, 248, 0.62)', 'rgba(167, 139, 250, 0.55)',
                    'rgba(129, 140, 248, 0.48)', 'rgba(167, 139, 250, 0.42)',
                    'rgba(129, 140, 248, 0.36)', 'rgba(167, 139, 250, 0.32)'
                ];

                if (monthlyDeptChart) {
                    monthlyDeptChart.data.labels = labels;
                    monthlyDeptChart.data.datasets[0].data = dataValues;
                    monthlyDeptChart.data.datasets[0].backgroundColor = colors.slice(0, items.length);
                    monthlyDeptChart.data.datasets[1].data = dataValues;
                    monthlyDeptChart.update('active');
                    return;
                }

                monthlyDeptChart = new Chart(ctx.getContext('2d'), {
                    type: 'bar',
                    data: {
                        labels,
                        datasets: [{
                                label: 'count',
                                data: dataValues,
                                backgroundColor: colors.slice(0, items.length),
                                borderRadius: 6,
                                borderSkipped: false,
                                maxBarThickness: 40,
                                order: 2
                            },
                            {
                                label: 'trend',
                                data: dataValues,
                                type: 'line',
                                borderColor: 'rgba(34, 211, 238, 1)',
                                backgroundColor: 'rgba(34, 211, 238, 0.08)',
                                borderWidth: 2.5,
                                pointBackgroundColor: '#22d3ee',
                                pointBorderColor: '#0b1120',
                                pointBorderWidth: 2,
                                pointRadius: 5,
                                pointHoverRadius: 7,
                                tension: 0.3,
                                fill: true,
                                order: 1,
                                datalabels: {
                                    display: false
                                }
                            }
                        ]
                    },
                    plugins: [ChartDataLabels],
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        layout: {
                            padding: {
                                top: 12
                            }
                        },
                        plugins: {
                            legend: {
                                display: false
                            },
                            datalabels: {
                                anchor: 'end',
                                align: 'top',
                                font: {
                                    family: "'Inter', sans-serif",
                                    size: 9,
                                    weight: '800'
                                },
                                color: '#e2e8f0',
                                formatter: v => v > 0 ? v + ' \u0e04\u0e31\u0e19' : ''
                            },
                            tooltip: {
                                backgroundColor: 'rgba(15,23,42,0.95)',
                                titleColor: '#e2e8f0',
                                bodyColor: '#94a3b8',
                                borderColor: 'rgba(255,255,255,0.1)',
                                borderWidth: 1,
                                cornerRadius: 8,
                                padding: 12,
                                filter: function(t) {
                                    return t.datasetIndex === 0;
                                },
                                callbacks: {
                                    label: c => ` ${c.parsed.y} \u0e04\u0e31\u0e19`
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                grid: {
                                    display: false
                                },
                                border: {
                                    color: 'rgba(255,255,255,0.15)'
                                },
                                ticks: {
                                    color: '#e2e8f0',
                                    font: {
                                        family: "'Sarabun', sans-serif",
                                        size: 14,
                                        weight: '700'
                                    },
                                    stepSize: 1,
                                    padding: 6
                                }
                            },
                            x: {
                                grid: {
                                    display: false
                                },
                                ticks: {
                                    color: '#e2e8f0',
                                    font: {
                                        family: "'Sarabun', sans-serif",
                                        size: 13,
                                        weight: '600'
                                    },
                                    maxRotation: 45,
                                    minRotation: 0
                                }
                            }
                        },
                        animation: {
                            duration: 1500,
                            easing: 'easeOutElastic',
                            delay: function(context) {
                                let delay = 0;
                                if (context.type === 'data' && context.mode === 'default') {
                                    delay = context.dataIndex * 120 + context.datasetIndex * 200;
                                }
                                return delay;
                            }
                        }
                    }
                });
            } catch (e) {
                console.warn('monthlyDeptChart error:', e);
            }
        }

        // ─── Monthly Top 10 Location Chart ───
        function updateMonthlyLocChart(topLocs) {
            try {
                const ctx = document.getElementById('monthlyLocChart');
                if (!ctx) return;

                if (!topLocs || topLocs.length === 0) {
                    if (monthlyLocChart) {
                        monthlyLocChart.destroy();
                        monthlyLocChart = null;
                    }
                    return;
                }

                const items = topLocs.slice(0, 10);
                const labels = items.map(d => d.name);
                const dataValues = items.map(d => d.count);
                const colors = [
                    'rgba(52, 211, 153, 0.95)', 'rgba(110, 231, 183, 0.90)',
                    'rgba(52, 211, 153, 0.78)', 'rgba(110, 231, 183, 0.72)',
                    'rgba(52, 211, 153, 0.62)', 'rgba(110, 231, 183, 0.55)',
                    'rgba(52, 211, 153, 0.48)', 'rgba(110, 231, 183, 0.42)',
                    'rgba(52, 211, 153, 0.36)', 'rgba(110, 231, 183, 0.32)'
                ];

                if (monthlyLocChart) {
                    monthlyLocChart.data.labels = labels;
                    monthlyLocChart.data.datasets[0].data = dataValues;
                    monthlyLocChart.data.datasets[0].backgroundColor = colors.slice(0, items.length);
                    monthlyLocChart.data.datasets[1].data = dataValues;
                    monthlyLocChart.update('active');
                    return;
                }

                monthlyLocChart = new Chart(ctx.getContext('2d'), {
                    type: 'bar',
                    data: {
                        labels,
                        datasets: [{
                                label: 'count',
                                data: dataValues,
                                backgroundColor: colors.slice(0, items.length),
                                borderRadius: 6,
                                borderSkipped: false,
                                maxBarThickness: 40,
                                order: 2
                            },
                            {
                                label: 'trend',
                                data: dataValues,
                                type: 'line',
                                borderColor: 'rgba(52, 211, 153, 1)',
                                backgroundColor: 'rgba(52, 211, 153, 0.08)',
                                borderWidth: 2.5,
                                pointBackgroundColor: '#34d399',
                                pointBorderColor: '#0b1120',
                                pointBorderWidth: 2,
                                pointRadius: 5,
                                pointHoverRadius: 7,
                                tension: 0.3,
                                fill: true,
                                order: 1,
                                datalabels: {
                                    display: false
                                }
                            }
                        ]
                    },
                    plugins: [ChartDataLabels],
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        layout: {
                            padding: {
                                top: 12
                            }
                        },
                        plugins: {
                            legend: {
                                display: false
                            },
                            datalabels: {
                                anchor: 'end',
                                align: 'top',
                                font: {
                                    family: "'Inter', sans-serif",
                                    size: 9,
                                    weight: '800'
                                },
                                color: '#e2e8f0',
                                formatter: v => v > 0 ? v + ' \u0e04\u0e31\u0e19' : ''
                            },
                            tooltip: {
                                backgroundColor: 'rgba(15,23,42,0.95)',
                                titleColor: '#e2e8f0',
                                bodyColor: '#94a3b8',
                                borderColor: 'rgba(255,255,255,0.1)',
                                borderWidth: 1,
                                cornerRadius: 8,
                                padding: 12,
                                filter: function(t) {
                                    return t.datasetIndex === 0;
                                },
                                callbacks: {
                                    label: c => ` ${c.parsed.y} \u0e04\u0e31\u0e19`
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                grid: {
                                    display: false
                                },
                                border: {
                                    color: 'rgba(255,255,255,0.15)'
                                },
                                ticks: {
                                    color: '#e2e8f0',
                                    font: {
                                        family: "'Sarabun', sans-serif",
                                        size: 14,
                                        weight: '700'
                                    },
                                    stepSize: 1,
                                    padding: 6
                                }
                            },
                            x: {
                                grid: {
                                    display: false
                                },
                                ticks: {
                                    color: '#e2e8f0',
                                    font: {
                                        family: "'Sarabun', sans-serif",
                                        size: 11,
                                        weight: '600'
                                    },
                                    maxRotation: 45,
                                    minRotation: 0,
                                    callback: function(v, i) {
                                        const l = this.getLabelForValue(i);
                                        return l.length > 15 ? l.substring(0, 13) + '...' : l;
                                    }
                                }
                            }
                        },
                        animation: {
                            duration: 1500,
                            easing: 'easeOutElastic',
                            delay: function(context) {
                                let delay = 0;
                                if (context.type === 'data' && context.mode === 'default') {
                                    delay = context.dataIndex * 120 + context.datasetIndex * 200;
                                }
                                return delay;
                            }
                        }
                    }
                });
            } catch (e) {
                console.warn('monthlyLocChart error:', e);
            }
        }

        let fleetOverviewChart = null;


        function updateFleetOverview(stats, carStatuses) {
            const totalBadge = document.getElementById('fleetTotalBadge');
            if (totalBadge) totalBadge.innerHTML = `<i class="bi bi-truck-front-fill"></i> รวม ${stats.totalCars} คัน`;

            const seriesData = [{
                    name: 'พร้อมใช้งาน',
                    y: (stats.totalCars || 0) - (stats.carsMaintenance || 0),
                    color: '#34d399'
                },
                {
                    name: 'ไม่พร้อมใช้งาน',
                    y: stats.carsMaintenance || 0,
                    color: '#f87171'
                }
            ].filter(d => d.y > 0);

            if (fleetOverviewChart) {
                fleetOverviewChart.series[0].setData(seriesData, true, false);
                return;
            }

            fleetOverviewChart = Highcharts.chart('fleetOverviewChart', {
                chart: {
                    type: 'pie',
                    backgroundColor: 'transparent',
                    spacing: [5, 5, 5, 5],
                    animation: false,
                    style: {
                        fontFamily: "'Sarabun', sans-serif"
                    }
                },
                title: {
                    text: null
                },
                credits: {
                    enabled: false
                },
                plotOptions: {
                    pie: {
                        innerSize: '50%',
                        startAngle: -90,
                        allowPointSelect: true,
                        cursor: 'pointer',
                        borderWidth: 2,
                        borderColor: 'rgba(11,17,32,0.6)',
                        dataLabels: {
                            enabled: true,
                            format: '<b>{point.name}</b>: {point.y} คัน ({point.percentage:.0f}%)',
                            style: {
                                color: '#e2e8f0',
                                fontSize: '12px',
                                fontWeight: '700',
                                textOutline: '2px rgba(11,17,32,0.8)'
                            },
                            connectorColor: '#475569',
                            connectorWidth: 1,
                            distance: 6
                        },
                        states: {
                            hover: {
                                brightness: 0.15,
                                halo: {
                                    size: 8,
                                    opacity: 0.3
                                }
                            }
                        }
                    }
                },
                tooltip: {
                    backgroundColor: 'rgba(15,23,42,0.95)',
                    borderColor: 'rgba(255,255,255,0.1)',
                    borderRadius: 8,
                    style: {
                        color: '#e2e8f0',
                        fontSize: '12px',
                        fontFamily: "'Sarabun', sans-serif"
                    },
                    pointFormat: '<b>{point.y} คัน</b> ({point.percentage:.1f}%)'
                },
                legend: {
                    enabled: false
                },
                series: [{
                    name: 'สถานะรถ',
                    data: seriesData
                }]
            });
        }



        // ─── Animated Car Fleet Groups ───
        function updateFleetGroups(bookings, stats) {
            const fleet = document.getElementById('fleetGroups');
            const summary = document.getElementById('carSummary');

            // Summary
            summary.innerHTML = `
                <div class="car-summary-item">
                    <div class="car-summary-dot available"></div>
                    <span style="color:var(--accent-green)">${stats.carsAvailable}</span>
                    <span style="color:var(--text-muted)">ว่าง</span>
                </div>
                <div class="car-summary-item">
                    <div class="car-summary-dot in-use"></div>
                    <span style="color:var(--accent-amber)">${stats.carsInUse}</span>
                    <span style="color:var(--text-muted)">ใช้งาน</span>
                </div>
            `;

            // Count bookings by status
            const groups = {
                issued: [],
                pending: [],
                completed: []
            };
            bookings.forEach(b => {
                if (b.status === 'issued') groups.issued.push(b);
                else if (b.status === 'pending') groups.pending.push(b);
                else groups.completed.push(b); // returned + closed
            });

            const groupConfigs = [{
                    key: 'issued',
                    label: 'DEPARTED',
                    sublabel: 'ขับออกไปแล้ว',
                    icon: 'bi-arrow-up-right-circle-fill',
                    color: '#10b981',
                    glowColor: 'rgba(16,185,129,0.2)',
                    borderColor: 'rgba(16,185,129,0.3)',
                    animClass: 'fleet-car-driving',
                    items: groups.issued,
                    totalCount: stats.issuedCount || groups.issued.length
                },
                {
                    key: 'pending',
                    label: 'AT GATE',
                    sublabel: 'รอจ่ายกุญแจ',
                    icon: 'bi-clock-fill',
                    color: '#f59e0b',
                    glowColor: 'rgba(245,158,11,0.2)',
                    borderColor: 'rgba(245,158,11,0.3)',
                    animClass: 'fleet-car-waiting',
                    items: groups.pending,
                    totalCount: stats.pendingCount || groups.pending.length
                },
                {
                    key: 'completed',
                    label: 'ARRIVED',
                    sublabel: 'กลับมาแล้ว',
                    icon: 'bi-arrow-down-left-circle-fill',
                    color: '#8b5cf6',
                    glowColor: 'rgba(139,92,246,0.2)',
                    borderColor: 'rgba(139,92,246,0.3)',
                    animClass: 'fleet-car-parked',
                    items: groups.completed,
                    totalCount: (stats.returnedCount || 0) + (stats.closedCount || 0) || groups.completed.length
                }
            ];

            fleet.innerHTML = groupConfigs.map(g => {
                const count = g.totalCount;
                const maxShow = 4;
                const showItems = g.items.slice(0, maxShow);
                const extraCount = count - showItems.length;

                const carIcons = showItems.map((item, idx) => `
                    <div class="fleet-car ${g.animClass}" style="animation-delay:${idx * 0.15}s">
                        <svg viewBox="0 0 40 16" style="color:${g.color}"><use href="#tinyCar"/></svg>
                        ${g.key === 'issued' ? '<div class="fleet-car-speed-lines"></div>' : ''}
                        ${g.key === 'completed' ? '<div class="fleet-car-check"><i class="bi bi-check-lg"></i></div>' : ''}
                        <div class="fleet-car-plate" style="color:${g.color}">${item.carLicense}</div>
                    </div>
                `).join('');

                const extraBadge = extraCount > 0 ?
                    `<div class="fleet-extra-badge" style="color:${g.color}">+${extraCount}</div>` :
                    '';

                return `
                    <div class="fleet-group" style="--group-color:${g.color};--group-glow:${g.glowColor};--group-border:${g.borderColor}">
                        <div class="fleet-group-header">
                            <div class="fleet-group-label">
                                <i class="bi ${g.icon}" style="color:${g.color}"></i>
                                <span>${g.label}</span>
                                <span class="fleet-group-sub">${g.sublabel}</span>
                            </div>
                            <div class="fleet-group-count" style="background:${g.glowColor};color:${g.color}">${count}</div>
                        </div>
                        <div class="fleet-cars-lane">
                            ${count === 0
                                ? '<div class="fleet-empty">— ไม่มี —</div>'
                                : `<div class="fleet-cars-row">${carIcons}${extraBadge}</div>`
                            }
                        </div>
                    </div>
                `;
            }).join('');
        }

        // ─── Car Type Booking (Pie Chart) ───
        let carTypeChart = null;

        function updateCarTypeChart(chartData, stats) {
            if (!chartData || !chartData.datasets) return;

            // Aggregate: sum all days per category
            const catTotals = {};
            chartData.datasets.forEach(ds => {
                const total = ds.data.reduce((a, b) => a + b, 0);
                if (total > 0) catTotals[ds.name] = (catTotals[ds.name] || 0) + total;
            });

            const labels = Object.keys(catTotals);
            const dataValues = Object.values(catTotals);
            const grandTotal = dataValues.reduce((a, b) => a + b, 0);

            const totalEl = document.getElementById('carTypeTotal');
            if (totalEl) totalEl.innerHTML = `<i class="bi bi-calendar-check"></i> รวม ${grandTotal} ครั้ง`;

            const pieColors = [
                '#22d3ee', '#fbbf24', '#a78bfa', '#34d399', '#f87171',
                '#fb923c', '#3b82f6', '#f472b6', '#818cf8', '#38bdf8'
            ];

            const ctx = document.getElementById('carTypeChart').getContext('2d');

            if (carTypeChart) {
                carTypeChart.data.labels = labels;
                carTypeChart.data.datasets[0].data = dataValues;
                carTypeChart.data.datasets[0].backgroundColor = pieColors.slice(0, labels.length);
                carTypeChart.update('active');
            } else {
                carTypeChart = new Chart(ctx, {
                    type: 'doughnut',
                    data: {
                        labels: labels,
                        datasets: [{
                            data: dataValues,
                            backgroundColor: pieColors.slice(0, labels.length),
                            borderWidth: 2,
                            borderColor: 'rgba(11,17,32,0.8)',
                            hoverBorderColor: '#fff',
                            hoverBorderWidth: 2,
                            hoverOffset: 6
                        }]
                    },
                    plugins: [ChartDataLabels],
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        cutout: '55%',
                        plugins: {
                            legend: {
                                display: false
                            },
                            datalabels: {
                                display: function(ctx) {
                                    return ctx.dataset.data[ctx.dataIndex] > 0;
                                },
                                color: '#fff',
                                font: {
                                    family: "'Inter', sans-serif",
                                    size: 11,
                                    weight: '800'
                                },
                                formatter: (value) => value
                            },
                            tooltip: {
                                backgroundColor: 'rgba(15,23,42,0.95)',
                                titleColor: '#e2e8f0',
                                bodyColor: '#94a3b8',
                                borderColor: 'rgba(255,255,255,0.1)',
                                borderWidth: 1,
                                cornerRadius: 8,
                                padding: 10,
                                titleFont: {
                                    family: "'Sarabun', sans-serif",
                                    size: 12
                                },
                                bodyFont: {
                                    family: "'Sarabun', sans-serif"
                                },
                                callbacks: {
                                    label: (ctx) => {
                                        const pct = grandTotal > 0 ? Math.round(ctx.parsed / grandTotal * 100) : 0;
                                        return ` ${ctx.label}: ${ctx.parsed} ครั้ง (${pct}%)`;
                                    }
                                }
                            }
                        },
                        animation: {
                            duration: 1200,
                            easing: 'easeOutQuart'
                        }
                    }
                });
            }

            // Custom legend
            const legendEl = document.getElementById('carTypeLegend');
            legendEl.innerHTML = labels.map((label, i) => {
                const pct = grandTotal > 0 ? Math.round(dataValues[i] / grandTotal * 100) : 0;
                return `<div style="display:flex;align-items:center;gap:5px;font-size:0.6rem;color:#94a3b8;font-family:'Sarabun',sans-serif">
                    <span style="width:8px;height:8px;border-radius:50%;background:${pieColors[i]};flex-shrink:0"></span>
                    <span style="white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:70px">${label}</span>
                    <span style="font-weight:800;color:#e2e8f0;font-family:'Inter',sans-serif;font-size:0.75rem;margin-left:auto">${dataValues[i]}</span>
                </div>`;
            }).join('');
        }

        // ─── จำนวนรถแยกตามหมวด (Highcharts 3D Pie) ───
        let carGroupChart = null;

        function updateCarGroupChart(carStatuses) {
            const groupMap = {};
            (carStatuses || []).forEach(car => {
                const grp = car.type || 'อื่นๆ';
                groupMap[grp] = (groupMap[grp] || 0) + 1;
            });

            const total = Object.values(groupMap).reduce((a, b) => a + b, 0);
            const totalEl = document.getElementById('carGroupTotal');
            if (totalEl) totalEl.innerHTML = `<i class="bi bi-truck-front-fill"></i> รวม ${total} คัน`;

            const pieColors = ['#22d3ee', '#fbbf24', '#a78bfa', '#34d399', '#f87171', '#fb923c', '#3b82f6', '#f472b6'];
            const seriesData = Object.entries(groupMap).map(([name, y], i) => ({
                name: name,
                y: y,
                color: pieColors[i % pieColors.length]
            }));

            if (carGroupChart) {
                carGroupChart.series[0].setData(seriesData, true, false);
                return;
            }

            carGroupChart = Highcharts.chart('carGroupChart', {
                chart: {
                    type: 'pie',
                    backgroundColor: 'transparent',
                    spacing: [5, 5, 5, 5],
                    animation: false,
                    style: {
                        fontFamily: "'Sarabun', sans-serif"
                    }
                },
                title: {
                    text: null
                },
                credits: {
                    enabled: false
                },
                plotOptions: {
                    pie: {
                        innerSize: '50%',
                        startAngle: -90,
                        allowPointSelect: true,
                        cursor: 'pointer',
                        borderWidth: 2,
                        borderColor: 'rgba(11,17,32,0.6)',
                        dataLabels: {
                            enabled: true,
                            format: '<b>{point.name}</b>: {point.y} คัน ({point.percentage:.0f}%)',
                            style: {
                                color: '#e2e8f0',
                                fontSize: '12px',
                                fontWeight: '700',
                                textOutline: '2px rgba(11,17,32,0.8)'
                            },
                            connectorColor: '#475569',
                            connectorWidth: 1,
                            distance: 6
                        },
                        states: {
                            hover: {
                                brightness: 0.15,
                                halo: {
                                    size: 8,
                                    opacity: 0.3
                                }
                            }
                        }
                    }
                },
                tooltip: {
                    backgroundColor: 'rgba(15,23,42,0.95)',
                    borderColor: 'rgba(255,255,255,0.1)',
                    borderRadius: 8,
                    style: {
                        color: '#e2e8f0',
                        fontSize: '12px',
                        fontFamily: "'Sarabun', sans-serif"
                    },
                    pointFormat: '<b>{point.y} คัน</b> ({point.percentage:.1f}%)'
                },
                legend: {
                    enabled: false
                },
                series: [{
                    name: 'จำนวนรถ',
                    data: seriesData
                }]
            });
        }

        // ─── จำนวนรถแต่ละประเภท (Highcharts 3D Pie) ───
        let carBodyChart = null;

        function updateCarBodyChart(chartData) {
            if (!chartData || !chartData.labels || chartData.labels.length === 0) return;

            const total = chartData.data.reduce((a, b) => a + b, 0);
            const totalEl = document.getElementById('carBodyTotal');
            if (totalEl) totalEl.innerHTML = `<i class="bi bi-car-front-fill"></i> รวม ${total} คัน`;

            const pieColors = ['#22d3ee', '#3b82f6', '#a78bfa', '#fbbf24', '#34d399', '#f87171', '#fb923c', '#f472b6', '#818cf8', '#38bdf8'];
            const seriesData = chartData.labels.map((name, i) => ({
                name: name,
                y: chartData.data[i],
                color: pieColors[i % pieColors.length]
            }));

            if (carBodyChart) {
                carBodyChart.series[0].setData(seriesData, true, false);
                return;
            }

            carBodyChart = Highcharts.chart('carBodyChart', {
                chart: {
                    type: 'pie',
                    backgroundColor: 'transparent',
                    spacing: [5, 5, 5, 5],
                    animation: false,
                    style: {
                        fontFamily: "'Sarabun', sans-serif"
                    }
                },
                title: {
                    text: null
                },
                credits: {
                    enabled: false
                },
                plotOptions: {
                    pie: {
                        innerSize: '50%',
                        startAngle: -90,
                        allowPointSelect: true,
                        cursor: 'pointer',
                        borderWidth: 2,
                        borderColor: 'rgba(11,17,32,0.6)',
                        dataLabels: {
                            enabled: true,
                            format: '<b>{point.name}</b>: {point.y} คัน ({point.percentage:.0f}%)',
                            style: {
                                color: '#e2e8f0',
                                fontSize: '11px',
                                fontWeight: '700',
                                textOutline: '2px rgba(11,17,32,0.8)'
                            },
                            connectorColor: '#475569',
                            connectorWidth: 1,
                            distance: 6
                        },
                        states: {
                            hover: {
                                brightness: 0.15,
                                halo: {
                                    size: 8,
                                    opacity: 0.3
                                }
                            }
                        }
                    }
                },
                tooltip: {
                    backgroundColor: 'rgba(15,23,42,0.95)',
                    borderColor: 'rgba(255,255,255,0.1)',
                    borderRadius: 8,
                    style: {
                        color: '#e2e8f0',
                        fontSize: '12px',
                        fontFamily: "'Sarabun', sans-serif"
                    },
                    pointFormat: '<b>{point.y} คัน</b> ({point.percentage:.1f}%)'
                },
                legend: {
                    enabled: false
                },
                series: [{
                    name: 'จำนวนรถ',
                    data: seriesData
                }]
            });
        }

        // ─── Bottom Ticker ───
        function updateTicker(bookings) {
            const scroll = document.getElementById('tickerScroll');

            if (bookings.length === 0) {
                scroll.innerHTML = `<span class="ticker-item" style="color:var(--text-muted)">ยังไม่มีรายการจอง</span>`;
                return;
            }

            const statusEmoji = {
                'pending': '⏳',
                'issued': '🔑',
                'returned': '✅',
                'closed': '📋',
                'private': '🚗'
            };

            // Duplicate for seamless scroll
            const items = [...bookings, ...bookings];
            scroll.innerHTML = items.map(b => `
                <span class="ticker-item">
                    ${statusEmoji[b.status] || '📌'}
                    <span class="car">${b.carLicense}</span>
                    <span class="user">${b.userName}</span>
                    ${b.destination ? '<span class="dest">→ ' + b.destination + '</span>' : ''}
                    <span style="color:var(--text-muted)">${b.usedTime || ''}</span>
                    <span class="sep">•</span>
                </span>
            `).join('');
        }

        // ─── Plant Selector ───
        function updatePlantSelector(plants) {
            const selector = document.getElementById('plantSelector');
            selector.innerHTML = plants.map(p =>
                `<button class="plant-btn ${p.id === currentPlant ? 'active' : ''}" onclick="switchPlant('${p.id}')">${p.name}</button>`
            ).join('');
        }

        function switchPlant(plantId) {
            if (plantId === currentPlant) return;
            currentPlant = plantId;

            // Update URL
            const url = new URL(window.location);
            url.searchParams.set('plant', plantId);
            history.replaceState(null, '', url);

            // Update active button
            document.querySelectorAll('.plant-btn').forEach(btn => {
                btn.classList.toggle('active', btn.textContent.trim() === (dashData?.availablePlants.find(p => p.id === plantId)?.name || plantId));
            });

            // Re-destroy charts for clean re-render
            if (weeklyChart) {
                weeklyChart.destroy();
                weeklyChart = null;
            }
            if (fleetOverviewChart) {
                fleetOverviewChart.destroy();
                fleetOverviewChart = null;
            }


            fetchDashboard();
        }

        // ─── Refresh Bar ───
        function startRefreshBar() {
            const bar = document.getElementById('refreshBar');
            bar.classList.remove('active');
            bar.style.width = '0';
            void bar.offsetWidth;
            bar.classList.add('active');
        }

        // ─── Fetch Dashboard Data ───
        async function fetchDashboard() {
            try {
                const dateParam = getEffectiveDate();
                const res = await fetch(`ajax_tv_dashboard_demo.php?plant=${currentPlant}&date=${dateParam}&_t=${Date.now()}`);
                const data = await res.json();

                if (!data.success) {
                    console.error('Dashboard data error:', data.error);
                    return;
                }

                dashData = data;

                // Update components
                setThaiDate(data.thaiDate);
                updateStats(data.stats);
                updateTopDeptChart(data.topDepartments || []);
                updateTopLocationChart(data.topLocations || []);
                updateMonthlyDeptChart(data.topDeptMonthly || []);
                updateMonthlyLocChart(data.topLocMonthly || []);

                updateFleetOverview(data.stats, data.carStatuses || []);

                updateCarGroupChart(data.carStatuses || []);
                updateCarBodyChart(data.carBodyChart);
                updateTicker(data.recentBookings);
                updatePlantSelector(data.availablePlants);
                updateLiveBadge(data.isToday);

                // Hide loading
                setTimeout(() => {
                    document.getElementById('loadingOverlay').classList.add('hidden');
                }, 800);

                // Start refresh bar
                startRefreshBar();

            } catch (err) {
                console.error('Dashboard fetch failed:', err);
            }
        }

        // ─── LIVE / Historical Badge ───
        function updateLiveBadge(isToday) {
            const badge = document.getElementById('liveBadge');
            const todayBtn = document.getElementById('dateTodayBtn');
            if (isToday) {
                badge.innerHTML = '<div class="live-dot"></div> LIVE';
                badge.classList.remove('historical');
                todayBtn.style.display = 'none';
            } else {
                badge.innerHTML = '<i class="bi bi-clock-history"></i> ย้อนหลัง';
                badge.classList.add('historical');
                todayBtn.style.display = 'inline-flex';
            }
        }

        // ─── Date Picker Logic ───
        function initDatePicker() {
            const picker = document.getElementById('datePicker');
            const prevBtn = document.getElementById('datePrev');
            const nextBtn = document.getElementById('dateNext');
            const todayBtn = document.getElementById('dateTodayBtn');

            // Set initial value to today
            picker.value = getTodayStr();

            // On date change
            picker.addEventListener('change', () => {
                const val = picker.value;
                if (!val) return;
                if (val === getTodayStr()) {
                    selectedDate = null;
                } else {
                    selectedDate = val;
                }
                onDateChanged();
            });

            // Prev day
            prevBtn.addEventListener('click', () => {
                const current = new Date(getEffectiveDate());
                current.setDate(current.getDate() - 1);
                const newDate = current.getFullYear() + '-' + String(current.getMonth() + 1).padStart(2, '0') + '-' + String(current.getDate()).padStart(2, '0');
                picker.value = newDate;
                selectedDate = (newDate === getTodayStr()) ? null : newDate;
                onDateChanged();
            });

            // Next day
            nextBtn.addEventListener('click', () => {
                const current = new Date(getEffectiveDate());
                current.setDate(current.getDate() + 1);
                const newDate = current.getFullYear() + '-' + String(current.getMonth() + 1).padStart(2, '0') + '-' + String(current.getDate()).padStart(2, '0');
                // Don't allow future dates
                if (newDate > getTodayStr()) return;
                picker.value = newDate;
                selectedDate = (newDate === getTodayStr()) ? null : newDate;
                onDateChanged();
            });

            // Today button
            todayBtn.addEventListener('click', () => {
                selectedDate = null;
                picker.value = getTodayStr();
                onDateChanged();
            });
        }

        function onDateChanged() {
            // Update URL
            const url = new URL(window.location);
            if (selectedDate) {
                url.searchParams.set('date', selectedDate);
            } else {
                url.searchParams.delete('date');
            }
            history.replaceState(null, '', url);

            // Re-destroy charts for clean re-render
            if (weeklyChart) {
                weeklyChart.destroy();
                weeklyChart = null;
            }


            // Reset auto-refresh (only auto-refresh when viewing today)
            setupAutoRefresh();

            fetchDashboard();
        }

        function setupAutoRefresh() {
            if (autoRefreshInterval) {
                clearInterval(autoRefreshInterval);
                autoRefreshInterval = null;
            }
            // Only auto-refresh when showing today's data
            if (!selectedDate) {
                autoRefreshInterval = setInterval(fetchDashboard, REFRESH_INTERVAL);
            }
        }

        // ─── Init ───
        (function init() {
            // Get plant from URL
            const params = new URLSearchParams(window.location.search);
            if (params.has('plant')) {
                currentPlant = params.get('plant');
            }
            if (params.has('date') && params.get('date') !== getTodayStr()) {
                selectedDate = params.get('date');
            }

            // Init date picker
            initDatePicker();
            if (selectedDate) {
                document.getElementById('datePicker').value = selectedDate;
            }

            // Initial load
            fetchDashboard();

            // Auto-refresh (only if viewing today)
            setupAutoRefresh();
        })();
    </script>
</body>

</html>