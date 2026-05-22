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
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>
    
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
                radial-gradient(at 0% 0%, rgba(59, 130, 246, 0.08) 0px, transparent 50%),
                radial-gradient(at 100% 0%, rgba(139, 92, 246, 0.08) 0px, transparent 50%),
                linear-gradient(180deg, rgba(5, 8, 17, 0.82) 0%, rgba(5, 8, 17, 0.75) 40%, rgba(5, 8, 17, 0.85) 100%),
                url('/images/bg1.png');
            background-size: cover;
            background-position: center center;
            background-repeat: no-repeat;
            background-attachment: fixed;
            color: var(--text-main);
            font-family: 'Outfit', 'Sarabun', sans-serif;
            overflow-x: hidden;
            min-height: 100vh;
            padding-bottom: 30px;
        }

        /* TV Mode: Specific for 1080p/4K TVs (Single Screen, No Scroll) */
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
                justify-content: space-between;
                padding-bottom: 8px;
                padding-top: 6px;
                overflow: hidden;
            }
            .premium-header { padding: 0.5rem 2rem !important; margin-bottom: 0 !important; height: 56px; min-height: 56px; }
            .mb-5, .mb-4 { margin-bottom: 0.4rem !important; }
            .g-4, .g-3 { --bs-gutter-x: 0.6rem; --bs-gutter-y: 0.4rem; }
            .stat-card { padding: 0.7rem 1rem !important; }
            .stat-value { font-size: 2rem !important; margin-bottom: 0.15rem !important; }
            .stat-label { font-size: 0.65rem !important; }
            .glass-card-header { padding: 0.4rem 1rem !important; }
            .glass-card-header h3 { font-size: 0.75rem !important; }
            .glass-card-body { padding: 0.15rem 0.5rem !important; }
            
            #fleetOverviewChart, #carGroupDonutChart, #carBodyDonutChart { height: 17vh !important; }
            #dailyDeptComboChart, #dailyLocComboChart, #monthlyDeptComboChart, #monthlyLocComboChart { height: 22vh !important; }
            
            .row { margin-left: -5px; margin-right: -5px; }
            .col-xl-2, .col-xl-4, .col-xl-6 { padding-left: 5px; padding-right: 5px; }
            
            /* Ensure glass cards don't add extra space */
            .glass-card { border-radius: 0.75rem; }
        }

        /* Standard Responsive for PC/Tablet/Mobile */
        @media (max-width: 1399px) {
            .premium-header { flex-wrap: wrap; gap: 1rem; height: auto; padding: 1rem; }
            .stat-value { font-size: 2rem; }
            .chart-container, [id*="Chart"] { height: 300px !important; }
        }

        @media (max-width: 768px) {
            .premium-header {
                padding: 0.75rem 1rem !important;
                flex-direction: column !important;
                gap: 0.75rem !important;
                height: auto !important;
                background: rgba(15, 23, 42, 0.95) !important;
                border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            }

            /* 1. Logo & Clock Row */
            .premium-header > div {
                width: 100% !important;
                display: flex !important;
                flex-direction: row !important;
                justify-content: space-between !important;
                align-items: center !important;
                margin: 0 !important;
                gap: 0.5rem !important;
            }

            /* Hide elements that clutter mobile */
            .premium-header .border-start, .premium-header .logo-text + div { display: none !important; }
            
            .logo-text { font-size: 1.1rem !important; letter-spacing: -0.01em; }
            .bi-car-front-fill { font-size: 1.2rem !important; }
            
            /* 2. Badge Section (Center row) */
            .premium-header > div:first-child {
                flex-direction: column !important;
                gap: 0.75rem !important;
            }
            /* Inner wrapper of logo/badge */
            .premium-header > div:first-child > div:first-child { 
                width: 100%;
                display: flex;
                justify-content: space-between;
                align-items: center;
            }
            .premium-header > div:first-child > div:last-child {
                width: 100%;
                display: flex;
                flex-direction: column;
                gap: 0.75rem;
            }

            .badge { 
                font-size: 0.75rem !important; 
                padding: 0.5rem 1rem !important;
                width: 100% !important;
                justify-content: center !important;
                background: rgba(34, 211, 238, 0.05) !important;
                border: 1px solid rgba(34, 211, 238, 0.2) !important;
            }

            /* 3. Plant Selector (Tab style) */
            #plantSelector {
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
                width: calc(100% + 2rem);
                margin: 0 -1rem !important;
                padding: 0.25rem 1rem !important;
                display: flex !important;
                gap: 0.4rem !important;
                flex-wrap: nowrap !important;
                justify-content: flex-start !important;
            }
            #plantSelector::-webkit-scrollbar { display: none; }
            
            .btn-premium {
                font-size: 0.8rem !important;
                padding: 0.5rem 1.25rem !important;
                border-radius: 12px !important;
                background: rgba(255, 255, 255, 0.03) !important;
                border: 1px solid rgba(255, 255, 255, 0.08) !important;
                white-space: nowrap;
                flex-shrink: 0;
            }
            .btn-premium.active {
                background: var(--accent-blue) !important;
                color: white !important;
                border-color: var(--accent-blue) !important;
                box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3) !important;
            }

            /* 4. Bottom Controls (Clock & Picker) */
            .premium-header > div:last-child {
                border-top: 1px solid rgba(255, 255, 255, 0.05);
                padding-top: 0.75rem !important;
            }
            
            #clockText { font-size: 0.85rem !important; font-weight: 600 !important; }
            #datePicker { 
                width: 130px !important; 
                font-size: 0.8rem !important;
                background: rgba(255,255,255,0.05) !important;
                border: 1px solid rgba(255,255,255,0.1) !important;
                border-radius: 8px !important;
            }
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

        .chart-container { height: 300px; width: 100%; position: relative; }

        /* Chart.js requires position:relative on parent container */
        #fleetOverviewChart, #carGroupDonutChart, #carBodyDonutChart,
        #dailyDeptComboChart, #dailyLocComboChart, #monthlyDeptComboChart, #monthlyLocComboChart {
            position: relative;
        }

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

        /* =========================================
           Notification Bell Styles
           ========================================= */
        .notification-bell {
            position: relative;
            cursor: pointer;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            transition: all 0.3s ease;
            color: var(--text-muted);
        }
        .notification-bell:hover {
            background: rgba(245, 158, 11, 0.15);
            border-color: rgba(245, 158, 11, 0.4);
            color: #f59e0b;
            transform: scale(1.08);
            box-shadow: 0 0 20px rgba(245, 158, 11, 0.15);
        }
        .notification-bell i {
            font-size: 1.15rem;
            transition: transform 0.3s ease;
        }
        .notification-bell:hover i {
            animation: bellRing 0.6s ease;
        }
        @keyframes bellRing {
            0% { transform: rotate(0); }
            15% { transform: rotate(14deg); }
            30% { transform: rotate(-14deg); }
            45% { transform: rotate(10deg); }
            60% { transform: rotate(-8deg); }
            75% { transform: rotate(4deg); }
            100% { transform: rotate(0); }
        }
        .notification-badge {
            position: absolute;
            top: -2px;
            right: -2px;
            min-width: 18px;
            height: 18px;
            background: linear-gradient(135deg, #ef4444, #dc2626);
            border-radius: 50px;
            color: white;
            font-size: 0.6rem;
            font-weight: 800;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0 4px;
            border: 2px solid var(--bg-deep);
            box-shadow: 0 0 8px rgba(239, 68, 68, 0.5);
            animation: badgePulse 2s infinite;
            line-height: 1;
        }
        .notification-badge.d-none { display: none !important; }
        @keyframes badgePulse {
            0%, 100% { box-shadow: 0 0 8px rgba(239, 68, 68, 0.5); }
            50% { box-shadow: 0 0 16px rgba(239, 68, 68, 0.8); }
        }

        /* Car Check Button */
        .car-check-btn {
            position: relative;
            cursor: pointer;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            transition: all 0.3s ease;
            color: var(--text-muted);
        }
        .car-check-btn:hover {
            background: rgba(34, 197, 94, 0.15);
            border-color: rgba(34, 197, 94, 0.4);
            color: #4ade80;
            transform: scale(1.08);
            box-shadow: 0 0 20px rgba(34, 197, 94, 0.15);
        }
        .car-check-btn i {
            font-size: 1.1rem;
            transition: transform 0.3s ease;
        }
        .car-check-badge {
            position: absolute;
            top: -2px;
            right: -2px;
            min-width: 18px;
            height: 18px;
            background: linear-gradient(135deg, #22c55e, #16a34a);
            border-radius: 50px;
            color: white;
            font-size: 0.6rem;
            font-weight: 800;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0 4px;
            border: 2px solid var(--bg-deep);
            line-height: 1;
        }
        .car-check-badge.d-none { display: none !important; }

        /* Available Cars Modal */
        #availableCarsModal .modal-content {
            background: linear-gradient(180deg, rgba(15, 23, 42, 0.98), rgba(10, 15, 30, 0.99)) !important;
        }
        .avail-car-card {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.06);
            border-radius: 0.75rem;
            padding: 0.6rem 0.85rem;
            margin-bottom: 0.4rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 0.75rem;
            transition: all 0.2s ease;
        }
        .avail-car-card:hover {
            background: rgba(255, 255, 255, 0.06);
            transform: translateX(3px);
        }
        .avail-car-left {
            display: flex;
            align-items: center;
            gap: 0.65rem;
            min-width: 0;
        }
        .avail-car-license {
            font-weight: 700;
            font-size: 0.85rem;
            color: #e2e8f0;
            white-space: nowrap;
        }
        .avail-car-detail {
            font-size: 0.68rem;
            color: #64748b;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .avail-status-tag {
            display: inline-flex;
            align-items: center;
            gap: 0.3rem;
            padding: 0.22rem 0.55rem;
            border-radius: 50px;
            font-size: 0.62rem;
            font-weight: 600;
            white-space: nowrap;
            flex-shrink: 0;
        }
        .avail-status-tag.available {
            background: rgba(34, 197, 94, 0.15);
            border: 1px solid rgba(34, 197, 94, 0.30);
            color: #86efac;
        }
        .avail-status-tag.in-use {
            background: rgba(220, 80, 80, 0.15);
            border: 1px solid rgba(220, 80, 80, 0.30);
            color: #f0a8a8;
        }
        .avail-status-tag.repair {
            background: rgba(220, 170, 60, 0.15);
            border: 1px solid rgba(220, 170, 60, 0.30);
            color: #e8d090;
        }
        .avail-status-tag.pending-close {
            background: rgba(96, 165, 250, 0.15);
            border: 1px solid rgba(96, 165, 250, 0.30);
            color: #93c5fd;
        }
        .avail-status-tag.booked {
            background: rgba(168, 85, 247, 0.15);
            border: 1px solid rgba(168, 85, 247, 0.30);
            color: #c4b5fd;
        }
        .avail-filter-tabs {
            display: flex;
            gap: 0.4rem;
            flex-wrap: wrap;
            margin-bottom: 0.75rem;
        }
        .avail-filter-tab {
            padding: 0.3rem 0.7rem;
            border-radius: 50px;
            font-size: 0.68rem;
            font-weight: 600;
            cursor: pointer;
            background: rgba(255, 255, 255, 0.04);
            border: 1px solid rgba(255, 255, 255, 0.08);
            color: #94a3b8;
            transition: all 0.2s;
        }
        .avail-filter-tab:hover {
            background: rgba(255, 255, 255, 0.08);
        }
        .avail-filter-tab.active {
            background: rgba(34, 211, 238, 0.15);
            border-color: rgba(34, 211, 238, 0.35);
            color: #67e8f9;
        }

        /* Available Cars Modal Responsive */
        @media (max-width: 991.98px) {
            #availableCarsModal .modal-dialog { max-width: 95vw; margin: 1.5rem auto; }
            #availableCarsModal .modal-content { animation: slideUp 0.35s ease-out; }
            #availableCarsModal .modal-header { padding: 0.85rem 1.25rem !important; position: sticky; top: 0; z-index: 10; backdrop-filter: blur(10px); }
            #availableCarsModal .modal-body { max-height: 70vh; overflow-y: auto; -webkit-overflow-scrolling: touch; }
        }
        @media (max-width: 575.98px) {
            #availableCarsModal .modal-dialog { max-width: 100%; width: 100%; margin: 0; min-height: 100vh; display: flex; align-items: flex-end; }
            #availableCarsModal .modal-content { border-radius: 1.25rem 1.25rem 0 0 !important; min-height: 92vh; max-height: 100vh; animation: slideUp 0.3s ease-out; }
            #availableCarsModal .modal-header { padding: 1rem 1rem 0.75rem !important; position: sticky; top: 0; z-index: 10; backdrop-filter: blur(12px); border-radius: 1.25rem 1.25rem 0 0; }
            #availableCarsModal .modal-title { font-size: 0.95rem; }
            #availableCarsModal .modal-body { max-height: calc(92vh - 56px); overflow-y: auto; -webkit-overflow-scrolling: touch; padding: 0.75rem !important; }
            .avail-car-card { padding: 0.5rem 0.7rem; }
            .avail-car-license { font-size: 0.78rem; }
            .avail-status-tag { font-size: 0.58rem; padding: 0.18rem 0.45rem; }
            .avail-filter-tab { font-size: 0.6rem; padding: 0.25rem 0.55rem; }
        }
        @media (max-width: 374px) {
            #availableCarsModal .modal-content { min-height: 95vh; }
            .avail-car-license { font-size: 0.72rem; }
            .avail-status-tag { font-size: 0.55rem; }
        }

        /* Notification Modal */
        #docAlertModal .modal-content {
            background: linear-gradient(180deg, rgba(15, 23, 42, 0.98), rgba(10, 15, 30, 0.99)) !important;
        }
        .doc-alert-card {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.06);
            border-radius: 1rem;
            padding: 1rem 1.25rem;
            margin-bottom: 0.75rem;
            transition: all 0.25s ease;
        }
        .doc-alert-card:hover {
            background: rgba(255, 255, 255, 0.06);
            border-color: rgba(34, 211, 238, 0.2);
            transform: translateX(4px);
        }
        .doc-alert-car-info {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin-bottom: 0.75rem;
        }
        .doc-alert-license {
            font-weight: 700;
            font-size: 0.95rem;
            color: #e2e8f0;
        }
        .doc-alert-meta {
            font-size: 0.72rem;
            color: var(--text-muted);
        }
        .doc-alert-items {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
        }
        .doc-alert-tag {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            padding: 0.3rem 0.65rem;
            border-radius: 50px;
            font-size: 0.7rem;
            font-weight: 600;
            letter-spacing: 0.02em;
        }
        .doc-alert-tag.expired {
            background: rgba(220, 80, 80, 0.18);
            border: 1px solid rgba(220, 80, 80, 0.35);
            color: #f0a8a8;
        }
        .doc-alert-tag.expiring_soon {
            background: rgba(220, 170, 60, 0.15);
            border: 1px solid rgba(220, 170, 60, 0.30);
            color: #e8d090;
        }
        .doc-alert-tag i {
            font-size: 0.6rem;
        }
        .doc-alert-summary {
            display: flex;
            gap: 1rem;
            padding: 0.75rem 1rem;
            background: rgba(255, 255, 255, 0.02);
            border-radius: 0.75rem;
            border: 1px solid rgba(255, 255, 255, 0.05);
            margin-bottom: 1rem;
        }
        .doc-alert-summary-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.8rem;
        }
        .doc-alert-summary-item .count {
            font-weight: 800;
            font-size: 1.1rem;
        }
        .doc-alert-summary-item.expired-summary .count { color: #e88a8a; }
        .doc-alert-summary-item.warning-summary .count { color: #dbb86a; }
        .doc-alert-summary-item.total-summary .count { color: #8dd0dc; }

        .doc-alert-empty {
            text-align: center;
            padding: 3rem 1rem;
            color: var(--text-muted);
        }
        .doc-alert-empty i {
            font-size: 3rem;
            opacity: 0.15;
            margin-bottom: 0.75rem;
        }

        /* Scrollbar in modal */
        #docAlertModal .modal-body::-webkit-scrollbar { width: 4px; }
        #docAlertModal .modal-body::-webkit-scrollbar-track { background: transparent; }
        #docAlertModal .modal-body::-webkit-scrollbar-thumb { background: rgba(34, 211, 238, 0.25); border-radius: 4px; }

        /* =========================================
           Doc Alert Modal — Responsive
           ========================================= */

        /* -- Tablet (576px – 991px) -- */
        @media (max-width: 991.98px) {
            #docAlertModal .modal-dialog {
                max-width: 95vw;
                margin: 1.5rem auto;
            }
            #docAlertModal .modal-content {
                animation: slideUp 0.35s ease-out;
            }
            #docAlertModal .modal-header {
                padding: 0.85rem 1.25rem !important;
                position: sticky;
                top: 0;
                z-index: 10;
                backdrop-filter: blur(10px);
            }
            #docAlertModal .modal-title { font-size: 1rem; }
            #docAlertModal .modal-body {
                max-height: 70vh;
                overflow-y: auto;
                -webkit-overflow-scrolling: touch;
            }
            .doc-alert-summary { flex-wrap: wrap; gap: 0.75rem; }
            .doc-alert-card { padding: 0.85rem 1rem; }
            .doc-alert-tag { font-size: 0.65rem; padding: 0.25rem 0.55rem; }
        }

        /* -- Mobile (≤ 575px) — Fullscreen sheet -- */
        @media (max-width: 575.98px) {
            #docAlertModal .modal-dialog {
                max-width: 100%;
                width: 100%;
                margin: 0;
                min-height: 100vh;
                display: flex;
                align-items: flex-end;
            }
            #docAlertModal .modal-content {
                border-radius: 1.25rem 1.25rem 0 0 !important;
                min-height: 92vh;
                max-height: 100vh;
                animation: slideUp 0.3s ease-out;
            }
            #docAlertModal .modal-header {
                padding: 1rem 1rem 0.75rem !important;
                position: sticky;
                top: 0;
                z-index: 10;
                backdrop-filter: blur(12px);
                border-radius: 1.25rem 1.25rem 0 0;
            }
            #docAlertModal .modal-title {
                font-size: 0.95rem;
            }
            #docAlertModal .modal-body {
                max-height: calc(92vh - 56px);
                overflow-y: auto;
                -webkit-overflow-scrolling: touch;
                padding: 0.75rem !important;
            }
            #docAlertModal .modal-body::-webkit-scrollbar { width: 3px; }
            #docAlertModal .modal-body::-webkit-scrollbar-thumb { background: rgba(34, 211, 238, 0.25); border-radius: 3px; }

            .doc-alert-summary {
                flex-direction: column;
                gap: 0.5rem;
                padding: 0.6rem 0.85rem;
            }
            .doc-alert-summary-item { font-size: 0.75rem; }
            .doc-alert-summary-item .count { font-size: 1rem; }

            .doc-alert-card {
                padding: 0.75rem 0.85rem;
                margin-bottom: 0.5rem;
                border-radius: 0.75rem;
            }
            .doc-alert-car-info { gap: 0.5rem; margin-bottom: 0.5rem; }
            .doc-alert-license { font-size: 0.85rem; }
            .doc-alert-meta { font-size: 0.65rem; }
            .doc-alert-items { gap: 0.35rem; }
            .doc-alert-tag {\n                font-size: 0.6rem;\n                padding: 0.2rem 0.5rem;\n                gap: 0.25rem;\n            }
            .doc-alert-tag i { font-size: 0.5rem; }
        }

        /* -- Very small screens (≤ 374px) -- */
        @media (max-width: 374px) {
            #docAlertModal .modal-content { min-height: 95vh; }
            .doc-alert-card { padding: 0.6rem 0.7rem; }
            .doc-alert-license { font-size: 0.8rem; }
            .doc-alert-tag { font-size: 0.55rem; padding: 0.18rem 0.4rem; }
            .doc-alert-summary-item .count { font-size: 0.9rem; }
        }

        /* =========================================
           Car Availability Status Badge (Doc Alert)
           ========================================= */
        .car-avail-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.3rem;
            padding: 0.2rem 0.6rem;
            border-radius: 50px;
            font-size: 0.65rem;
            font-weight: 700;
            letter-spacing: 0.02em;
            white-space: nowrap;
            flex-shrink: 0;
        }
        .car-avail-badge i { font-size: 0.55rem; }
        .car-avail-badge.ready {
            background: rgba(34, 197, 94, 0.12);
            border: 1px solid rgba(34, 197, 94, 0.30);
            color: #86efac;
        }
        .car-avail-badge.repair {
            background: rgba(245, 158, 11, 0.12);
            border: 1px solid rgba(245, 158, 11, 0.30);
            color: #fbbf24;
        }
        .car-avail-badge.not_ready {
            background: rgba(239, 68, 68, 0.12);
            border: 1px solid rgba(239, 68, 68, 0.30);
            color: #f87171;
        }
        .doc-alert-card-top {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 0.5rem;
        }
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

            <!-- Notification Bell -->
            <div class="notification-bell" id="notificationBell" onclick="showDocAlertModal()" title="แจ้งเตือนเอกสารรถ">
                <i class="bi bi-bell-fill"></i>
                <span class="notification-badge d-none" id="notificationBadge">0</span>
            </div>

            <!-- Car Availability Check -->
            <div class="car-check-btn" id="carCheckBtn" onclick="showAvailableCarsModal()" title="เช็ครถว่างวันนี้">
                <i class="bi bi-car-front-fill"></i>
                <span class="car-check-badge d-none" id="carCheckBadge">0</span>
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
        <div class="row g-3 mb-0">
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
                        <button type="button" class="btn d-flex align-items-center justify-content-center" data-bs-dismiss="modal" aria-label="ปิด" style="width: 36px; height: 36px; background: rgba(239, 68, 68, 0.2); border: 1.5px solid rgba(239, 68, 68, 0.5); border-radius: 50%; color: #f87171; font-size: 1.1rem; transition: all 0.2s;" onmouseover="this.style.background='rgba(239,68,68,0.4)';this.style.borderColor='#ef4444'" onmouseout="this.style.background='rgba(239,68,68,0.2)';this.style.borderColor='rgba(239,68,68,0.5)'">
                            <i class="bi bi-x-lg"></i>
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

    <!-- Document Alert Modal -->
    <div class="modal fade" id="docAlertModal" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content bg-dark border-secondary shadow-lg rounded-4 overflow-hidden">
                <div class="modal-header border-bottom border-white border-opacity-10 px-4 py-3" style="background: #0f172a;">
                    <h5 class="modal-title fw-bold d-flex align-items-center gap-2" style="color: #e2e8f0;">
                        <i class="bi bi-bell-fill" style="color: #8dd0dc;"></i>
                        <span>แจ้งเตือนต่อเอกสารรถ</span>
                    </h5>
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge rounded-pill d-none" id="docAlertCount" style="font-size: 0.7rem; background: rgba(100, 160, 180, 0.15); color: #8dd0dc; border: 1px solid rgba(100, 160, 180, 0.25);"></span>
                        <button type="button" class="btn d-flex align-items-center justify-content-center" data-bs-dismiss="modal" aria-label="ปิด" style="width: 36px; height: 36px; background: rgba(239, 68, 68, 0.2); border: 1.5px solid rgba(239, 68, 68, 0.5); border-radius: 50%; color: #f87171; font-size: 1.1rem; transition: all 0.2s;" onmouseover="this.style.background='rgba(239,68,68,0.4)';this.style.borderColor='#ef4444'" onmouseout="this.style.background='rgba(239,68,68,0.2)';this.style.borderColor='rgba(239,68,68,0.5)'">
                            <i class="bi bi-x-lg"></i>
                        </button>
                    </div>
                </div>
                <div class="modal-body p-3" style="max-height: 70vh; overflow-y: auto;">
                    <!-- Summary -->
                    <div class="doc-alert-summary" id="docAlertSummary"></div>
                    <!-- Alert Cards -->
                    <div id="docAlertBody"></div>
                    <!-- Empty State -->
                    <div class="doc-alert-empty" id="docAlertEmpty" style="display:none;">
                        <i class="bi bi-shield-check d-block"></i>
                        <p class="mb-0 mt-2">เอกสารรถทุกคันเป็นปกติ ✨</p>
                        <p class="small opacity-50">ไม่มีเอกสารที่ต้องต่ออายุในขณะนี้</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Available Cars Modal -->
    <div class="modal fade" id="availableCarsModal" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content bg-dark border-secondary shadow-lg rounded-4 overflow-hidden">
                <div class="modal-header border-bottom border-white border-opacity-10 px-4 py-3" style="background: #0f172a;">
                    <h5 class="modal-title fw-bold d-flex align-items-center gap-2" style="color: #e2e8f0;">
                        <i class="bi bi-car-front-fill" style="color: #86efac;"></i>
                        <span>เช็คสถานะรถวันนี้</span>
                    </h5>
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge rounded-pill d-none" id="availCarCount" style="font-size: 0.7rem; background: rgba(34, 197, 94, 0.15); color: #86efac; border: 1px solid rgba(34, 197, 94, 0.25);"></span>
                        <button type="button" class="btn d-flex align-items-center justify-content-center" data-bs-dismiss="modal" aria-label="ปิด" style="width: 36px; height: 36px; background: rgba(239, 68, 68, 0.2); border: 1.5px solid rgba(239, 68, 68, 0.5); border-radius: 50%; color: #f87171; font-size: 1.1rem; transition: all 0.2s;" onmouseover="this.style.background='rgba(239,68,68,0.4)';this.style.borderColor='#ef4444'" onmouseout="this.style.background='rgba(239,68,68,0.2)';this.style.borderColor='rgba(239,68,68,0.5)'">
                            <i class="bi bi-x-lg"></i>
                        </button>
                    </div>
                </div>
                <div class="modal-body p-3" style="max-height: 70vh; overflow-y: auto;">
                    <!-- Summary -->
                    <div class="doc-alert-summary" id="availCarSummary"></div>
                    <!-- Filter Tabs -->
                    <div class="avail-filter-tabs" id="availFilterTabs"></div>
                    <!-- Car List -->
                    <div id="availCarBody"></div>
                </div>
            </div>
        </div>
    </div>

    <script>
        const DATA_URL = "{{ route('tv.dashboard.data') }}";
        const DOC_ALERT_URL = "{{ route('tv.dashboard.doc-alerts') }}";
        const REFRESH_RATE = 3600000; // 1 hour
        
        let currentPlant = '1100';
        let dashData = null;
        let modal = null;
        let docAlertModal = null;
        let availableCarsModal = null;
        let docAlertData = null;

        document.addEventListener('DOMContentLoaded', () => {
            modal = new bootstrap.Modal(document.getElementById('cardModal'));
            docAlertModal = new bootstrap.Modal(document.getElementById('docAlertModal'));
            availableCarsModal = new bootstrap.Modal(document.getElementById('availableCarsModal'));
            document.getElementById('datePicker').value = new Date().toISOString().split('T')[0];
            
            document.getElementById('datePicker').addEventListener('change', fetchDashboard);
            
            updateClock();
            setInterval(updateClock, 1000);
            
            fetchDashboard();
            setInterval(fetchDashboard, REFRESH_RATE);

            // Fetch doc alerts initially
            fetchDocAlerts();

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
            updateCarCheckBadge(data.carStatuses);
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

        // Chart.js instance tracking (destroy before re-render to prevent memory leak)
        const chartInstances = {};

        function getOrCreateChart(canvasId, config) {
            if (chartInstances[canvasId]) {
                chartInstances[canvasId].destroy();
            }
            const ctx = document.getElementById(canvasId);
            if (!ctx) return null;
            // Chart.js needs a <canvas>, replace div with canvas if needed
            let canvas = ctx;
            if (ctx.tagName !== 'CANVAS') {
                canvas = ctx.querySelector('canvas');
                if (!canvas) {
                    canvas = document.createElement('canvas');
                    ctx.innerHTML = '';
                    ctx.appendChild(canvas);
                }
            }
            chartInstances[canvasId] = new Chart(canvas, config);
            return chartInstances[canvasId];
        }

        // Center text plugin for donut charts
        const centerTextPlugin = {
            id: 'centerText',
            afterDraw(chart) {
                if (!chart.config.options.plugins?.centerText) return;
                const { text, subText } = chart.config.options.plugins.centerText;
                const { ctx: c, chartArea: { left, right, top, bottom } } = chart;
                const cx = (left + right) / 2;
                const cy = (top + bottom) / 2;
                c.save();
                if (text) {
                    c.font = 'bold 1.2rem Outfit, Sarabun, sans-serif';
                    c.fillStyle = '#f8fafc';
                    c.textAlign = 'center';
                    c.textBaseline = 'middle';
                    c.fillText(text, cx, subText ? cy - 8 : cy);
                }
                if (subText) {
                    c.font = '0.6rem Outfit, Sarabun, sans-serif';
                    c.fillStyle = '#94a3b8';
                    c.textAlign = 'center';
                    c.textBaseline = 'middle';
                    c.fillText(subText, cx, cy + 12);
                }
                c.restore();
            }
        };
        Chart.register(centerTextPlugin);

        // Chart.js global defaults for dark theme
        Chart.defaults.color = '#94a3b8';
        Chart.defaults.font.family = "'Outfit', 'Sarabun', sans-serif";
        Chart.defaults.font.size = 10;
        Chart.defaults.plugins.legend.display = false;

        // Shared leader-line plugin for all donut charts
        const leaderLinePlugin = {
            id: 'leaderLines',
            afterDraw(chart) {
                const dataset = chart.data.datasets[0];
                if (!dataset || chart.config.type !== 'doughnut') return;
                const meta = chart.getDatasetMeta(0);
                const { ctx: c, chartArea: { left, right, top, bottom } } = chart;
                const cx = (left + right) / 2;
                const cy = (top + bottom) / 2;
                const total = dataset.data.reduce((a, b) => a + b, 0);
                if (total === 0) return;

                const outerRadius = meta.data[0]?.outerRadius || 0;
                const fontSize = chart.config.options?.plugins?.leaderLines?.fontSize || 9;
                const lineLen = chart.config.options?.plugins?.leaderLines?.lineLength || 18;
                const elbowLen = chart.config.options?.plugins?.leaderLines?.elbowLength || 14;

                c.save();

                // Collect label positions first for anti-collision
                const labels = [];
                dataset.data.forEach((val, i) => {
                    if (val === 0) return;
                    const arc = meta.data[i];
                    const midAngle = (arc.startAngle + arc.endAngle) / 2;
                    const pct = ((val / total) * 100).toFixed(1);
                    const name = chart.data.labels[i] || '';
                    const isRight = Math.cos(midAngle) >= 0;

                    // Point on outer edge of donut
                    const edgeX = cx + Math.cos(midAngle) * outerRadius;
                    const edgeY = cy + Math.sin(midAngle) * outerRadius;

                    // End of leader line
                    const midX = cx + Math.cos(midAngle) * (outerRadius + lineLen);
                    const midY = cy + Math.sin(midAngle) * (outerRadius + lineLen);

                    // Elbow end
                    const endX = midX + (isRight ? elbowLen : -elbowLen);
                    const endY = midY;

                    labels.push({ val, pct, name, isRight, edgeX, edgeY, midX, midY, endX, endY, color: dataset.backgroundColor[i] || '#94a3b8', i });
                });

                // Simple anti-collision: push labels apart if too close vertically
                const minGap = fontSize * 2.2;
                const sides = { left: [], right: [] };
                labels.forEach(l => sides[l.isRight ? 'right' : 'left'].push(l));

                ['left', 'right'].forEach(side => {
                    const arr = sides[side].sort((a, b) => a.endY - b.endY);
                    for (let j = 1; j < arr.length; j++) {
                        const diff = arr[j].endY - arr[j - 1].endY;
                        if (diff < minGap) {
                            const shift = (minGap - diff) / 2;
                            arr[j - 1].endY -= shift;
                            arr[j - 1].midY -= shift;
                            arr[j].endY += shift;
                            arr[j].midY += shift;
                        }
                    }
                });

                // Draw
                labels.forEach(l => {
                    // Leader line
                    c.beginPath();
                    c.moveTo(l.edgeX, l.edgeY);
                    c.lineTo(l.midX, l.midY);
                    c.lineTo(l.endX, l.endY);
                    c.strokeStyle = l.color + '99'; // 60% opacity
                    c.lineWidth = 1;
                    c.stroke();

                    // Small dot at edge
                    c.beginPath();
                    c.arc(l.edgeX, l.edgeY, 2, 0, Math.PI * 2);
                    c.fillStyle = l.color;
                    c.fill();

                    // Text
                    c.font = `${fontSize}px Outfit, Sarabun, sans-serif`;
                    c.textAlign = l.isRight ? 'left' : 'right';
                    c.textBaseline = 'middle';
                    const textX = l.endX + (l.isRight ? 4 : -4);

                    // Name
                    c.fillStyle = '#cbd5e1';
                    c.fillText(l.name, textX, l.endY - (fontSize * 0.55));

                    // Value + percentage
                    c.fillStyle = '#94a3b8';
                    c.fillText(`${l.val} (${l.pct}%)`, textX, l.endY + (fontSize * 0.55));
                });

                c.restore();
            }
        };
        Chart.register(leaderLinePlugin);

        function renderFleetOverviewDonut(stats) {
            document.getElementById('totalCarsBadge1').textContent = stats.totalCars;
            getOrCreateChart('fleetOverviewChart', {
                type: 'doughnut',
                data: {
                    labels: ['พร้อมใช้งาน', 'ไม่พร้อมใช้งาน'],
                    datasets: [{
                        data: [stats.carsAvailable, stats.carsNotReady],
                        backgroundColor: ['#10b981', '#ef4444'],
                        borderWidth: 0,
                        hoverOffset: 6
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '65%',
                    layout: { padding: { top: 20, bottom: 20, left: 50, right: 50 } },
                    plugins: {
                        centerText: { text: stats.totalCars.toString(), subText: 'คัน' },
                        leaderLines: { fontSize: 9, lineLength: 16, elbowLength: 12 },
                        tooltip: {
                            callbacks: {
                                label: (ctx) => {
                                    const total = ctx.dataset.data.reduce((a, b) => a + b, 0);
                                    const pct = total > 0 ? ((ctx.raw / total) * 100).toFixed(1) : 0;
                                    return `${ctx.label}: ${ctx.raw} (${pct}%)`;
                                }
                            }
                        },
                        datalabels: false
                    }
                }
            });
        }

        function renderCarGroupDonut(chartData) {
            const total = chartData.data.reduce((a, b) => a + b, 0);
            document.getElementById('totalCarsBadge2').textContent = total;
            const colors = ['#22d3ee', '#f59e0b', '#3b82f6', '#8b5cf6', '#ec4899'];
            getOrCreateChart('carGroupDonutChart', {
                type: 'doughnut',
                data: {
                    labels: chartData.labels,
                    datasets: [{
                        data: chartData.data,
                        backgroundColor: chartData.labels.map((_, i) => colors[i % colors.length]),
                        borderWidth: 0,
                        hoverOffset: 6
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '65%',
                    layout: { padding: { top: 20, bottom: 20, left: 50, right: 50 } },
                    plugins: {
                        centerText: { text: total.toString(), subText: 'คัน' },
                        leaderLines: { fontSize: 9, lineLength: 16, elbowLength: 12 },
                        tooltip: {
                            callbacks: {
                                label: (ctx) => {
                                    const t = ctx.dataset.data.reduce((a, b) => a + b, 0);
                                    const pct = t > 0 ? ((ctx.raw / t) * 100).toFixed(1) : 0;
                                    return `${ctx.label}: ${ctx.raw} (${pct}%)`;
                                }
                            }
                        }
                    }
                }
            });
        }

        function renderCarBodyDonut(chartData) {
            const total = chartData.data.reduce((a, b) => a + b, 0);
            document.getElementById('totalCarsBadge3').textContent = total;
            const colors = ['#3b82f6', '#8b5cf6', '#22d3ee', '#10b981', '#f59e0b', '#ef4444', '#ec4899', '#f97316'];
            getOrCreateChart('carBodyDonutChart', {
                type: 'doughnut',
                data: {
                    labels: chartData.labels,
                    datasets: [{
                        data: chartData.data,
                        backgroundColor: chartData.labels.map((_, i) => colors[i % colors.length]),
                        borderWidth: 0,
                        hoverOffset: 6
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '65%',
                    layout: { padding: { top: 20, bottom: 20, left: 50, right: 50 } },
                    plugins: {
                        centerText: { text: total.toString(), subText: 'คัน' },
                        leaderLines: { fontSize: 8, lineLength: 14, elbowLength: 10 },
                        tooltip: {
                            callbacks: {
                                label: (ctx) => {
                                    const t = ctx.dataset.data.reduce((a, b) => a + b, 0);
                                    const pct = t > 0 ? ((ctx.raw / t) * 100).toFixed(1) : 0;
                                    return `${ctx.label}: ${ctx.raw} (${pct}%)`;
                                }
                            }
                        }
                    }
                }
            });
        }

        function renderComboChart(id, data, color, unit) {
            if (!data || data.length === 0) return;
            const categories = data.map(i => i.name);
            const values = data.map(i => i.count);
            
            // Create gradient for bars
            const container = document.getElementById(id);
            let canvas = container;
            if (container.tagName !== 'CANVAS') {
                canvas = container.querySelector('canvas');
                if (!canvas) {
                    canvas = document.createElement('canvas');
                    container.innerHTML = '';
                    container.appendChild(canvas);
                }
            }
            const ctx2d = canvas.getContext('2d');
            const grad = ctx2d.createLinearGradient(0, 0, 0, canvas.height || 280);
            grad.addColorStop(0, color);
            grad.addColorStop(1, color + '1A'); // 10% opacity

            getOrCreateChart(id, {
                type: 'bar',
                data: {
                    labels: categories,
                    datasets: [
                        {
                            label: 'จำนวน',
                            type: 'bar',
                            data: values,
                            backgroundColor: grad,
                            borderRadius: 4,
                            borderSkipped: false,
                            barPercentage: 0.65,
                            order: 2
                        },
                        {
                            label: 'แนวโน้ม',
                            type: 'line',
                            data: values,
                            borderColor: '#f8fafc',
                            borderWidth: 2,
                            pointBackgroundColor: color,
                            pointBorderColor: '#f8fafc',
                            pointBorderWidth: 1,
                            pointRadius: 3,
                            pointHoverRadius: 5,
                            tension: 0.3,
                            fill: false,
                            order: 1
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: { intersect: false, mode: 'index' },
                    scales: {
                        x: {
                            grid: { display: false },
                            border: { display: false },
                            ticks: { color: '#64748b', font: { size: 9 }, maxRotation: 45, minRotation: 0 }
                        },
                        y: {
                            grid: { color: 'rgba(255,255,255,0.03)' },
                            border: { display: false },
                            ticks: { color: '#475569', font: { size: 9 } },
                            beginAtZero: true
                        }
                    },
                    plugins: {
                        tooltip: {
                            callbacks: {
                                label: (ctx) => `${ctx.dataset.label}: ${ctx.raw} ${unit}`
                            }
                        }
                    }
                },
                plugins: [{
                    // Data labels on top of bars
                    id: 'barLabels',
                    afterDatasetsDraw(chart) {
                        const meta = chart.getDatasetMeta(0); // bar dataset
                        if (!meta) return;
                        const c = chart.ctx;
                        c.save();
                        c.font = '9px Outfit, Sarabun, sans-serif';
                        c.fillStyle = '#94a3b8';
                        c.textAlign = 'center';
                        c.textBaseline = 'bottom';
                        meta.data.forEach((bar, i) => {
                            c.fillText(`${values[i]} ${unit}`, bar.x, bar.y - 4);
                        });
                        c.restore();
                    }
                }]
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
            fetchDocAlerts();
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

        // =========================================
        // Document Alert Notification System
        // =========================================
        async function fetchDocAlerts() {
            try {
                const res = await fetch(`${DOC_ALERT_URL}?plant=${currentPlant}&_t=${Date.now()}`);
                const data = await res.json();
                if (!data.success) return;

                docAlertData = data;
                const badge = document.getElementById('notificationBadge');
                if (data.total > 0) {
                    badge.textContent = data.total > 99 ? '99+' : data.total;
                    badge.classList.remove('d-none');
                } else {
                    badge.classList.add('d-none');
                }
            } catch (err) {
                console.error('Doc alert error:', err);
            }
        }

        function formatThaiDate(dateStr) {
            if (!dateStr) return '—';
            const thaiMonthsShort = ['', 'ม.ค.', 'ก.พ.', 'มี.ค.', 'เม.ย.', 'พ.ค.', 'มิ.ย.', 'ก.ค.', 'ส.ค.', 'ก.ย.', 'ต.ค.', 'พ.ย.', 'ธ.ค.'];
            const parts = dateStr.split('-');
            if (parts.length !== 3) return dateStr;
            const year = parseInt(parts[0]) + 543;
            const month = parseInt(parts[1]);
            const day = parseInt(parts[2]);
            return `${day} ${thaiMonthsShort[month]} ${year}`;
        }

        function getDaysUntil(dateStr) {
            if (!dateStr) return null;
            const today = new Date();
            today.setHours(0,0,0,0);
            const target = new Date(dateStr);
            target.setHours(0,0,0,0);
            return Math.ceil((target - today) / (1000 * 60 * 60 * 24));
        }

        function showDocAlertModal() {
            if (!docAlertData) { fetchDocAlerts(); return; }

            const alerts = docAlertData.alerts || [];
            const statusCounts = docAlertData.statusCounts || { ready: 0, repair: 0, not_ready: 0 };
            const countBadge = document.getElementById('docAlertCount');
            const summary = document.getElementById('docAlertSummary');
            const body = document.getElementById('docAlertBody');
            const empty = document.getElementById('docAlertEmpty');

            if (alerts.length === 0) {
                countBadge.classList.add('d-none');
                summary.style.display = 'none';
                body.innerHTML = '';
                empty.style.display = 'block';
                docAlertModal.show();
                return;
            }

            // Count expired vs expiring
            let expiredCount = 0, warningCount = 0;
            alerts.forEach(a => {
                a.docs.forEach(d => {
                    if (d.status === 'expired') expiredCount++;
                    else warningCount++;
                });
            });

            countBadge.textContent = alerts.length + ' คัน';
            countBadge.classList.remove('d-none');

            // Summary — documents + car status
            summary.style.display = 'flex';
            summary.innerHTML = `
                <div class="doc-alert-summary-item total-summary">
                    <i class="bi bi-car-front-fill" style="color: #8dd0dc;"></i>
                    <span class="count">${alerts.length}</span>
                    <span class="text-muted small">คัน</span>
                </div>
                <div class="doc-alert-summary-item expired-summary">
                    <i class="bi bi-exclamation-triangle-fill" style="color: #e88a8a;"></i>
                    <span class="count">${expiredCount}</span>
                    <span class="text-muted small">หมดอายุ</span>
                </div>
                <div class="doc-alert-summary-item warning-summary">
                    <i class="bi bi-clock-fill" style="color: #dbb86a;"></i>
                    <span class="count">${warningCount}</span>
                    <span class="text-muted small">ใกล้หมดอายุ</span>
                </div>
                <div style="width:1px; background: rgba(255,255,255,0.08); margin: 0 0.25rem;"></div>
                <div class="doc-alert-summary-item">
                    <i class="bi bi-check-circle-fill" style="color: #86efac;"></i>
                    <span class="count" style="color: #86efac;">${statusCounts.ready}</span>
                    <span class="text-muted small">พร้อม</span>
                </div>
                <div class="doc-alert-summary-item">
                    <i class="bi bi-wrench-adjustable-circle" style="color: #fbbf24;"></i>
                    <span class="count" style="color: #fbbf24;">${statusCounts.repair}</span>
                    <span class="text-muted small">ส่งซ่อม</span>
                </div>
                <div class="doc-alert-summary-item">
                    <i class="bi bi-x-octagon-fill" style="color: #f87171;"></i>
                    <span class="count" style="color: #f87171;">${statusCounts.not_ready}</span>
                    <span class="text-muted small">ไม่พร้อม</span>
                </div>
            `;

            // Car availability status mapping
            const carAvailMap = {
                'ready':     { text: 'พร้อมใช้งาน', icon: 'bi-check-circle-fill', cls: 'ready' },
                'repair':    { text: 'ส่งซ่อม', icon: 'bi-wrench-adjustable-circle', cls: 'repair' },
                'not_ready': { text: 'ไม่พร้อมใช้งาน', icon: 'bi-x-octagon-fill', cls: 'not_ready' },
            };

            // Build alert cards
            empty.style.display = 'none';
            body.innerHTML = alerts.map((a, idx) => {
                const docsHtml = a.docs.map(d => {
                    const daysLeft = getDaysUntil(d.endDate);
                    const daysText = d.status === 'expired' 
                        ? `หมดอายุ ${Math.abs(daysLeft)} วันแล้ว` 
                        : `เหลือ ${daysLeft} วัน`;
                    const icon = d.status === 'expired' ? 'bi-x-circle-fill' : 'bi-exclamation-circle-fill';
                    return `<span class="doc-alert-tag ${d.status}">
                        <i class="bi ${icon}"></i>
                        ${d.type} · ${formatThaiDate(d.endDate)} · ${daysText}
                    </span>`;
                }).join('');

                const carInfo = [a.brand, a.model].filter(x => x).join(' ');
                const carTypeInfo = [a.group, a.carType].filter(x => x).join(' · ');

                // Car availability badge
                const avail = carAvailMap[a.carAvailability] || carAvailMap['ready'];
                const availBadgeHtml = `<span class="car-avail-badge ${avail.cls}"><i class="bi ${avail.icon}"></i> ${avail.text}</span>`;

                return `<div class="doc-alert-card">
                    <div class="doc-alert-card-top">
                        <div class="doc-alert-car-info" style="margin-bottom:0;">
                            <span class="badge bg-info bg-opacity-10 border border-info border-opacity-20 text-info px-2 py-1" style="font-size: 0.75rem;">${idx + 1}</span>
                            <div>
                                <div class="doc-alert-license">${a.license}</div>
                                <div class="doc-alert-meta">${carInfo}${carTypeInfo ? ' · ' + carTypeInfo : ''}</div>
                            </div>
                        </div>
                        ${availBadgeHtml}
                    </div>
                    <div class="doc-alert-items">${docsHtml}</div>
                </div>`;
            }).join('');

            docAlertModal.show();
        }

        // =========================================
        // Available Cars Check System
        // =========================================
        let currentAvailFilter = 'all';

        function updateCarCheckBadge(carStatuses) {
            if (!carStatuses) return;
            const poolCars = carStatuses.filter(c => c.type === 'รถส่วนกลาง');
            const availCount = poolCars.filter(c => c.availability === 'available' && !c.hasBooking).length;
            const badge = document.getElementById('carCheckBadge');
            if (availCount > 0) {
                badge.textContent = availCount;
                badge.classList.remove('d-none');
            } else {
                badge.classList.add('d-none');
            }
        }

        const availStatusMap = {
            'available': { text: 'ว่าง', icon: 'bi-check-circle-fill', cls: 'available' },
            'in-use':    { text: 'กำลังใช้งาน', icon: 'bi-key-fill', cls: 'in-use' },
            'repair':    { text: 'ส่งซ่อม', icon: 'bi-wrench-adjustable', cls: 'repair' },
            'pending-close': { text: 'รอปิดงาน', icon: 'bi-hourglass-split', cls: 'pending-close' },
        };

        function showAvailableCarsModal() {
            if (!dashData || !dashData.carStatuses) return;

            const cars = dashData.carStatuses.filter(c => c.type === 'รถส่วนกลาง');
            const summary = document.getElementById('availCarSummary');
            const tabs = document.getElementById('availFilterTabs');
            const body = document.getElementById('availCarBody');
            const countBadge = document.getElementById('availCarCount');

            // Count by status
            const counts = { available: 0, 'in-use': 0, repair: 0, 'pending-close': 0, booked: 0 };
            cars.forEach(c => {
                counts[c.availability] = (counts[c.availability] || 0) + 1;
                if (c.availability === 'available' && c.hasBooking) counts.booked++;
            });
            const freeCount = counts.available - counts.booked;

            countBadge.textContent = `รถทั้งหมด ${cars.length} คัน`;
            countBadge.classList.remove('d-none');

            // Summary
            summary.innerHTML = `
                <div class="doc-alert-summary-item" style="flex:1">
                    <i class="bi bi-car-front-fill" style="color: #8dd0dc;"></i>
                    <span class="count" style="color: #8dd0dc;">${cars.length}</span>
                    <span class="text-muted small">ทั้งหมด</span>
                </div>
                <div class="doc-alert-summary-item" style="flex:1">
                    <i class="bi bi-check-circle-fill" style="color: #86efac;"></i>
                    <span class="count" style="color: #86efac;">${freeCount}</span>
                    <span class="text-muted small">ว่าง</span>
                </div>
                <div class="doc-alert-summary-item" style="flex:1">
                    <i class="bi bi-key-fill" style="color: #f0a8a8;"></i>
                    <span class="count" style="color: #f0a8a8;">${counts['in-use']}</span>
                    <span class="text-muted small">ใช้งาน</span>
                </div>
                <div class="doc-alert-summary-item" style="flex:1">
                    <i class="bi bi-wrench-adjustable" style="color: #e8d090;"></i>
                    <span class="count" style="color: #e8d090;">${counts.repair}</span>
                    <span class="text-muted small">ซ่อม</span>
                </div>
            `;

            // Filter tabs
            const filterDefs = [
                { key: 'all', label: `ทั้งหมด (${cars.length})` },
                { key: 'available', label: `ว่าง (${freeCount})` },
                { key: 'in-use', label: `ใช้งาน (${counts['in-use']})` },
                { key: 'repair', label: `ส่งซ่อม (${counts.repair})` },
                { key: 'pending-close', label: `รอปิดงาน (${counts['pending-close']})` },
            ];
            tabs.innerHTML = filterDefs.map(f =>
                `<span class="avail-filter-tab ${currentAvailFilter === f.key ? 'active' : ''}" onclick="filterAvailCars('${f.key}')">${f.label}</span>`
            ).join('');

            // Render car list
            renderAvailCarList(cars);
            availableCarsModal.show();
        }

        function filterAvailCars(filterKey) {
            currentAvailFilter = filterKey;
            if (!dashData || !dashData.carStatuses) return;
            // Update tab styles
            document.querySelectorAll('.avail-filter-tab').forEach(t => t.classList.remove('active'));
            event.target.classList.add('active');
            renderAvailCarList(dashData.carStatuses.filter(c => c.type === '\u0e23\u0e16\u0e2a\u0e48\u0e27\u0e19\u0e01\u0e25\u0e32\u0e07'));
        }

        function renderAvailCarList(cars) {
            const body = document.getElementById('availCarBody');
            let filtered = cars;
            if (currentAvailFilter === 'available') {
                filtered = cars.filter(c => c.availability === 'available' && !c.hasBooking);
            } else if (currentAvailFilter !== 'all') {
                filtered = cars.filter(c => c.availability === currentAvailFilter);
            }

            if (filtered.length === 0) {
                body.innerHTML = `<div class="text-center py-4 text-muted"><i class="bi bi-inbox d-block" style="font-size:2rem; opacity:0.15;"></i><p class="mt-2 small">ไม่พบรถในสถานะนี้</p></div>`;
                return;
            }

            body.innerHTML = filtered.map((c, idx) => {
                const st = availStatusMap[c.availability] || availStatusMap['available'];
                let statusCls = st.cls;
                let statusText = st.text;
                let statusIcon = st.icon;

                // Special: available but has booking today
                if (c.availability === 'available' && c.hasBooking) {
                    statusCls = 'booked';
                    statusText = 'มีการจอง';
                    statusIcon = 'bi-calendar-check';
                }

                const detail = [c.brand, c.model].filter(x => x).join(' ');
                const typeInfo = [c.type, c.carType].filter(x => x).join(' · ');

                return `<div class="avail-car-card">
                    <div class="avail-car-left">
                        <span class="badge" style="font-size:0.65rem; min-width:24px; background: rgba(255,255,255,0.06); color: #64748b;">${idx + 1}</span>
                        <div>
                            <div class="avail-car-license">${c.license}</div>
                            <div class="avail-car-detail">${detail}${typeInfo ? ' · ' + typeInfo : ''}</div>
                        </div>
                    </div>
                    <span class="avail-status-tag ${statusCls}"><i class="bi ${statusIcon}"></i> ${statusText}</span>
                </div>`;
            }).join('');
        }
    </script>
</body>
</html>
