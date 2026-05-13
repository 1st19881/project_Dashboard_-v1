<?php

/**
 * AJAX endpoint for TV Dashboard
 * Returns all dashboard data in a single JSON response
 * No authentication required - designed for public TV display
 */

header('Content-Type: application/json; charset=UTF-8');
header('Access-Control-Allow-Origin: *');

include("connectdatabase.php");
$conn = oci_connect("web", "web123", "SAGDB", "WE8DEC");

if (!$conn) {
    echo json_encode(['success' => false, 'error' => 'Database connection failed']);
    exit;
}

// Support historical date viewing via ?date=YYYY-MM-DD
$actualToday = date('Y-m-d');
if (isset($_GET['date']) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $_GET['date'])) {
    $today = $_GET['date'];
} else {
    $today = $actualToday;
}
$isToday = ($today === $actualToday);
$filterPlant = isset($_GET['plant']) ? trim($_GET['plant']) : '1100';

// Plant name mapping
$plantNames = [
    '1100' => 'SAB',
    '1101' => 'SAAB',
    '1200' => 'SLAB',
    '1201' => 'SRAB',
    '1202' => 'SLAB1',
    '1203' => 'SLAB2',
    '1300' => 'SRDC',
    '1400' => 'SATC',
    '1800' => 'SAM'
];

$plantName = isset($plantNames[$filterPlant]) ? $plantNames[$filterPlant] : $filterPlant;

// ??? 1. TODAY'S STATS ???
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
    AND t1.USED_DATE = :today AND t1.PLANT = :plant";

$rsStats = oci_parse($conn, $sqlStats);
oci_bind_by_name($rsStats, ':today', $today);
oci_bind_by_name($rsStats, ':plant', $filterPlant);
oci_execute($rsStats);

$totalBookings = 0;
$pendingCount = 0;
$issuedCount = 0;
$returnedCount = 0;
$closedCount = 0;
$privateCount = 0;
$totalDistance = 0;

while ($row = oci_fetch_array($rsStats, OCI_ASSOC + OCI_RETURN_NULLS)) {
    $totalBookings++;
    if (empty($row['CAR_TYPE'])) {
        $privateCount++;
    } elseif ($row['APP_ID'] === 'AP04') {
        $closedCount++;
    } elseif ($row['KEY_STATUS'] === 'R') {
        $returnedCount++;
    } elseif ($row['KEY_STATUS'] === 'Y' && !empty($row['MILE_OUT'])) {
        $issuedCount++;
    } else {
        $pendingCount++;
    }
    // นับไมล์ทุกรายการที่มี TOTAL_MILE_GO
    if (!empty($row['TOTAL_MILE_GO'])) {
        $m = intval($row['TOTAL_MILE_GO']);
        if ($m > 0) $totalDistance += $m;
    }
}

// ??? 2. WEEKLY CHART DATA (last 7 days) ???
$weekStart = date('Y-m-d', strtotime('-6 days'));
$weekEnd = $today;

$sqlWeek = "SELECT t1.USED_DATE, COUNT(*) AS CNT
    FROM TRC_REQUEST_RECORD_NEW t1
    WHERE t1.APP_ID IN ('AP02', 'AP04')
    AND t1.USED_DATE BETWEEN :start_date AND :end_date
    AND t1.PLANT = :plant
    GROUP BY t1.USED_DATE
    ORDER BY t1.USED_DATE";

$rsWeek = oci_parse($conn, $sqlWeek);
oci_bind_by_name($rsWeek, ':start_date', $weekStart);
oci_bind_by_name($rsWeek, ':end_date', $weekEnd);
oci_bind_by_name($rsWeek, ':plant', $filterPlant);
oci_execute($rsWeek);

$weekDataMap = [];
while ($row = oci_fetch_array($rsWeek, OCI_ASSOC)) {
    $weekDataMap[$row['USED_DATE']] = intval($row['CNT']);
}

