<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * EmployeeLoginController — Login/Logout ของพนักงาน
 * ใช้ตาราง INTRA.USERS บน oracle_intra (192.1.1.142)
 * จัดการ TIS-620→UTF-8 encoding + สร้าง session ตามโครงสร้างระบบเดิม
 */
class EmployeeLoginController extends Controller
{
    /** แมป cost center prefix → plant number */
    private const PLANT_MAP = [
        '10' => '1100',
        '11' => '1101',
        '20' => '1200',
        '21' => '1201',
        '22' => '1202',
        '23' => '1203',
        '30' => '1300',
        '40' => '1400',
    ];

    /** แสดงฟอร์ม Login — ถ้า login แล้ว redirect ไป Dashboard พนักงาน */
    public function showLoginForm()
    {
        if (session()->has('employee')) {
            return redirect()->route('employee.dashboard');
        }

        return view('employee.login');
    }

    /**
     * ตรวจสอบ Login — เทียบ username/password จาก INTRA.USERS
     * แปลง TIS-620→UTF-8, สร้าง plant_no จาก cost center, เก็บ session
     */
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $user = strtoupper(trim($request->username));
        $pass = $request->password;

        // Query เหมือนระบบเดิม: upper(USERS_USERNAME) = :user AND USERS_STATUS='1'
        $row = DB::connection('oracle_intra')
            ->table('INTRA.USERS')
            ->whereRaw("UPPER(USERS_USERNAME) = ?", [$user])
            ->where('USERS_STATUS', '1')
            ->first();

        if (!$row) {
            return back()
                ->withInput($request->only('username'))
                ->with('error', 'ชื่อผู้ใช้ไม่ถูกต้องหรือถูกระงับ');
        }

        // ตรวจ password แบบ plain-text (ตามระบบเดิม)
        if ($row->users_password !== $pass) {
            return back()
                ->withInput($request->only('username'))
                ->with('error', 'รหัสผ่านไม่ถูกต้อง');
        }

        // ── สร้าง plant_no จาก cost center ──
        $costCenter = $row->users_costcenter ?? '';
        $prefix = substr($costCenter, 0, 2);
        $plantNo = self::PLANT_MAP[$prefix] ?? '1100';

        // ── แปลง double-encoded TIS-620 → UTF-8 (ข้อมูลไทยจาก INTRA) ──
        $fnameTh = self::tis620toUtf8($row->users_fnameth ?? '');
        $lnameTh = self::tis620toUtf8($row->users_lnameth ?? '');
        $fnameEn = $row->users_fname ?? '';
        $lnameEn = $row->users_lname ?? '';
        $department = self::tis620toUtf8($row->users_department ?? '');
        $positionText = self::tis620toUtf8($row->users_position ?? '');

        $fullname = trim($fnameTh . ' ' . $lnameTh);
        if (empty($fullname)) {
            $fullname = trim($fnameEn . ' ' . $lnameEn);
        }

        // ── Position (pad 4 digits ตามเดิม) ──
        $position = str_pad($row->users_position ?? '', 4, '0', STR_PAD_LEFT);

        // ── UserMenuPerm (decode JSON ตามเดิม) ──
        $menuPerm = [];
        $menuPerRaw = $row->users_menuper ?? '';
        if (!empty($menuPerRaw) && $menuPerRaw !== 'null') {
            $menuPerm = json_decode($menuPerRaw, true) ?? [];
        }

        // ══════════════════════════════════════════════
        // สร้าง session ตามโครงสร้างระบบเดิม
        // ══════════════════════════════════════════════
        session([
            'employee' => [
                // ── perm array (Sesession_User) ──
                'SSID'         => session()->getId(),
                'VSID'         => md5(time() . rand(0, 999)),
                'UserID'       => $row->users_id,
                'User_Code'    => $row->users_empcode,
                'Fullname'     => $fullname,
                'Status'       => $row->users_group,
                'Usersite'     => $row->users_siteid,
                'CodComp'      => $row->users_codecomp,
                'Department'   => $department,
                'CostCenter'   => $row->users_costcenter,
                'Position'     => $position,
                'plant_no'     => $plantNo,
                'UserMenuPerm' => $menuPerm,

                // ── ค่า session แยก (ตามระบบเดิม) ──
                'user_code'       => $row->users_empcode,
                'user_name'       => $fnameTh,
                'codcomp'         => $row->users_codecomp,
                'department_code' => $row->users_department,
                'position_code'   => $position,
                'cost_center'     => $row->users_costcenter,
                'dept_code'       => $row->users_department,

                // ── ข้อมูลเพิ่มเติมสำหรับ UI ──
                'USERS_USERNAME'   => $row->users_username,
                'USERS_FNAME'      => $fnameEn,
                'USERS_LNAME'      => $lnameEn,
                'USERS_FNAMETH'    => $fnameTh,
                'USERS_LNAMETH'    => $lnameTh,
                'USERS_EMPCODE'    => $row->users_empcode,
                'USERS_EMAIL'      => $row->users_email,
                'USERS_DEPARTMENT' => $department,
                'USERS_POSITION'   => $positionText,
            ],
        ]);

        return redirect()->route('employee.dashboard')
                         ->with('success', 'เข้าสู่ระบบสำเร็จ');
    }

    /** Logout — ลบ session('employee') แล้ว redirect กลับหน้า Login */
    public function logout()
    {
        session()->forget('employee');

        return redirect()->route('employee.login')
                         ->with('success', 'ออกจากระบบสำเร็จ');
    }

    /**
     * แปลง TIS-620 → UTF-8
     * 
     * Oracle INTRA (charset=WE8DEC) ส่ง raw TIS-620 bytes ออกมาตรงๆ
     * แค่ convert TIS-620 → UTF-8 ขั้นตอนเดียว
     */
    public static function tis620toUtf8(?string $str): string
    {
        if (empty($str)) {
            return '';
        }

        // ถ้ามีอักษรไทย UTF-8 ถูกต้องอยู่แล้ว ไม่ต้องแปลง
        if (preg_match('/[\x{0E00}-\x{0E7F}]/u', $str)) {
            return $str;
        }

        // แปลง TIS-620 → UTF-8 ตรงๆ
        $converted = @iconv('TIS-620', 'UTF-8//IGNORE', $str);
        return ($converted !== false && !empty($converted)) ? $converted : $str;
    }
}

