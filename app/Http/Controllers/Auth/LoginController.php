<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * LoginController — จัดการ Login/Logout ของ Admin
 * ตรวจสอบ username/password จาก HRMSIT.KPI_USERS, เก็บข้อมูลใน session('admin')
 */
class LoginController extends Controller
{
    /** แสดงฟอร์ม Login — ถ้า login แล้ว redirect ไป Dashboard */
    public function showLoginForm()
    {
        if (session()->has('admin')) {
            return redirect()->route('admin.dashboard');
        }

        return view('auth.login');
    }

    /** ตรวจสอบ Login — เทียบ username/password + สร้าง session */
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $user = DB::connection('oracle')
            ->table('HRMSIT.KPI_USERS')
            ->where('USERNAME', $request->username)
            ->where('PASSWORD', $request->password)
            ->first();

        if (!$user) {
            return back()
                ->withInput($request->only('username'))
                ->with('error', 'ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง');
        }

        // แปลง TIS-620 → UTF-8 สำหรับข้อมูลไทย
        $nameTh = self::tis620toUtf8($user->nameth ?? '');
        $dept   = self::tis620toUtf8($user->dept ?? '');

        // Store user data in session
        session([
            'admin' => [
                'USER_ID'    => $user->user_id,
                'USERNAME'   => $user->username,
                'NAMETH'     => $nameTh,
                'DEPT'       => $dept,
                'CODEMPID'   => $user->codempid,
                'USER_LEVEL' => $user->user_level,
            ],
        ]);

        return redirect()->route('admin.dashboard')
                         ->with('success', 'เข้าสู่ระบบสำเร็จ');
    }

    /** Logout — ลบ session('admin') แล้ว redirect กลับหน้า Login */
    public function logout()
    {
        session()->forget('admin');
        session()->flush();

        return redirect()->route('login')
                         ->with('success', 'ออกจากระบบสำเร็จ');
    }

    /**
     * แปลง TIS-620 → UTF-8
     *
     * Oracle HRMS (charset=TH8TISASCII) ส่ง raw TIS-620 bytes ออกมาตรงๆ
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