$thaiDaysShort = ['อา', 'จ', 'อ', 'พ', 'พฤ', 'ศ', 'ส'];
$weekLabels = [];
$weekData = [];
$currentDate = $weekStart;
while (strtotime($currentDate) <= strtotime($weekEnd)) {
    $dow = date('w', strtotime($currentDate));
    $day = date('d', strtotime($currentDate));
    $weekLabels[] = $thaiDaysShort[$dow] . ' ' . $day;
    $weekData[] = isset($weekDataMap[$currentDate]) ? $weekDataMap[$currentDate] : 0;
    $currentDate = date('Y-m-d', strtotime($currentDate . ' +1 day'));
}

// ??? 3. MONTHLY CHART DATA ???
$monthStart = date('Y-m-01');
$monthEnd = date('Y-m-t');

$sqlMonth = "SELECT SUBSTR(t1.USED_DATE, 9, 2) AS DAY_NUM, COUNT(*) AS CNT
    FROM TRC_REQUEST_RECORD_NEW t1
    WHERE t1.APP_ID IN ('AP02', 'AP04')
    AND t1.USED_DATE BETWEEN :start_date AND :end_date
    AND t1.PLANT = :plant
    GROUP BY SUBSTR(t1.USED_DATE, 9, 2)
    ORDER BY DAY_NUM";

$rsMonth = oci_parse($conn, $sqlMonth);
oci_bind_by_name($rsMonth, ':start_date', $monthStart);
oci_bind_by_name($rsMonth, ':end_date', $monthEnd);
oci_bind_by_name($rsMonth, ':plant', $filterPlant);
oci_execute($rsMonth);

$monthDataMap = [];
while ($row = oci_fetch_array($rsMonth, OCI_ASSOC)) {
    $monthDataMap[intval($row['DAY_NUM'])] = intval($row['CNT']);
}

$daysInMonth = intval(date('t'));
$monthLabels = [];
$monthData = [];
for ($d = 1; $d <= $daysInMonth; $d++) {
    $monthLabels[] = $d;
    $monthData[] = isset($monthDataMap[$d]) ? $monthDataMap[$d] : 0;
}

// ??? 4. RECENT BOOKINGS (latest 20) ???
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
    AND t1.USED_DATE = :today
    AND t1.PLANT = :plant
    ORDER BY t1.USED_TIME ASC
) WHERE ROWNUM <= 50";

$rsRecent = oci_parse($conn, $sqlRecent);
oci_bind_by_name($rsRecent, ':today', $today);
oci_bind_by_name($rsRecent, ':plant', $filterPlant);
oci_execute($rsRecent);

$recentBookings = [];
while ($row = oci_fetch_array($rsRecent, OCI_ASSOC + OCI_RETURN_NULLS)) {
    $ks = $row['KEY_STATUS'] ?? '';
    $ap = $row['APP_ID'] ?? '';
    $carType = $row['CAR_TYPE'] ?? '';
    if (empty($carType)) $status = 'private';
    elseif ($ap === 'AP04') $status = 'closed';
    elseif ($ks === 'R') $status = 'returned';
    elseif ($ks === 'Y' && !empty($row['MILE_OUT'])) $status = 'issued';
    else $status = 'pending';

    $statusLabels = [
        'pending' => 'รอจ่ายกุญแจ',
        'issued' => 'กำลังใช้งาน',
        'returned' => 'เสร็จสิ้น',
        'closed' => 'ปิดใบจองแล้ว',
        'private' => 'รถส่วนตัว'
    ];

    $recentBookings[] = [
        'docId' => $row['DOC_ID'],
        'carLicense' => @iconv('TIS-620', 'UTF-8', $row['CAR_LICENSE'] ?? ''),
        'userName' => @iconv('TIS-620', 'UTF-8', $row['USER_NAME'] ?? ''),
        'usedTime' => $row['USED_TIME'] ?? '',
        'backTime' => $row['BACK_TIME'] ?? '',
        'destination' => @iconv('TIS-620', 'UTF-8', $row['REQUEST_GOTO'] ?? ''),
        'province' => @iconv('TIS-620', 'UTF-8', $row['REQUEST_PROVINCE'] ?? ''),
        'status' => $status,
        'statusLabel' => $statusLabels[$status],
        'totalMile' => !empty($row['TOTAL_MILE_GO']) ? intval($row['TOTAL_MILE_GO']) : 0,
    ];
}

