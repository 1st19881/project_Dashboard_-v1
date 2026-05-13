<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EmployeeMiddleware
{
    /**
     * Handle an incoming request.
     * ตรวจสอบ session employee login
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!session()->has('employee')) {
            return redirect()->route('employee.login')->with('error', 'กรุณาเข้าสู่ระบบ');
        }

        return $next($request);
    }
}
