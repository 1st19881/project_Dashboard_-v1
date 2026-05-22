<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Employee\EmployeeLoginController;
use App\Http\Controllers\Employee\EmployeeDashboardController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Response;

/*
|--------------------------------------------------------------------------
| Static Asset Route (IIS workaround)
|--------------------------------------------------------------------------
*/

Route::get('css/{file}', function ($file) {
    $path = public_path('css/' . $file);
    if (!file_exists($path)) abort(404);
    return Response::file($path, ['Content-Type' => 'text/css']);
})->where('file', '.*');

Route::get('js/{file}', function ($file) {
    $path = public_path('js/' . $file);
    if (!file_exists($path)) abort(404);
    $mime = str_ends_with($file, '.map') ? 'application/json' : 'application/javascript';
    return Response::file($path, ['Content-Type' => $mime]);
})->where('file', '.*');

Route::get('favicon.ico', function () {
    $path = public_path('favicon.ico');
    if (!file_exists($path)) abort(404);
    return Response::file($path, ['Content-Type' => 'image/x-icon']);
});

/*
|--------------------------------------------------------------------------
| Admin Auth Routes (Login / Logout)
|--------------------------------------------------------------------------
*/

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.submit');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

/*
|--------------------------------------------------------------------------
| Admin Routes (Protected by admin middleware)
|--------------------------------------------------------------------------
*/

Route::middleware('admin')->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // เพิ่ม routes ของโปรเจคใหม่ที่นี่
    // Route::get('/example', [ExampleController::class, 'index'])->name('example.index');
});


/*
|--------------------------------------------------------------------------
| Employee Auth Routes (Login / Logout)
|--------------------------------------------------------------------------
*/

Route::get('/employee/login', [EmployeeLoginController::class, 'showLoginForm'])->name('employee.login');
Route::post('/employee/login', [EmployeeLoginController::class, 'login'])->name('employee.login.submit');
Route::post('/employee/logout', [EmployeeLoginController::class, 'logout'])->name('employee.logout');


/*
|--------------------------------------------------------------------------
| Employee Routes (Protected by employee middleware)
|--------------------------------------------------------------------------
*/

Route::middleware('employee')->prefix('employee')->name('employee.')->group(function () {
    Route::get('/', [EmployeeDashboardController::class, 'index'])->name('dashboard');

    // เพิ่ม routes ของโปรเจคใหม่ที่นี่
    // Route::get('/example', [ExampleController::class, 'index'])->name('example.index');
});


/*
|--------------------------------------------------------------------------
| Database Test Routes (ลบออกเมื่อขึ้น Production)
|--------------------------------------------------------------------------
*/

// Route::prefix('test')->group(function () {

//     // ทดสอบ Connection ทั้ง 2 ตัว
//     Route::get('/db', function () {
//         $results = [];

//         // === Test 1: Oracle HRMS (192.1.1.240) ===
//         try {
//             $pdo = \Illuminate\Support\Facades\DB::connection('oracle')->getPdo();
//             $rows = \Illuminate\Support\Facades\DB::connection('oracle')
//                 ->select("SELECT 'HRMS OK' AS STATUS, SYSDATE AS SERVER_TIME FROM DUAL");
//             $results['oracle_hrms'] = [
//                 'status'  => '✅ Connected',
//                 'host'    => config('database.connections.oracle.host'),
//                 'service' => config('database.connections.oracle.service_name'),
//                 'charset' => config('database.connections.oracle.charset'),
//                 'data'    => $rows,
//             ];
//         } catch (\Exception $e) {
//             $results['oracle_hrms'] = [
//                 'status' => '❌ Failed',
//                 'error'  => $e->getMessage(),
//             ];
//         }

//         // === Test 2: Oracle INTRA (192.1.1.241) ===
//         try {
//             $pdo = \Illuminate\Support\Facades\DB::connection('oracle_intra')->getPdo();
//             $rows = \Illuminate\Support\Facades\DB::connection('oracle_intra')
//                 ->select("SELECT 'INTRA OK' AS STATUS, SYSDATE AS SERVER_TIME FROM DUAL");
//             $results['oracle_intra'] = [
//                 'status'  => '✅ Connected',
//                 'host'    => config('database.connections.oracle_intra.host'),
//                 'service' => config('database.connections.oracle_intra.service_name'),
//                 'charset' => config('database.connections.oracle_intra.charset'),
//                 'data'    => $rows,
//             ];
//         } catch (\Exception $e) {
//             $results['oracle_intra'] = [
//                 'status' => '❌ Failed',
//                 'error'  => $e->getMessage(),
//             ];
//         }

//         return response()->json($results, 200, [], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
//     });

//     // ทดสอบดู Tables ใน schema (HRMS)
//     Route::get('/db/tables', function () {
//         try {
//             $tables = \Illuminate\Support\Facades\DB::connection('oracle')
//                 ->select("SELECT TABLE_NAME FROM USER_TABLES ORDER BY TABLE_NAME");
//             return response()->json([
//                 'connection' => 'oracle (HRMS)',
//                 'count'      => count($tables),
//                 'tables'     => $tables,
//             ], 200, [], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
//         } catch (\Exception $e) {
//             return response()->json(['error' => $e->getMessage()], 500);
//         }
//     });

//     // ทดสอบดู Tables ใน schema (INTRA)
//     Route::get('/db/tables-intra', function () {
//         try {
//             $tables = \Illuminate\Support\Facades\DB::connection('oracle_intra')
//                 ->select("SELECT TABLE_NAME FROM USER_TABLES ORDER BY TABLE_NAME");
//             return response()->json([
//                 'connection' => 'oracle_intra (SAGDB)',
//                 'count'      => count($tables),
//                 'tables'     => $tables,
//             ], 200, [], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
//         } catch (\Exception $e) {
//             return response()->json(['error' => $e->getMessage()], 500);
//         }
//     });

// });

/*
|--------------------------------------------------------------------------
| Default Redirect
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return redirect()->route('login');
});

/*
|--------------------------------------------------------------------------
| TV Dashboard Routes (Public for Display)
|--------------------------------------------------------------------------
*/
use App\Http\Controllers\Employee\TVDashboardController;

Route::prefix('tv-dashboard')->name('tv.dashboard.')->group(function () {
    Route::get('/', [TVDashboardController::class, 'index'])->name('index');
    Route::get('/data', [TVDashboardController::class, 'getData'])->name('data');
    Route::get('/doc-alerts', [TVDashboardController::class, 'getDocAlerts'])->name('doc-alerts');
});