// ??? 5. CAR STATUS SUMMARY ???
$sqlCars = "SELECT t1.CAR_LICENSE, t1.CAR_GROUP, t1.CAR_STATUS, t1.CAR_BAND, t1.CAR_MODEL, t1.CAR_TYPE,
    (SELECT COUNT(*) FROM TRC_REQUEST_RECORD_NEW r 
     WHERE r.CAR_LICENSE = t1.CAR_LICENSE 
     AND r.APP_ID = 'AP02' 
     AND r.KEY_STATUS = 'Y'
     AND r.USED_DATE = :today) AS KEY_ISSUED,
    (SELECT COUNT(*) FROM TRC_REQUEST_RECORD_NEW r2
     WHERE r2.CAR_LICENSE = t1.CAR_LICENSE 
     AND r2.APP_ID = 'AP02' 
     AND r2.KEY_STATUS = 'R'
     AND r2.USED_DATE = :today2) AS KEY_RETURNED,
    (SELECT COUNT(*) FROM TRC_REQUEST_RECORD_NEW r3
     WHERE r3.CAR_LICENSE = t1.CAR_LICENSE 
     AND r3.APP_ID IN ('AP02','AP04')
     AND r3.USED_DATE = :today3) AS HAS_BOOKING
    FROM TRC_CAR_NEW t1
    WHERE t1.CAR_PLANT = :plant
    AND t1.CAR_GROUP != '" . iconv('UTF-8', 'TIS-620', 'กรุณาเลือก') . "'
    ORDER BY t1.CAR_LICENSE";

$rsCars = oci_parse($conn, $sqlCars);
oci_bind_by_name($rsCars, ':today', $today);
oci_bind_by_name($rsCars, ':today2', $today);
oci_bind_by_name($rsCars, ':today3', $today);
oci_bind_by_name($rsCars, ':plant', $filterPlant);
oci_execute($rsCars);



$carStatuses = [];
$totalCars = 0;
$carsInUse = 0;
$carsAvailable = 0;
$carsMaintenance = 0;
$carsPendingClose = 0;

// TIS-620 keywords for repair status comparison
$kwMaint = @iconv('UTF-8', 'TIS-620', 'ส่งซ่อม');
$kwRepairWord = @iconv('UTF-8', 'TIS-620', 'ซ่อม');
$kwNotReadyWord = @iconv('UTF-8', 'TIS-620', 'ไม่พร้อม');

while ($row = oci_fetch_array($rsCars, OCI_ASSOC + OCI_RETURN_NULLS)) {
    $totalCars++;

    $carStatusRaw = trim($row['CAR_STATUS'] ?? '');
    $keyIssued = intval($row['KEY_ISSUED']) > 0;
    $keyReturned = intval($row['KEY_RETURNED']) > 0;
    $hasBooking = intval($row['HAS_BOOKING']) > 0;

    // Determine car condition: ready or maintenance
    $isRepair = false;
    if ($kwMaint && strpos($carStatusRaw, $kwMaint) !== false) {
        $isRepair = true;
    } elseif ($kwRepairWord && strpos($carStatusRaw, $kwRepairWord) !== false) {
        $isRepair = true;
    } elseif ($kwNotReadyWord && strpos($carStatusRaw, $kwNotReadyWord) !== false) {
        $isRepair = true;
    }

    // Availability: repair / in-use / pending-close / available
    if ($isRepair) {
        $availability = 'repair';
        $carsMaintenance++;
    } elseif ($keyIssued && !$keyReturned) {
        $availability = 'in-use';
        $carsInUse++;
    } elseif ($keyReturned) {
        // Keys returned but APP_ID still 'AP02' (not closed to AP04)
        $availability = 'pending-close';
        $carsPendingClose++;
    } else {
        $availability = 'available';
        $carsAvailable++;
    }

    $carStatuses[] = [
        'license' => @iconv('TIS-620', 'UTF-8', $row['CAR_LICENSE'] ?? ''),
        'type' => @iconv('TIS-620', 'UTF-8', $row['CAR_GROUP'] ?? ''),
        'brand' => @iconv('TIS-620', 'UTF-8', $row['CAR_BAND'] ?? ''),
        'model' => @iconv('TIS-620', 'UTF-8', $row['CAR_MODEL'] ?? ''),
        'carType' => @iconv('TIS-620', 'UTF-8', $row['CAR_TYPE'] ?? ''),
        'carStatus' => @iconv('TIS-620', 'UTF-8', $row['CAR_STATUS'] ?? ''),
        'availability' => $availability,
        'hasBooking' => $hasBooking,
        'inUse' => ($availability === 'in-use')
    ];
}

