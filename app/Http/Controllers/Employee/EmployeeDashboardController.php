<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

/**
 * EmployeeDashboardController — หน้า Dashboard ของพนักงาน
 */
class EmployeeDashboardController extends Controller
{
    /** แสดง Dashboard พนักงาน */
    public function index()
    {
        return view('employee.dashboard');
    }
}
