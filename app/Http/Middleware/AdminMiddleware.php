<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     * ตรวจสอบ session admin login
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!session()->has('admin')) {
            return redirect()->route('login')->with('error', 'กรุณาเข้าสู่ระบบ');
        }

        return $next($request);
    }
}