// ═══ 5B. WEEKLY BOOKING BY CAR TYPE (line chart) ═══
$sqlCarTypeTrend = "SELECT t1.USED_DATE, 
    NVL(t4.CAR_GROUP, 'PRIVATE') AS CAR_CAT,
    COUNT(*) AS CNT
    FROM TRC_REQUEST_RECORD_NEW t1
    LEFT JOIN (
        SELECT CAR_LICENSE, CAR_GROUP,
               ROW_NUMBER() OVER (PARTITION BY CAR_LICENSE ORDER BY CAR_ID DESC) AS RN
        FROM TRC_CAR_NEW
    ) t4 ON t4.CAR_LICENSE = t1.CAR_LICENSE AND t4.RN = 1
    WHERE t1.APP_ID IN ('AP02', 'AP04')
    AND t1.USED_DATE BETWEEN :start_date AND :end_date
    AND t1.PLANT = :plant
    GROUP BY t1.USED_DATE, NVL(t4.CAR_GROUP, 'PRIVATE')
    ORDER BY t1.USED_DATE, CAR_CAT";

$rsCarTypeTrend = oci_parse($conn, $sqlCarTypeTrend);
oci_bind_by_name($rsCarTypeTrend, ':start_date', $weekStart);
oci_bind_by_name($rsCarTypeTrend, ':end_date', $weekEnd);
oci_bind_by_name($rsCarTypeTrend, ':plant', $filterPlant);
oci_execute($rsCarTypeTrend);

// Collect raw data: [date][category] = count
$catTrendRaw = [];
$allCategories = [];
while ($row = oci_fetch_array($rsCarTypeTrend, OCI_ASSOC + OCI_RETURN_NULLS)) {
    $date = $row['USED_DATE'];
    $cat = $row['CAR_CAT'];
    $catLabel = ($cat === 'PRIVATE') ? 'รถส่วนตัว' : @iconv('TIS-620', 'UTF-8', $cat);
    if (empty(trim($catLabel))) $catLabel = 'อื่นๆ';
    $catTrendRaw[$date][$catLabel] = intval($row['CNT']);
    $allCategories[$catLabel] = true;
}

// Build datasets for each category over 7 days
$catDatasets = [];
foreach (array_keys($allCategories) as $catName) {
    $dataArr = [];
    $tmpDate = $weekStart;
    while (strtotime($tmpDate) <= strtotime($weekEnd)) {
        $dataArr[] = isset($catTrendRaw[$tmpDate][$catName]) ? $catTrendRaw[$tmpDate][$catName] : 0;
        $tmpDate = date('Y-m-d', strtotime($tmpDate . ' +1 day'));
    }
    $catDatasets[] = [
        'name' => $catName,
        'data' => $dataArr
    ];
}

