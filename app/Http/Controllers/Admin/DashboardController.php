<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

/**
 * DashboardController — หน้า Dashboard หลักของ Admin
 */
class DashboardController extends Controller
{
    /**
     * แสดง Dashboard
     */
    public function index(Request $request)
    {
        return view('admin.dashboard');
    }
}
