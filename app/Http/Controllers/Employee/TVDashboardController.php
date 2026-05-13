<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class TVDashboardController extends Controller
{
    public function index()
    {
        return view('employee.tv_dashboard');
    }

    public function getData(Request $request)
    {
        try {
            $db = DB::connection('oracle_intra');

            $actualToday = Carbon::now()->format('Y-m-d');
            $today = $request->query('date', $actualToday);
            if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $today)) {
                $today = $actualToday;
            }
            $isToday = ($today === $actualToday);
            $filterPlant = trim($request->query('plant', '1100'));

            // Plant name mapping
            $plantNames = [
                '1100' => 'SAB', '1101' => 'SAAB', '1200' => 'SLAB', '1201' => 'SRAB',
                '1202' => 'SLAB1', '1203' => 'SLAB2', '1300' => 'SRDC', '1400' => 'SATC', '1800' => 'SAM'
            ];
            $plantName = $plantNames[$filterPlant] ?? $filterPlant;

            // 1. TODAY'S STATS
            $sqlStats = "SELECT t1.APP_ID, t1.KEY_STATUS, t3.TOTAL_MILE_GO, t3.MILE_OUT,
                    t4.CAR_GROUP AS CAR_TYPE
                FROM TRC_REQUEST_RECORD_NEW t1
                LEFT JOIN TRC_CAR_APPROVE_NEW t3 ON t3.DOC_ID = t1.DOC_ID
                LEFT JOIN (
                    SELECT CAR_LICENSE, CAR_GROUP,
                           ROW_NUMBER() OVER (PARTITION BY CAR_LICENSE ORDER BY CAR_ID DESC) AS RN
                    FROM TRC_CAR_NEW
                ) t4 ON t4.CAR_LICENSE = t1.CAR_LICENSE AND t4.RN = 1
                WHERE t1.APP_ID IN ('AP02', 'AP04')
                AND TRIM(t1.USED_DATE) = ? 
                AND TRIM(t1.PLANT) = ?";

            $statsRows = $db->select($sqlStats, [$today, $filterPlant]);

            $totalBookings = count($statsRows);
            $pendingCount = 0; $issuedCount = 0; $returnedCount = 0; $closedCount = 0; $privateCount = 0; $totalDistance = 0;

            foreach ($statsRows as $row) {
                $row = array_change_key_case((array)$row, CASE_UPPER);
                if (empty($row['CAR_TYPE'])) { $privateCount++; }
                elseif ($row['APP_ID'] === 'AP04') { $closedCount++; }
                elseif ($row['KEY_STATUS'] === 'R') { $returnedCount++; }
                elseif ($row['KEY_STATUS'] === 'Y' && !empty($row['MILE_OUT'])) { $issuedCount++; }
                else { $pendingCount++; }
                
                if (!empty($row['TOTAL_MILE_GO'])) {
                    $m = intval($row['TOTAL_MILE_GO']);
                    if ($m > 0) $totalDistance += $m;
                }
            }

            // 2. WEEKLY CHART DATA
            $weekEnd = $today;
            $weekStart = Carbon::parse($today)->subDays(6)->format('Y-m-d');
            $sqlWeek = "SELECT TRIM(t1.USED_DATE) AS USED_DATE, COUNT(*) AS CNT
                FROM TRC_REQUEST_RECORD_NEW t1
                WHERE t1.APP_ID IN ('AP02', 'AP04')
                AND TRIM(t1.USED_DATE) BETWEEN ? AND ?
                AND TRIM(t1.PLANT) = ?
                GROUP BY TRIM(t1.USED_DATE)
                ORDER BY USED_DATE";

            $weekRows = $db->select($sqlWeek, [$weekStart, $weekEnd, $filterPlant]);
            $weekDataMap = [];
            foreach ($weekRows as $row) { 
                $rowArr = array_change_key_case((array)$row, CASE_UPPER);
                $weekDataMap[$rowArr['USED_DATE']] = intval($rowArr['CNT']); 
            }

            $thaiDaysShort = ['อา', 'จ', 'อ', 'พ', 'พฤ', 'ศ', 'ส'];
            $weekLabels = []; $weekData = [];
            $currentDate = Carbon::parse($weekStart);
            $end = Carbon::parse($weekEnd);
            while ($currentDate <= $end) {
                $dateStr = $currentDate->format('Y-m-d');
                $weekLabels[] = $thaiDaysShort[$currentDate->dayOfWeek] . ' ' . $currentDate->day;
                $weekData[] = $weekDataMap[$dateStr] ?? 0;
                $currentDate->addDay();
            }

            // 3. MONTHLY CHART DATA
            $monthStart = Carbon::parse($today)->startOfMonth()->format('Y-m-d');
            $monthEnd = Carbon::parse($today)->endOfMonth()->format('Y-m-d');
            $sqlMonth = "SELECT SUBSTR(TRIM(t1.USED_DATE), 9, 2) AS DAY_NUM, COUNT(*) AS CNT
                FROM TRC_REQUEST_RECORD_NEW t1
                WHERE t1.APP_ID IN ('AP02', 'AP04')
                AND TRIM(t1.USED_DATE) BETWEEN ? AND ?
                AND TRIM(t1.PLANT) = ?
                GROUP BY SUBSTR(TRIM(t1.USED_DATE), 9, 2)
                ORDER BY DAY_NUM";
            $monthRows = $db->select($sqlMonth, [$monthStart, $monthEnd, $filterPlant]);
            $monthDataMap = [];
            foreach ($monthRows as $row) { 
                $rowArr = array_change_key_case((array)$row, CASE_UPPER);
                $monthDataMap[intval($rowArr['DAY_NUM'])] = intval($rowArr['CNT']); 
            }

            $daysInMonth = Carbon::parse($today)->daysInMonth;
            $monthLabels = []; $monthData = [];
            for ($d = 1; $d <= $daysInMonth; $d++) {
                $monthLabels[] = $d; $monthData[] = $monthDataMap[$d] ?? 0;
            }

            // 4. RECENT BOOKINGS
            $sqlRecent = "SELECT * FROM (
                SELECT t1.DOC_ID, t1.CAR_LICENSE, t1.USER_ID, t1.USED_DATE, t1.USED_TIME, t1.KEY_STATUS, t1.APP_ID, t1.BACK_DATE, t1.BACK_TIME,
                    t1.REQUEST_GOTO, t1.REQUEST_PROVINCE,
                    t2.USERS_FNAMETH || ' ' || t2.USERS_LNAMETH AS USER_NAME,
                    t3.MILE_OUT, t3.TOTAL_MILE_GO,
                    t4.CAR_GROUP AS CAR_TYPE
                FROM TRC_REQUEST_RECORD_NEW t1
                LEFT JOIN WEB.USERS t2 ON t2.USERS_EMPCODE = t1.USER_ID
                LEFT JOIN TRC_CAR_APPROVE_NEW t3 ON t3.DOC_ID = t1.DOC_ID
                LEFT JOIN (
                    SELECT CAR_LICENSE, CAR_GROUP,
                           ROW_NUMBER() OVER (PARTITION BY CAR_LICENSE ORDER BY CAR_ID DESC) AS RN
                    FROM TRC_CAR_NEW
                ) t4 ON t4.CAR_LICENSE = t1.CAR_LICENSE AND t4.RN = 1
                WHERE t1.APP_ID IN ('AP02', 'AP04')
                AND TRIM(t1.USED_DATE) = ?
                AND TRIM(t1.PLANT) = ?
                ORDER BY t1.USED_TIME ASC
            ) WHERE ROWNUM <= 50";
            $recentRows = $db->select($sqlRecent, [$today, $filterPlant]);
            $recentBookings = [];
            foreach ($recentRows as $row) {
                $row = array_change_key_case((array)$row, CASE_UPPER);
                $ks = $row['KEY_STATUS'] ?? ''; $ap = $row['APP_ID'] ?? ''; $carType = $row['CAR_TYPE'] ?? '';
                if (empty($carType)) $status = 'private';
                elseif ($ap === 'AP04') $status = 'closed';
                elseif ($ks === 'R') $status = 'returned';
                elseif ($ks === 'Y' && !empty($row['MILE_OUT'])) $status = 'issued';
                else $status = 'pending';

                $recentBookings[] = [
                    'docId' => $row['DOC_ID'],
                    'carLicense' => $this->toUtf8($row['CAR_LICENSE'] ?? ''),
                    'userName' => $this->toUtf8($row['USER_NAME'] ?? ''),
                    'usedTime' => $row['USED_TIME'] ?? '',
                    'backTime' => $row['BACK_TIME'] ?? '',
                    'destination' => $this->toUtf8($row['REQUEST_GOTO'] ?? ''),
                    'province' => $this->toUtf8($row['REQUEST_PROVINCE'] ?? ''),
                    'status' => $status,
                    'totalMile' => !empty($row['TOTAL_MILE_GO']) ? intval($row['TOTAL_MILE_GO']) : 0,
                ];
            }

            // 5. CAR STATUS SUMMARY
            $sqlCars = "SELECT t1.CAR_LICENSE, t1.CAR_GROUP, t1.CAR_STATUS, t1.CAR_BAND, t1.CAR_MODEL, t1.CAR_TYPE,
                (SELECT COUNT(*) FROM TRC_REQUEST_RECORD_NEW r WHERE TRIM(r.CAR_LICENSE) = TRIM(t1.CAR_LICENSE) AND r.APP_ID = 'AP02' AND r.KEY_STATUS = 'Y' AND TRIM(r.USED_DATE) = ?) AS KEY_ISSUED,
                (SELECT COUNT(*) FROM TRC_REQUEST_RECORD_NEW r2 WHERE TRIM(r2.CAR_LICENSE) = TRIM(t1.CAR_LICENSE) AND r2.APP_ID = 'AP02' AND r2.KEY_STATUS = 'R' AND TRIM(r2.USED_DATE) = ?) AS KEY_RETURNED,
                (SELECT COUNT(*) FROM TRC_REQUEST_RECORD_NEW r3 WHERE TRIM(r3.CAR_LICENSE) = TRIM(t1.CAR_LICENSE) AND r3.APP_ID IN ('AP02','AP04') AND TRIM(r3.USED_DATE) = ?) AS HAS_BOOKING
                FROM TRC_CAR_NEW t1
                WHERE TRIM(t1.CAR_PLANT) = ?
                AND TRIM(t1.CAR_GROUP) != ?
                ORDER BY t1.CAR_LICENSE";

            $placeholder = $this->toTis620('กรุณาเลือก');
            $carRows = $db->select($sqlCars, [$today, $today, $today, $filterPlant, $placeholder]);

            $carStatuses = [];
            $totalCars = count($carRows);
            $carsInUse = 0; $carsAvailable = 0; $carsMaintenance = 0; $carsPendingClose = 0;
            $kwMaint = $this->toTis620('ส่งซ่อม');
            $kwRepairWord = $this->toTis620('ซ่อม');
            $kwNotReadyWord = $this->toTis620('ไม่พร้อม');

            foreach ($carRows as $row) {
                $row = array_change_key_case((array)$row, CASE_UPPER);
                $carStatusRaw = trim($row['CAR_STATUS'] ?? '');
                $keyIssued = intval($row['KEY_ISSUED']) > 0;
                $keyReturned = intval($row['KEY_RETURNED']) > 0;
                $hasBooking = intval($row['HAS_BOOKING']) > 0;
                $isRepair = (strpos($carStatusRaw, $kwMaint) !== false || strpos($carStatusRaw, $kwRepairWord) !== false || strpos($carStatusRaw, $kwNotReadyWord) !== false);

                if ($isRepair) { $availability = 'repair'; $carsMaintenance++; }
                elseif ($keyIssued && !$keyReturned) { $availability = 'in-use'; $carsInUse++; }
                elseif ($keyReturned) { $availability = 'pending-close'; $carsPendingClose++; }
                else { $availability = 'available'; $carsAvailable++; }

                $carStatuses[] = [
                    'license' => $this->toUtf8($row['CAR_LICENSE'] ?? ''),
                    'type' => $this->toUtf8($row['CAR_GROUP'] ?? ''),
                    'availability' => $availability,
                    'hasBooking' => $hasBooking,
                ];
            }

            // Group summary for donut charts
            $groupCounts = [];
            foreach ($carStatuses as $c) {
                $g = $c['type'] ?: 'ไม่ระบุ';
                $groupCounts[$g] = ($groupCounts[$g] ?? 0) + 1;
            }
            $carGroupChart = [
                'labels' => array_keys($groupCounts),
                'data' => array_values($groupCounts)
            ];

            // 6. CAR BODY TYPE COUNT
            $sqlCarBody = "SELECT CAR_TYPE, COUNT(*) AS CNT
                FROM TRC_CAR_NEW
                WHERE TRIM(CAR_PLANT) = ?
                AND TRIM(CAR_GROUP) != ?
                AND CAR_TYPE IS NOT NULL
                GROUP BY CAR_TYPE
                ORDER BY CNT DESC";
            $bodyRows = $db->select($sqlCarBody, [$filterPlant, $placeholder]);
            $carBodyLabels = []; $carBodyCounts = [];
            foreach ($bodyRows as $row) {
                $rowArr = array_change_key_case((array)$row, CASE_UPPER);
                $label = $this->toUtf8($rowArr['CAR_TYPE'] ?? '');
                if (!empty(trim($label))) { $carBodyLabels[] = $label; $carBodyCounts[] = intval($rowArr['CNT']); }
            }

            // 7. TOP DEPARTMENTS (Daily)
            $sqlDept = "SELECT t2.USERS_DEPARTMENT, COUNT(*) AS CNT
                FROM TRC_REQUEST_RECORD_NEW t1
                LEFT JOIN WEB.USERS t2 ON t2.USERS_EMPCODE = t1.USER_ID
                WHERE t1.APP_ID IN ('AP02', 'AP04')
                AND TRIM(t1.USED_DATE) = ?
                AND TRIM(t1.PLANT) = ?
                AND t2.USERS_DEPARTMENT IS NOT NULL
                GROUP BY t2.USERS_DEPARTMENT
                ORDER BY CNT DESC";
            $deptRows = $db->select($sqlDept, [$today, $filterPlant]);
            $topDepartments = [];
            foreach (array_slice($deptRows, 0, 10) as $row) {
                $rowArr = array_change_key_case((array)$row, CASE_UPPER);
                $topDepartments[] = ['name' => $this->toUtf8($rowArr['USERS_DEPARTMENT'] ?? ''), 'count' => intval($rowArr['CNT'])];
            }

            // 8. TOP LOCATIONS (Daily)
            $sqlLoc = "SELECT t1.REQUEST_GOTO, COUNT(*) AS CNT
                FROM TRC_REQUEST_RECORD_NEW t1
                WHERE t1.APP_ID IN ('AP02', 'AP04')
                AND TRIM(t1.USED_DATE) = ?
                AND TRIM(t1.PLANT) = ?
                AND t1.REQUEST_GOTO IS NOT NULL
                GROUP BY t1.REQUEST_GOTO
                ORDER BY CNT DESC";
            $locRows = $db->select($sqlLoc, [$today, $filterPlant]);
            $topLocations = [];
            foreach (array_slice($locRows, 0, 10) as $row) {
                $rowArr = array_change_key_case((array)$row, CASE_UPPER);
                $locName = $this->toUtf8($rowArr['REQUEST_GOTO'] ?? '');
                if (trim($locName) !== '') $topLocations[] = ['name' => $locName, 'count' => intval($rowArr['CNT'])];
            }

            // 9. MONTHLY STATS (DEPT & LOC)
            $sqlDeptMonth = "SELECT t2.USERS_DEPARTMENT, COUNT(*) AS CNT
                FROM TRC_REQUEST_RECORD_NEW t1
                LEFT JOIN WEB.USERS t2 ON t2.USERS_EMPCODE = t1.USER_ID
                WHERE t1.APP_ID IN ('AP02', 'AP04')
                AND TRIM(t1.USED_DATE) BETWEEN ? AND ?
                AND TRIM(t1.PLANT) = ?
                AND t2.USERS_DEPARTMENT IS NOT NULL
                GROUP BY t2.USERS_DEPARTMENT
                ORDER BY CNT DESC";
            $deptMonthRows = $db->select($sqlDeptMonth, [$monthStart, $monthEnd, $filterPlant]);
            $topDeptMonthly = [];
            foreach (array_slice($deptMonthRows, 0, 10) as $row) {
                $rowArr = array_change_key_case((array)$row, CASE_UPPER);
                $topDeptMonthly[] = ['name' => $this->toUtf8($rowArr['USERS_DEPARTMENT'] ?? ''), 'count' => intval($rowArr['CNT'])];
            }

            $sqlLocMonth = "SELECT REQUEST_GOTO AS LOC_NAME, COUNT(*) AS CNT
                FROM TRC_REQUEST_RECORD_NEW
                WHERE APP_ID IN ('AP02', 'AP04')
                AND TRIM(USED_DATE) BETWEEN ? AND ?
                AND TRIM(PLANT) = ?
                AND REQUEST_GOTO IS NOT NULL
                GROUP BY REQUEST_GOTO
                ORDER BY CNT DESC";
            $locMonthRows = $db->select($sqlLocMonth, [$monthStart, $monthEnd, $filterPlant]);
            $topLocMonthly = [];
            foreach (array_slice($locMonthRows, 0, 10) as $row) {
                $rowArr = array_change_key_case((array)$row, CASE_UPPER);
                $locName = $this->toUtf8($rowArr['LOC_NAME'] ?? '');
                if (trim($locName) !== '') $topLocMonthly[] = ['name' => $locName, 'count' => intval($rowArr['CNT'])];
            }

            // AVAILABLE PLANTS
            $sqlAvailablePlants = "SELECT DISTINCT PLANT FROM TRC_REQUEST_RECORD_NEW WHERE APP_ID IN ('AP02','AP04') AND PLANT IS NOT NULL ORDER BY PLANT";
            $plantRows = $db->select($sqlAvailablePlants);
            $availablePlants = [];
            foreach ($plantRows as $r) {
                $rArr = array_change_key_case((array)$r, CASE_UPPER);
                $pid = trim($rArr['PLANT']);
                $availablePlants[] = ['id' => $pid, 'name' => $plantNames[$pid] ?? $pid];
            }

            $thaiMonths = ['', 'มกราคม', 'กุมภาพันธ์', 'มีนาคม', 'เมษายน', 'พฤษภาคม', 'มิถุนายน', 'กรกฎาคม', 'สิงหาคม', 'กันยายน', 'ตุลาคม', 'พฤศจิกายน', 'ธันวาคม'];
            $thaiDaysFull = ['อาทิตย์', 'จันทร์', 'อังคาร', 'พุธ', 'พฤหัสบดี', 'ศุกร์', 'เสาร์'];
            $dt = Carbon::parse($today);
            $thaiDate = "วัน{$thaiDaysFull[$dt->dayOfWeek]}ที่ " . $dt->day . " {$thaiMonths[$dt->month]} " . ($dt->year + 543);

            return response()->json([
                'success' => true,
                'thaiDate' => $thaiDate,
                'plantName' => $plantName,
                'plant' => $filterPlant,
                'stats' => [
                    'totalBookings' => $totalBookings,
                    'pendingCount' => $pendingCount,
                    'issuedCount' => $issuedCount,
                    'returnedCount' => $returnedCount + $closedCount, 
                    'returnedCount_raw' => $returnedCount,
                    'closedCount' => $closedCount,
                    'privateCount' => $privateCount,
                    'totalDistance' => $totalDistance,
                    'totalCars' => $totalCars,
                    'carsInUse' => $carsInUse,
                    'carsAvailable' => $carsAvailable,
                    'carsMaintenance' => $carsMaintenance,
                    'carsNotReady' => $totalCars - $carsAvailable,
                ],
                'weekChart' => ['labels' => $weekLabels, 'data' => $weekData],
                'monthChart' => ['labels' => $monthLabels, 'data' => $monthData],
                'recentBookings' => $recentBookings,
                'carStatuses' => $carStatuses,
                'topDepartments' => $topDepartments,
                'topLocations' => $topLocations,
                'topDeptMonthly' => $topDeptMonthly,
                'topLocMonthly' => $topLocMonthly,
                'carBodyChart' => ['labels' => $carBodyLabels, 'data' => $carBodyCounts],
                'carGroupChart' => $carGroupChart,
                'availablePlants' => $availablePlants,
            ], 200, [], JSON_UNESCAPED_UNICODE);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    private function toUtf8($str)
    {
        if (empty($str)) return '';
        return @iconv('TIS-620', 'UTF-8', $str);
    }

    private function toTis620($str)
    {
        if (empty($str)) return '';
        return @iconv('UTF-8', 'TIS-620', $str);
    }
}