// ═══ 5C. CAR BODY TYPE COUNT (เก๋ง, กระบะ, SUV...) ═══
$sqlCarBody = "SELECT CAR_TYPE, COUNT(*) AS CNT
    FROM TRC_CAR_NEW
    WHERE CAR_PLANT = :plant
    AND CAR_GROUP != '" . iconv('UTF-8', 'TIS-620', 'กรุณาเลือก') . "'
    AND CAR_TYPE IS NOT NULL
    GROUP BY CAR_TYPE
    ORDER BY CNT DESC";

$rsCarBody = oci_parse($conn, $sqlCarBody);
oci_bind_by_name($rsCarBody, ':plant', $filterPlant);
oci_execute($rsCarBody);

$carBodyLabels = [];
$carBodyCounts = [];
while ($row = oci_fetch_array($rsCarBody, OCI_ASSOC + OCI_RETURN_NULLS)) {
    $label = @iconv('TIS-620', 'UTF-8', $row['CAR_TYPE'] ?? '');
    if (!empty(trim($label))) {
        $carBodyLabels[] = $label;
        $carBodyCounts[] = intval($row['CNT']);
    }
}

// ??? 6. TOP 10 DEPARTMENTS (by booking count) ???
$sqlDept = "SELECT t2.USERS_DEPARTMENT, COUNT(*) AS CNT
    FROM TRC_REQUEST_RECORD_NEW t1
    LEFT JOIN WEB.USERS t2 ON t2.USERS_EMPCODE = t1.USER_ID
    WHERE t1.APP_ID IN ('AP02', 'AP04')
    AND t1.USED_DATE = :today
    AND t1.PLANT = :plant
    AND t2.USERS_DEPARTMENT IS NOT NULL
    GROUP BY t2.USERS_DEPARTMENT
    ORDER BY CNT DESC";

$rsDept = oci_parse($conn, $sqlDept);
oci_bind_by_name($rsDept, ':today', $today);
oci_bind_by_name($rsDept, ':plant', $filterPlant);
oci_execute($rsDept);

$topDepartments = [];
$deptCount = 0;
while ($row = oci_fetch_array($rsDept, OCI_ASSOC + OCI_RETURN_NULLS)) {
    if ($deptCount >= 10) break;
    $topDepartments[] = [
        'name' => @iconv('TIS-620', 'UTF-8', $row['USERS_DEPARTMENT'] ?? ''),
        'count' => intval($row['CNT'])
    ];
    $deptCount++;
}

// ??? 6.5. TOP 10 LOCATIONS (by booking count, daily) ???
$sqlLoc = "SELECT t1.REQUEST_GOTO, COUNT(*) AS CNT
    FROM TRC_REQUEST_RECORD_NEW t1
    WHERE t1.APP_ID IN ('AP02', 'AP04')
    AND t1.USED_DATE = :today
    AND t1.PLANT = :plant
    AND t1.REQUEST_GOTO IS NOT NULL
    GROUP BY t1.REQUEST_GOTO
    ORDER BY CNT DESC";

$rsLoc = oci_parse($conn, $sqlLoc);
oci_bind_by_name($rsLoc, ':today', $today);
oci_bind_by_name($rsLoc, ':plant', $filterPlant);
oci_execute($rsLoc);

$topLocations = [];
$locCount = 0;
while ($row = oci_fetch_array($rsLoc, OCI_ASSOC + OCI_RETURN_NULLS)) {
    if ($locCount >= 10) break;
    $locName = @iconv('TIS-620', 'UTF-8', $row['REQUEST_GOTO'] ?? '');
    if (trim($locName) === '') continue;
    $topLocations[] = [
        'name' => $locName,
        'count' => intval($row['CNT'])
    ];
    $locCount++;
}

// ═══ 6.6. TOP 10 DEPARTMENTS (MONTHLY) ═══
$monthStart = date('Y-m-01', strtotime($today));
$monthEnd = date('Y-m-t', strtotime($today));

$sqlDeptMonth = "SELECT t2.USERS_DEPARTMENT, COUNT(*) AS CNT
    FROM TRC_REQUEST_RECORD_NEW t1
    LEFT JOIN WEB.USERS t2 ON t2.USERS_EMPCODE = t1.USER_ID
    WHERE t1.APP_ID IN ('AP02', 'AP04')
    AND t1.USED_DATE BETWEEN :s AND :e
    AND t1.PLANT = :plant
    AND t2.USERS_DEPARTMENT IS NOT NULL
    GROUP BY t2.USERS_DEPARTMENT
    ORDER BY CNT DESC";

$rsDeptMonth = oci_parse($conn, $sqlDeptMonth);
oci_bind_by_name($rsDeptMonth, ':s', $monthStart);
oci_bind_by_name($rsDeptMonth, ':e', $monthEnd);
oci_bind_by_name($rsDeptMonth, ':plant', $filterPlant);
$okDeptMonth = @oci_execute($rsDeptMonth);

$topDeptMonthly = [];
if ($okDeptMonth) {
    $deptMCount = 0;
    while ($row = oci_fetch_array($rsDeptMonth, OCI_ASSOC + OCI_RETURN_NULLS)) {
        if ($deptMCount >= 10) break;
        $topDeptMonthly[] = [
            'name' => @iconv('TIS-620', 'UTF-8', $row['USERS_DEPARTMENT'] ?? ''),
            'count' => intval($row['CNT'])
        ];
        $deptMCount++;
    }
}

// ═══ 6.7. TOP 10 LOCATIONS (MONTHLY) ═══
$sqlLocMonth = "SELECT REQUEST_GOTO AS LOC_NAME, COUNT(*) AS CNT
    FROM TRC_REQUEST_RECORD_NEW
    WHERE APP_ID IN ('AP02', 'AP04')
    AND USED_DATE BETWEEN :s AND :e
    AND PLANT = :plant
    AND REQUEST_GOTO IS NOT NULL
    GROUP BY REQUEST_GOTO
    ORDER BY CNT DESC";

$rsLocMonth = oci_parse($conn, $sqlLocMonth);
oci_bind_by_name($rsLocMonth, ':s', $monthStart);
oci_bind_by_name($rsLocMonth, ':e', $monthEnd);
oci_bind_by_name($rsLocMonth, ':plant', $filterPlant);
$okLocMonth = @oci_execute($rsLocMonth);

$topLocMonthly = [];
if ($okLocMonth) {
    $locMCount = 0;
    while ($row = oci_fetch_array($rsLocMonth, OCI_ASSOC + OCI_RETURN_NULLS)) {
        if ($locMCount >= 10) break;
        $locName = @iconv('TIS-620', 'UTF-8', $row['LOC_NAME'] ?? '');
        if (trim($locName) === '') continue;
        $topLocMonthly[] = [
            'name' => $locName,
            'count' => intval($row['CNT'])
        ];
        $locMCount++;
    }
}

// ═══ 7. TOP 10 KM BY DEPARTMENT ═══
$sqlKm = "SELECT u.USERS_DEPARTMENT AS DEPT_NAME, SUM(sub.TOTAL_MILE_GO) AS TOTAL_KM
    FROM (
        SELECT DISTINCT t1.DOC_ID, t1.USER_ID,
            CASE WHEN t3.TOTAL_MILE_GO > 0 THEN t3.TOTAL_MILE_GO ELSE 0 END AS TOTAL_MILE_GO
        FROM TRC_REQUEST_RECORD_NEW t1
        LEFT JOIN TRC_CAR_APPROVE_NEW t3 ON t3.DOC_ID = t1.DOC_ID
        WHERE t1.APP_ID IN ('AP02', 'AP04')
        AND t1.USED_DATE BETWEEN :km_start AND :km_end
        AND t1.PLANT = :plant
    ) sub
    LEFT JOIN WEB.USERS u ON u.USERS_EMPCODE = sub.USER_ID
    WHERE u.USERS_DEPARTMENT IS NOT NULL
    GROUP BY u.USERS_DEPARTMENT
    ORDER BY TOTAL_KM DESC";

$rsKm = oci_parse($conn, $sqlKm);
oci_bind_by_name($rsKm, ':km_start', $monthStart);
oci_bind_by_name($rsKm, ':km_end', $monthEnd);
oci_bind_by_name($rsKm, ':plant', $filterPlant);
oci_execute($rsKm);

$topKmDepts = [];
$kmCount = 0;
while ($row = oci_fetch_array($rsKm, OCI_ASSOC + OCI_RETURN_NULLS)) {
    if ($kmCount >= 10) break;
    $totalKm = floatval($row['TOTAL_KM']);
    if ($totalKm > 0) {
        $topKmDepts[] = [
            'name' => @iconv('TIS-620', 'UTF-8', $row['DEPT_NAME'] ?? ''),
            'km' => round($totalKm, 1)
        ];
        $kmCount++;
    }
}

// ??? AVAILABLE PLANTS ???
$sqlPlants = "SELECT DISTINCT PLANT FROM TRC_REQUEST_RECORD_NEW WHERE APP_ID IN ('AP02','AP04') AND PLANT IS NOT NULL ORDER BY PLANT";
$rsPlants = oci_parse($conn, $sqlPlants);
oci_execute($rsPlants);
$availablePlants = [];
while ($r = oci_fetch_array($rsPlants, OCI_ASSOC)) {
    $pid = $r['PLANT'];
    $availablePlants[] = [
        'id' => $pid,
        'name' => isset($plantNames[$pid]) ? $plantNames[$pid] : $pid
    ];
}

// ??? Thai date ??? (based on queried date, not necessarily today)
$thaiMonths = ['', 'มกราคม', 'กุมภาพันธ์', 'มีนาคม', 'เมษายน', 'พฤษภาคม', 'มิถุนายน', 'กรกฎาคม', 'สิงหาคม', 'กันยายน', 'ตุลาคม', 'พฤศจิกายน', 'ธันวาคม'];
$thaiDaysFull = ['อาทิตย์', 'จันทร์', 'อังคาร', 'พุธ', 'พฤหัสบดี', 'ศุกร์', 'เสาร์'];
$ts = strtotime($today);
$thaiDate = "วัน{$thaiDaysFull[date('w',$ts)]}ที่ " . date('j', $ts) . " {$thaiMonths[intval(date('n',$ts))]} " . (date('Y', $ts) + 543);

// ??? Build Response ???
$response = [
    'success' => true,
    'timestamp' => date('Y-m-d H:i:s'),
    'thaiDate' => $thaiDate,
    'queryDate' => $today,
    'isToday' => $isToday,
    'plant' => $filterPlant,
    'plantName' => $plantName,
    'availablePlants' => $availablePlants,
    'stats' => [
        'totalBookings' => $totalBookings,
        'pendingCount' => $pendingCount,
        'issuedCount' => $issuedCount,
        'returnedCount' => $returnedCount,
        'closedCount' => $closedCount,
        'privateCount' => $privateCount,
        'totalDistance' => $totalDistance,
        'totalDistanceFormatted' => number_format($totalDistance),
        'totalCars' => $totalCars,
        'carsInUse' => $carsInUse,
        'carsAvailable' => $carsAvailable,
        'carsMaintenance' => $carsMaintenance,
        'carsPendingClose' => $carsPendingClose,
    ],
    'weekChart' => [
        'labels' => $weekLabels,
        'data' => $weekData
    ],
    'monthChart' => [
        'labels' => $monthLabels,
        'data' => $monthData
    ],
    'recentBookings' => $recentBookings,
    'carStatuses' => $carStatuses,
    'topDepartments' => $topDepartments,
    'topLocations' => $topLocations,
    'topDeptMonthly' => $topDeptMonthly,
    'topLocMonthly' => $topLocMonthly,
    'topKmDepts' => $topKmDepts,
    'carTypeChart' => [
        'labels' => $weekLabels,
        'datasets' => $catDatasets
    ],
    'carBodyChart' => [
        'labels' => $carBodyLabels,
        'data' => $carBodyCounts
    ],
];

echo json_encode($response, JSON_UNESCAPED_UNICODE);
oci_close($conn);
